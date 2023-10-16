#!/bin/bash -l
## Resets various caches and configs

## NOTE: This script assumes it is in protected/scripts. If you move it then relative paths will not work!

abort() {
    echo >&2 "
****************************
*** ABORTED DUE TO ERROR ***
****************************

The last return code was: $?

"
    date
    echo "An error occurred. Exiting..." >&2
    exit 1
}

trap 'abort' 0

set -e

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

# $USER is not always populated in docker containers, so get the running user if it is empty
function getuser() {
    if [ -z $USER ]; then
        USER=$(id -u -n)
    fi

    echo $USER
}
curuser=$(getuser)

# add current user to www-data group, this resolves a lot of local access issues
gpasswd -a "$curuser" www-data

# disable log to browser during fix, otherwise it causes extraneous trace output on the CLI
export LOG_TO_BROWSER=""

# check if git is installed - it won't be for production images, so we can skip some steps
if ! command -v git &>/dev/null; then
    echo "Git is not installed (This must be a production image). Some steps will be skipped..."
    gitinstalled=0
else
    gitinstalled=1
fi

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
        composerexta="--no-dev"
        npmextra="--only=production"
        echo "************************** LIVE MODE ******************************"
    }
    [[ "${OE_MODE^^}" == "HOST" ]] && {
        composerexta="--ignore-platform-reqs"
        echo "-----= HOST MODE =----"
    }

    echo "DEPENDENCIES BEING EVALUATED..."
    # delete the lock file first as we want to be sure it gets regenerated (note the lock file is no longer comitted to version control)
    rm "$WROOT/composer.lock" 2>/dev/null || :
    echo -e "\n** Installing/updating composer dependencies **"
    sudo -E composer install --working-dir=$WROOT --no-plugins --no-scripts --prefer-dist --no-interaction --optimize-autoloader $composerexta

    echo -e "\n** Installing/updating npm dependencies **"

    # have to cd, as not all npm commands support setting a working directory
    cd "$WROOT" || exit 1

    # If we've switched from dev to live, remove dev dependencies, else, just prune
    [ "${OE_MODE^^}" == "LIVE" ] && sudo -E npm prune --production || sudo -E npm prune

    rm package-lock.json >/dev/null 2>&1 || :
    sudo -E npm update --no-save $npmextra

    # List current modules (will show any issues if above commands have been blocked by firewall).
    npm list --depth=0

    # return to original directory
    cd - >/dev/null 2>&1

    # Refresh git submodules
    if [ "$gitinstalled" == "1" ]; then
        echo -e "\n** Refreshing git submodules **"
        git -C $WROOT submodule update --init
    fi

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
    sudo chown www-data:www-data $WROOT/protected/runtime/cache/events 2>/dev/null
    sudo rm -rf $WROOT/assets/* 2>/dev/null || :
    echo ""
    echo "clearing APC cache..."
    curl http://localhost/apc_clear.php || :
fi

# Fix permissions
if [ $noperms = 0 ]; then
    sudo gpasswd -a "$curuser" www-data # add current user to www-data group

    if [ -f /init_scripts/50-create-folders.sh ]; then
        bash /init_scripts/50-create-folders.sh
    else

        declare -a folders774=(
            "$WROOT/protected/config/local"
        )

        declare -a folders6777=(
            "$WROOT/cache"
            "$WROOT/assets"
            "$WROOT/protected/cache"
            "$WROOT/protected/cache/events"
            "$WROOT/protected/files"
            "$WROOT/protected/runtime"
            "$WROOT/protected/runtime/cache"
            "$WROOT/protected/migrations/data/freehand_templates"
        )

        declare -a folders755=(
            "$WROOT/protected/assets/newblue/"
        )

        declare -a foldersExclude=(
            "$WROOT/protected/assets/newblue/src"
            "$WROOT/protected/assets/newblue/node_scripts"
            "$WROOT/assets/newblue"
        )

        # This will set the correct permissions on any given folder (and all it's sub-folders) that does not meet the correct criteria
        # Folder name should be supplied as prameter 1
        # additional options are -uid=<user name to chown>; -gid=<group name to chown>; -octal=<octal file permissions to chmod>
        function set_perms() (

            user="www-data"
            group="www-data"
            octal=774

            PARAMS=()
            while [[ $# -gt 0 ]]; do
                p="$1"

                case $p in
                -uid*) # Set suffix - ignore if .
                    user=${1#*=}
                    ;;
                -gid*)
                    group=${1#*=}
                    ;;
                -octal*)
                    octal=${1#*=}
                    ;;
                *) # add everything else to the params array for processing in the next section
                    PARAMS+=("$1")
                    ;;
                esac
                shift
            done
            set -- "${PARAMS[@]}" # restore positional parameters

            # If no sticky / uid / gid bits are specified, make sure we unset any existing settings by adding 00 in front
            [ ${#octal} -le 3 ] && choctal="00$octal" || choctal=$octal

            echo "Setting up permissions for $1 (as $octal, $user:$group)"

            mkdir -p "$i" 2>/dev/null || :
            ## Fix permissions for root folder
            chown -L "$user":www-data "$1"
            chmod "$choctal" "$1"

            # Exclude any specific folders from the search
            unset exclude
            exclude=""
            for i in "${foldersExclude[@]}"; do
                exclude+=" -not \( -path '$i' -prune \)"
            done

            # Loop through each sub-folder individually and recursively change permissions.
            # We use a loop to improve performance on large folder structures (e.g, protected/files),
            # where there are many thousands of files, but only a subset of the folders may have the wrong permissions
            eval "find -L \"$1\" -maxdepth 1 -mindepth 1 -type d ${exclude}" | while read -r folder; do

                if [ "$(stat -c '%U' $folder/)" != $user ] || [ "$(stat -c '%G' $folder)" != "$group" ]; then
                    sudo chown -RL "$user":"$group" "$folder" || :
                    echo "Modified ownership on $folder to; OWNER: $user, GROUP: $group"
                fi
                if [ "$(stat -c "%a" $folder/)" != "$octal" ]; then
                    [ ${#octal} -le 3 ] && choctal="00$octal" || choctal=$octal

                    sudo chmod -R $choctal "$folder" || :
                    echo "Modified permissions on $folder to $choctal"
                fi
            done
        )

        # loop through the list of folders to set permission to 774
        for i in "${folders774[@]}"; do
            set_perms "$i" -octal=774
        done

        # loop through the list of folders to set permission to 777
        for i in "${folders6777[@]}"; do
            set_perms "$i" -octal=6777
        done

        # loop through the list of folders to set permission to 777
        for i in "${folders755[@]}"; do
            set_perms "$i" -octal=755
        done

        # Any further arrays of folders could be added with different permission requirements...

        # A hack to stop the newblue submodule as being changed...
        # Will fail silently if git is not installed or the folder does not exist
        if [ "$gitinstalled" == "1" ]; then
            git -C $WROOT/protected/assets/newblue reset --hard >/dev/null 2>&1 || :
        fi

    fi

    # re-own composer and npm config folders in user home directory (sorts issues caused if sudo was used to composer/npm update previously)
    sudo chown -RL $curuser ~/.config 2>/dev/null || :
    sudo chown -RL $curuser ~/.composer 2>/dev/null || :

    #  update ImageMagick policy to allow PDFs
    sudo sed -i 's%<policy domain="coder" rights="none" pattern="PDF" />%<policy domain="coder" rights="read|write" pattern="PDF" />%' /etc/ImageMagick-6/policy.xml &>/dev/null || :
    sudo sed -i 's%<policy domain="coder" rights="none" pattern="PDF" />%<policy domain="coder" rights="read|write" pattern="PDF" />%' /etc/ImageMagick/policy.xml &>/dev/null || :
fi

if [ $buildassests = 1 ]; then
    echo "(re)building assets..."
    # use curl to ping the login page - forces php/apache to rebuild the assets directory
    curl -s http://localhost >/dev/null || :
fi

# Set some git properties - fail silently if git is not installed
if [ "$gitinstalled" == "1" ]; then
    # Set git to ignore file permissions
    git -C $WROOT config core.fileMode false 2>/dev/null
    # Set to cache password in memory (should only ask once per day or each reboot)
    git config --global credential.helper 'cache --timeout=86400' 2>/dev/null
fi

# restart apache
if [ "$restart" == "1" ]; then
    echo -e "\nrestarting apache..\n"
    sudo service apache2 restart &>/dev/null
fi

# remove any leftover nxblu files when switching to a 6.7.x branch
if [ -d "$WROOT/protected/assets/nxblu" ]; then
    echo "Removing nxblu files..."
    sudo rm -rf "$WROOT/protected/assets/nxblu" 2>/dev/null || :
fi

echo ""
echo "...Done"
echo ""

trap : 0
