#!/bin/bash -l
## Resets various caches and configs

## NOTE: This script assumes it is in protected/scripts. If you move it then relative paths will not work!

# Find fuill folder path where this script is located, then find root folder
SOURCE="${BASH_SOURCE[0]}"
while [ -h "$SOURCE" ]; do # resolve $SOURCE until the file is no longer a symlink
    DIR="$(cd -P "$(dirname "$SOURCE")" && pwd)"
    SOURCE="$(readlink "$SOURCE")"
    [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE" # if $SOURCE was a relative symlink, we need to resolve it relative to the path where the symlink file was located
done
# Determine root folder for site - all relative paths will be built from here
SCRIPTDIR="$(cd -P "$(dirname "$SOURCE")" && pwd)"
WROOT="$(cd -P "$SCRIPTDIR/../../" && pwd)"

curuser="${LOGNAME:-root}"

# process commandline parameters
clearcahes=1
buildassests=1
migrate=1
showhelp=0
composer=1
nowarnmigrate=0
resetconfig=0
eyedraw=1
noperms=0
restart=0
forceperms=0

while [[ $# -gt 0 ]]; do
    p="$1"

    case $p in
    --no-clear | -nc)
        clearcahes=0
        ;;
    --no-assets | -na)
        buildassests=0
        ;;
    --no-migrate | --nomigrate | -nm)
        migrate=0
        ;;
    --no-eyedraw | -ned | -ne)
        eyedraw=0
        ;;
    --help)
        showhelp=1
        ;;
    --no-composer | --no-dependencies | -nd)
        composer=0
        ;;
    --no-permissions | -np)
        noperms=1
        ;;
    --force-perms)
        forceperms=1
        ;;
    --no-warn-migrate)
        nowarnmigrate=1
        ;;
    -fc | --reset-config)
        resetconfig=1
        ;;
    -r | --restart)
        restart=1
        ;;
    --no-compile) #reserved for future use
        ;;
    *)
        [ ! -z $p ] && echo "Unknown command line: $p" || :
        ;;
    esac

    shift # move to next parameter
done

# Show help text
if [ $showhelp = 1 ]; then
    echo ""
    echo "DESCRIPTION:"
    echo "Applies various fixes to make sure files in in the correct place, database is migrated, code is compiled, etc."
    echo ""
    echo "usage: $0 <branch> [--help] [--no-clear ] [--no-assets] [--no-migrate]"
    echo ""
    echo "COMMAND OPTIONS:"
    echo ""
    echo "  --help         : Show this help"
    echo "  --no-clear     : Do not clear caches"
    echo "  --no-assets    : Do not (re)build assets"
    echo "  --no-migrate   : Do not run db migrations"
    echo "  --no-dependencies  : Do not update composer or npm dependencies"
    echo "  --no-eyedraw   : Do not (re)import eyedraw configuration"
    echo "  --no-permissions : Do not reset file permissions"
    echo "  --force-perms  : force permission update, even if system thinks they're correct"
    echo "  --restart      : restart apache"
    echo ""
    exit 1
fi

if [ -f "$WROOT/.htaccess.sample" ]; then
    echo Renaming .htaccess file
    mv .htaccess.sample .htaccess
    sudo chown www-data:www-data .htaccess
fi

if [ -f "$WROOT/index.example.php" ]; then
    echo Renaming index.php file
    mv $WROOT/index.example.php $WROOT/index.php
    sudo chown www-data:www-data $WROOT/index.php
fi

if [ $resetconfig -eq 1 ]; then
    echo "
    ************************************************************************
    ************************************************************************
    ********* WARNING: Restoring defualt local configuration ...   *********
    *********                                                      *********
    *********        Any custom modules will be disabled           *********
    *********        You may need to reset your database           *********
    ************************************************************************
    ************************************************************************
    "
    rm -f "$WROOT/protected/config/local/common.php"
fi

if [ ! -f "$WROOT/protected/config/local/common.php" ]; then

    echo "WARNING: Copying sample configuration into local ..."
    sudo mkdir -p $WROOT/protected/config/local
    sudo cp -n $WROOT/protected/config/local.sample/common.sample.php $WROOT/protected/config/local/common.php
    sudo cp -n $WROOT/protected/config/local.sample/console.sample.php $WROOT/protected/config/local/console.php

fi

# update composer and npm dependencies. If OE_MODE is LIVE, then do not install dev components
if [ "$composer" == "1" ]; then

    [[ "${OE_MODE^^}" == "LIVE" ]] && {
        composerexta="--no-dev --optimize-autoloader"
        npmextra="--only=production"
        echo "************************** LIVE MODE ******************************"
    }
    [[ "${OE_MODE^^}" == "HOST" ]] && {
        composerexta="--ignore-platform-reqs"
        echo "-----= HOST MODE =----"
    }

    echo "DEPENDENCIES BEING EVALUATED..."

    echo "Installing/updating composer dependencies"
    sudo -E composer install --working-dir=$WROOT --no-plugins --no-scripts --prefer-dist --no-interaction $composerexta

    echo "Installing/updating npm dependencies"

    # have to cd, as not all npm commands support setting a working directory
    cd "$WROOT" || exit 1

    # If we've switched from dev to live, remove dev dependencies, else, just prune
    [ "${OE_MODE^^}" == "LIVE" ] && sudo -E npm prune --production || sudo -E npm prune

    rm package-lock.json >/dev/null 2>&1
    sudo -E npm update --no-save $npmextra

    # List current modules (will show any issues if above commands have been blocked by firewall).
    npm list --depth=0

    # return to original directory
    cd - >/dev/null 2>&1

    # Refresh git submodules
    git -C $WROOT submodule update --init

fi

# Automatically migrate up, unless --no-migrate parameter is given or the OE_NO_DB env variable is set
if [ "$migrate" == "1" ] && [ "$OE_NO_DB" != "true" ]; then
    echo ""
    echo "Migrating database..."
    if ! bash $SCRIPTDIR/oe-migrate.sh --quiet; then
        ## Quit if migrate failed
        exit 1
    fi
    echo ""
else
    if [ "$nowarnmigrate" = "0" ]; then
        echo "
Migrations were not run automaically. If you need to run the database migrations, run script $SCRIPTDIR/oe-migrate.sh
"
    fi
fi

# import eyedraw config
if [ "$eyedraw" = "1" ]; then
    printf "\n\nImporting eyedraw configuration...\n\n"
    php $WROOT/protected/yiic eyedrawconfigload --filename=$WROOT/protected/config/core/OE_ED_CONFIG.xml
fi

# Clear caches
if [ $clearcahes = 1 ]; then
    echo "Clearing caches..."
    sudo rm -rf $WROOT/protected/runtime/cache/* 2>/dev/null || :
    sudo mkdir -p $WROOT/protected/runtime/cache/events 2>/dev/null || :
    sudo chown www-data $WROOT/protected/runtime/cache/events 2>/dev/null
    sudo rm -rf $WROOT/assets/* 2>/dev/null || :
    echo ""
fi

# Fix permissions
if [ $noperms = 0 ]; then
    sudo gpasswd -a "$curuser" www-data # add current user to www-data group

    # We can ignore setting file permissions when running in a docker conatiner, as we always run as root
    if [[ "$DOCKER_CONTAINER" != "TRUE" ]] || [ $forceperms == 1 ]; then
        echo -e "\nResetting file permissions..."
        if [ $(stat -c '%U' $WROOT) != $curuser ] || [ $(stat -c '%G' $WROOT) != "www-data" ] || [ $forceperms == 1 ]; then
            echo "updaing ownership on $WROOT"
            sudo chown -R $curuser:www-data $WROOT
        else
            echo "ownership of $WROOT looks ok, skipping. Use --force-perms to override"
        fi

        folders774=($WROOT/protected/config/local $WROOT/assets/ $WROOT/protected/runtime $WROOT/protected/files)

        for i in "${folders774[@]}"; do
            echo "updating $i to 774..."
            sudo chmod -R 774 $i

        done

        if [ $(stat -c '%U' $WROOT/protected/runtime/testme) != $curuser ] || [ $(stat -c '%G' $WROOT/protected/runtime/testme) != "www-data" ] || [ $(stat -c %a "$WROOT/protected/runtime/testme") != 774 ]; then
            echo "setting sticky bit for protected/runtime"
            sudo chmod -R g+s $WROOT/protected/runtime
        fi

        if [ $(stat -c '%U' $WROOT/protected/files/testme) != $curuser ] || [ $(stat -c '%G' $WROOT/protected/files/testme) != "www-data" ] || [ $(stat -c %a "$WROOT/protected/files/testme") != 774 ]; then
            echo "setting sticky bit for protected/files"
            sudo chmod -R g+s $WROOT/protected/files
        fi

        # re-own composer and npm config folders in user home directory (sorts issues caused if sudo was used to composer/npm update previously)
        sudo chown -R $curuser ~/.config 2>/dev/null || :
        sudo chown -R $curuser ~/.composer 2>/dev/null || :
    fi
    #  update ImageMagick policy to allow PDFs
    sudo sed -i 's%<policy domain="coder" rights="none" pattern="PDF" />%<policy domain="coder" rights="read|write" pattern="PDF" />%' /etc/ImageMagick-6/policy.xml &>/dev/null
    sudo sed -i 's%<policy domain="coder" rights="none" pattern="PDF" />%<policy domain="coder" rights="read|write" pattern="PDF" />%' /etc/ImageMagick/policy.xml &>/dev/null
fi

if [ $buildassests = 1 ]; then
    echo "(re)building assets..."
    # use curl to ping the login page - forces php/apache to rebuild the assets directory
    curl -s http://localhost >/dev/null
fi

# Set some git properties

git -C $WROOT config core.fileMode false 2>/dev/null
# Set to cache password in memory (should only ask once per day or each reboot)
git config --global credential.helper 'cache --timeout=86400' 2>/dev/null

# restart apache
if [ "$restart" == "1" ]; then
    echo -e "\nrestarting apache..\n"
    sudo service apache2 restart &>/dev/null
fi

echo ""
echo "...Done"
echo ""
