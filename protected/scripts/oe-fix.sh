#!/bin/bash
## Resets various caches and configs

## NOTE: This script assumes it is in protected/scripts. If you move it then relative paths will not work!

# Find fuill folder path where this script is located, then find root folder
SOURCE="${BASH_SOURCE[0]}"
while [ -h "$SOURCE" ]; do # resolve $SOURCE until the file is no longer a symlink
  DIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"
  SOURCE="$(readlink "$SOURCE")"
  [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE" # if $SOURCE was a relative symlink, we need to resolve it relative to the path where the symlink file was located
done
# Determine root folder for site - all relative paths will be built from here
SCRIPTDIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"
WROOT="$( cd -P "$SCRIPTDIR/../../" && pwd )"

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

while [[ $# -gt 0 ]]
do
    p="$1"

    case $p in
	    --no-clear|-nc) clearcahes=0
	    ;;
	    --no-assets|-na) buildassests=0
	    ;;
        --no-migrate|--nomigrate|-nm) migrate=0
	    ;;
	    --no-eyedraw|-ned|-ne) eyedraw=0
	    ;;
        --help) showhelp=1
        ;;
	    --no-composer|--no-dependencies|-nd) composer=0
	    ;;
	    --no-permissions|-np) noperms=1
	    ;;
        --force-perms) forceperms=1
        ;;
	    --no-warn-migrate) nowarnmigrate=1
	    ;;
	    -fc|--reset-config) resetconfig=1
	    ;;
        -r|--restart) restart=1
        ;;
		--no-compile) #reserved for future use
		;;
	    *)  echo "Unknown command line: $p"
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

if [ ! -f "$WROOT/protected/config/local/common.php" ]; then

	# 	************************************************************************
	# 	************************************************************************
	# 	********* WARNING: Restoring backed up local configuration ... *********
	# 	*********                                                      *********
	# 	********* Remove /etc/openeyes/backup/config/local to prevent  *********
	# 	*********                  or use -ff flag                     *********
	# 	************************************************************************
	# 	************************************************************************
    #

        echo "WARNING: Copying sample configuration into local ..."
		sudo mkdir -p $WROOT/protected/config/local
		sudo cp -n $WROOT/protected/config/local.sample/common.sample.php $WROOT/protected/config/local/common.php
		sudo cp -n $WROOT/protected/config/local.sample/console.sample.php $WROOT/protected/config/local/console.php

fi;

# update composer and npm dependencies. If OE_MODE is LIVE, then do not install dev components
if [ "$composer" == "1" ]; then


    [ "$OE_MODE" == "LIVE" ] && composerexta="--no-dev"
    [ "$OE_MODE" == "LIVE" ] && npmextra="--only=production"

    echo "DEPENDENCIES BEING EVALUATED... $composerexta $npmextra"

	echo "Installing/updating composer dependencies"
	sudo -E composer install --working-dir=$WROOT --no-plugins --no-scripts $composerexta

	echo "Installing/updating npm dependencies"
	rm $WROOT/package-lock.json &> /dev/null
	sudo -E npm update --no-save --prefix $WROOT $npmextra

	# If we've switched from dev to live, remove dev dependencies
	[ "$OE_MODE" == "LIVE" ] && npm prune --prefix $WROOT --production

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
	sudo php $WROOT/protected/yiic eyedrawconfigload --filename=$WROOT/protected/config/core/OE_ED_CONFIG.xml 2>/dev/null
fi

# Clear caches
if [ $clearcahes = 1 ]; then
	echo "Clearing caches..."
	sudo rm -rf $WROOT/protected/runtime/cache/* 2>/dev/null || :
	sudo rm -rf $WROOT/assets/* 2>/dev/null || :
	echo ""
fi

# Fix permissions
if [ $noperms = 0 ]; then
    sudo gpasswd -a "$USER" www-data # add current user to www-data group
	echo "Resetting file permissions..."
    if [ `stat -c '%U' $WROOT` != $USER ] || [ `stat -c '%G' $WROOT` != "www-data" ]; then
        echo "updaing ownership on $WROOT"
        sudo chown -R "$USER":www-data $WROOT
    else
        echo "ownership of $WROOT looks ok, skipping. Use --force-perms to override"
    fi


    folders774=( $WROOT/protected/config/local $WROOT/assets/ $WROOT/protected/runtime $WROOT/protected/files )

    for i in "${folders774[@]}"
    do
        if [ `stat -c %a "$i"` != 774 ] || [ $forceperms == 1 ]; then
            echo "updating $i to 774..."
            sudo chmod -R 774 $i
        else
            echo "Permissions look ok for $i, skipping. Use --force-perms to override"
        fi
    done

    touch $WROOT/protected/runtime/testme
    touch $WROOT/protected/files/testme

    if [ `stat -c '%U' $WROOT/protected/runtime/testme` != $USER ] || [ `stat -c '%G' $WROOT/protected/runtime/testme` != "www-data" ] || [ `stat -c %a "$WROOT/protected/runtime/testme"` != 774 ]; then
        echo "setting sticky bit for protected/runtime"
        sudo chmod -R g+s $WROOT/protected/runtime
    fi

    if [ `stat -c '%U' $WROOT/protected/files/testme` != $USER ] || [ `stat -c '%G' $WROOT/protected/files/testme` != "www-data" ] || [ `stat -c %a "$WROOT/protected/files/testme"` != 774 ]; then
        echo "setting sticky bit for protected/files"
        sudo chmod -R g+s $WROOT/protected/files
    fi

    # re-own composer and npm config folders in user home directory (sots issues caused if sudo was used to composer/npm update previously)
	sudo chown -R "$USER" ~/.config 2>/dev/null || :
	sudo chown -R "$USER" ~/.composer 2>/dev/null || :

	#  update ImageMagick policy to allow PDFs
	sudo sed -i 's%<policy domain="coder" rights="none" pattern="PDF" />%<policy domain="coder" rights="read|write" pattern="PDF" />%' /etc/ImageMagick-6/policy.xml &> /dev/null
	sudo sed -i 's%<policy domain="coder" rights="none" pattern="PDF" />%<policy domain="coder" rights="read|write" pattern="PDF" />%' /etc/ImageMagick/policy.xml &> /dev/null
fi

if [ $buildassests = 1 ]; then
	echo "(re)building assets..."
	# use curl to ping the login page - forces php/apache to rebuild the assets directory
	curl -s http://localhost/site/login > /dev/null
    curl -s http://localhost:8888/site/login > /dev/null
fi

# Set some git properties

git config core.fileMode false 2>/dev/null
# Set to cache password in memory (should only ask once per day or each reboot)
git config --global credential.helper 'cache --timeout=86400' 2>/dev/null

# restart apache
if [ "$restart" == "1" ]; then
    echo -e "\nrestarting apache..\n"
    sudo service apache2 restart &> /dev/null
fi

echo ""
echo "...Done"
echo ""
