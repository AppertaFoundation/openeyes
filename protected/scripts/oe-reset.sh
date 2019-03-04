#!/bin/bash -l

## If the OE_NO_DB build parameter is set, then this script will not be run
if [ "$OE_NO_DB" == "true" ]; then
    echo "
    OE_NO_DB is $OE_NO_DB - skipping database reset
    "
fi

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
MODULEROOT=$WROOT/protected/modules

## default DB connection variables
# If database user / pass are empty then set from environment variables of from docker secrets (secrets are the recommended approach)
# Note that this script ignores the old db.conf method. If you are still using this deprecated
# method, then you'll need to manually set the relevant environment variables to match your db.conf
if [ ! -z "$MYSQL_ROOT_PASSWORD" ] ; then
    dbpassword="$MYSQL_ROOT_PASSWORD"
elif [ -f "/run/secrets/MYSQL_ROOT_PASSWORD" ] ; then
    dbpassword="$(</run/secrets/MYSQL_ROOT_PASSWORD)"
else
    dbpassword=""
fi

if [ ! -z "$MYSQL_SUPER_USER" ] ; then
    username="$MYSQL_SUPER_USER"
elif [ -f "/run/secrets/MYSQL_SUPER_USER" ] ; then
    username="$(</run/secrets/MYSQL_SUPER_USER)"
else
	# fallback to using root for deleting and restoring DB
    username="root"
fi


port=${DATABASE_PORT:-"3306"}
host=${DATABASE_HOST:-"localhost"}


# Process commandline parameters

nobanner=0
migrate=1
bannertext="Database reset at $(date)"
branch=0
demo=0
nofiles=0
showhelp=0
checkoutparams="--sample-only --no-fix"
cleanbase=0
migrateparams="-q"
nofix=0
dwservrunning=0
restorefile="/tmp/openeyes_sample_data.sql"

PARAMS=()
while [[ $# -gt 0 ]]
do
    p="$1"

    case $p in
    	-nb|--no-banner) nobanner=1
    		## Do not update the user banner after reset
    		;;
    	--no-migrate|-nm|--nomigrate) migrate=0
    		## nomigrate will prevent database migrations from running automatically at the end of reset
    		;;
    	--banner) # set banner textr and move to next param
            bannertext="$2"
            shift
    		;;
    	--branch|-b) # set branch and move to next param
            branch="$2"
            shift
    		;;
    	--demo) demo=1
    		## Install demo scripts (worklists, etc)
    		;;
    	--help) showhelp=1
    		;;
    	--no-files) nofiles=1
    		;;
        # TODO: fix genetics
    	# --genetics-enable)
    	# 	sudo bash /vagrant/install/add-genetics.sh
    	# ;;
    	# --genetics-disable)
    	# 	sudo bash /vagrant/install/add-genetics.sh -r
    	# ;;
    	-p) # set dbpassword and move on to next param
            dbpassword="$2"
            shift
    	;;
        -u) # Set username and move to next param
            username="$2"
            shift
        ;;
    	--no-fix) nofix=1
    		## do not run oe-fix (useful when calling from other scripts)
    	;;
    	--clean-base) cleanbase=1
    		## Do not import base data (migrate from clean db instead)
    	;;
    	--ignore-warnings) migrateparams="$migrateparams $p"
    		# Ignore warnings during migrate
    	;;
		-f|--custom-file) # use a custom database backup for the restore
			restorefile="$2"
			shift
		;;
    	*)  if [ "$p" == "--hard" ]; then
                echo "Unknown parameter $p $2"
                exit 1
            else
                # Hold for processing later
                PARAMS+=("$p")
            fi
		;;
    esac
    shift # move to next parameter
done

# If we are checking out new branch,then pass all unprocessed commands to checkout command
# Else, throw error and list unknown commands
if  [ ${#PARAMS[@]} -gt 0 ]; then
    if [ "$branch" != "0" ]; then
        for i in "${PARAMS[@]}"
        do
            $checkoutparams="$checkoutparams $i"
        done
    else
        echo "Unknown Parameter(s):"
        for i in "${PARAMS[@]}"
        do
            echo $i
        done
        exit 1;
    fi
fi

if [ $showhelp = 1 ]; then
    echo ""
    echo "DESCRIPTION:"
    echo "Resets database to latest 'sample' database"
    echo ""
    echo "usage: $0 [--branch | b branchname] [--help] [--no-migrate | -nm ] [--banner \"banner text\"] [--develop | -d] [ --no-banner | -nb ] [-p dbpassword] [--genetics-enable] [--genetics-disable]"
    echo ""
    echo "COMMAND OPTIONS:"
    echo "  --help         : Display this help text"
    echo "  --no-migrate "
    echo "          | -nm   : Prevent database migrations running automatically after"
    echo "                   checkout"
	echo "  --branch       : Download sample database on the specified branch"
	echo "          | -b      before resetting"
    echo "  --develop    "
    echo "          |-d    : If specified branch is not found, fallback to develop branch"
    echo "                   - default would fallback to master"
    echo "  --no-banner  "
	echo "          |-nb   : Remove the user banner text after resetting"
	echo "  --no-files     : Do not clear protected/files during reset"
    echo "  --banner>      : Set the user banner to the specified text after reset"
    echo "                   - default is 'Database reset at <time>'"
	echo "	-p			   : specify root dbpassword for mysql (default is \"dbpassword\")"
    echo "  -u             : specify username for connecting to database (default is 'root')"
	echo "  --demo         : Install additional scripts to set up openeyes for demo"
	echo "  --genetics-enable"
	echo "                  : enable genetics modules (if currently diabled)"
	echo "  --genetics-disable"
	echo "                  : disable genetics modules (if currently enabled)"
	echo "	--clean-base	: Do not import sample data - migrate from clean db instead"
	echo "	--ignore-warnings	: Ignore warnings during migration"
	echo "	--no-fix		: do not run oe-fix routines after reset"
	echo ""
    exit 1
fi

# add -p to front of dbpassword (deals with blank dbpassword)
if [ ! -z $dbpassword ]; then
	dbpassword="-p$dbpassword"
fi

dbconnectionstring="sudo mysql -u $username $dbpassword --port=$port --host=$host"


if ps ax | grep -v grep | grep run-dicom-service.sh > /dev/null; then
		dwservrunning=1
		echo "Stopping dicom-file-watcher..."
		sudo service dicom-file-watcher stop
fi

if [[ ! "$branch" = "0"  || ! -d $WROOT/protected/modules/sample/sql ]]; then
	
	## If no branch is specified, use build branch or fallback to master
	[ "$branch" = "0" ] && branch=${BUILD_BRANCH:-"master"} || :
	
	## Checkout new sample database branch
	echo "Downloading database for $branch"

    bash $SCRIPTDIR/oe-checkout.sh $branch $checkoutparams
fi

echo "Clearing current database"

echo "
drop database if exists openeyes;
create database openeyes;
grant all privileges on openeyes.* to 'openeyes'@'%' identified by 'openeyes';
flush privileges;
" > /tmp/openeyes-mysql-create.sql

eval $dbconnectionstring < /tmp/openeyes-mysql-create.sql
rm /tmp/openeyes-mysql-create.sql

if [ $nofiles = "0" ]; then
	echo Deleting protected files
	sudo rm -rf $WROOT/protected/files/*
	sudo rm -rf /tmp/docman
fi

if [ $cleanbase = "0" ]; then
  # Extract or copy sample DB (since v3.2 db has been zipped)
  rm -f /tmp/openeyes_sample_data.sql >/dev/null
  [ -f $MODULEROOT/sample/sql/openeyes_sample_data.sql ] && cp $MODULEROOT/sample/sql/openeyes_sample_data.sql /tmp || :
  [ -f $MODULEROOT/sample/sql/sample_db.zip ] && unzip $MODULEROOT/sample/sql/sample_db.zip -d /tmp || :

	echo "Re-importing database"
	eval $dbconnectionstring -D openeyes < $restorefile || { echo -e "\n\nCOULD NOT IMPORT $restorefile. Quiting...\n\n"; exit 1; }
fi

# Force default institution code to match common.php
icode=$(grep -oP '(?<=institution_code. => .).*?(?=.,)' $WROOT/protected/config/local/common.php)
if [ ! -z $icode ]; then

	echo "

	setting institution to $icode

	"

	echo "UPDATE institution SET remote_id = '$icode' WHERE id = 1;" > /tmp/openeyes-mysql-institute.sql

	eval $dbconnectionstring -D openeyes < /tmp/openeyes-mysql-institute.sql

	rm /tmp/openeyes-mysql-institute.sql
fi

# Run pre-migration demo scripts
if [ $demo = "1" ]; then

	echo "RUNNING PRE_MIGRATION SCRIPTS..."

	shopt -s nullglob
	for f in $(ls $MODULEROOT/sample/sql/demo/pre-migrate | sort -V)
	do
		if [[ $f == *.sql ]]; then
			echo "importing $f"
			eval $dbconnectionstring -D openeyes < $MODULEROOT/sample/sql/demo/pre-migrate/$f
		elif [[ $f == *.sh ]]; then
			echo "running $f"
			sudo bash "$MODULEROOT/sample/sql/demo/pre-migrate/$f"
		fi
	done
fi

# Run migrations
if [ $migrate = "1" ]; then
	echo Performing database migrations
	bash $SCRIPTDIR/oe-migrate.sh $migrateparams
	echo "The following migrations were applied..."
	grep applied $WROOT/protected/runtime/migrate.log
fi

# Run demo scripts
if [ $demo = "1" ]; then

	echo "RUNNING DEMO SCRIPTS..."

	shopt -s nullglob
	for f in $(ls $MODULEROOT/sample/sql/demo | sort -V)
	do
		if [[ $f == *.sql ]]; then
			echo "importing $f"
			eval $dbconnectionstring -D openeyes < $MODULEROOT/sample/sql/demo/$f
		elif [[ $f == *.sh ]]; then
			echo "running $f"
			sudo bash "$MODULEROOT/sample/sql/demo/$f"
		fi
	done
fi

# Run genetics scripts if genetics is enabled
if grep -q "'Genetics'," $WROOT/protected/config/local/common.php && ! grep -q "/\*'Genetics'," $WROOT/protected/config/local/common.php ; then

	echo "RUNNING Genetics files..."

	shopt -s nullglob
    for f in $(ls $MODULEROOT/sample/sql/demo/genetics | sort -V)
    do
		if [[ $f == *.sql ]]; then
			echo "importing $f"
			eval $dbconnectionstring -D openeyes < $MODULEROOT/sample/sql/demo/genetics/$f
		elif [[ $f == *.sh ]]; then
			echo "running $f"
			sudo bash "$MODULEROOT/sample/sql/demo/genetics/$f"
		fi
    done

fi

# Set banner to confirm reset
if [ ! $nobanner = "1" ]; then
	echo "setting banner to: $bannertext"
	echo "
	use openeyes;
	UPDATE openeyes.setting_installation s SET s.value='$bannertext' WHERE s.key='watermark';
	" | sudo tee /tmp/openeyes-mysql-setbanner.sql > /dev/null

	eval $dbconnectionstring < /tmp/openeyes-mysql-setbanner.sql
	sudo rm /tmp/openeyes-mysql-setbanner.sql
fi

# Run local post-migaration demo scripts
if [ $demo = "1" ]; then

	echo "RUNNING POST RESET SCRIPTS..."

	shopt -s nullglob
    for f in $(ls $MODULEROOT/sample/sql/demo/local-post | sort -V)
    do
		if [[ $f == *.sql ]]; then
			echo "importing $f"
			eval $dbconnectionstring -D openeyes < $MODULEROOT/sample/sql/demo/local-post/$f
		elif [[ $f == *.sh ]]; then
			echo "running $f"
			sudo bash "$MODULEROOT/sample/sql/demo/local-post/$f"
		fi
    done
fi

if [ ! $nofix = 1 ]; then
	bash $SCRIPTDIR/oe-fix.sh --no-migrate --no-warn-migrate --no-composer --no-permissions #--no-compile --no-restart
fi

# restart the service if we stopped it
if [ $dwservrunning = 1 ]; then
	echo "Restarting dicom-file-watcher..."
	sudo service dicom-file-watcher start
fi

printf "\e[42m\e[97m  RESET COMPLETE  \e[0m \n"
echo ""

bash $SCRIPTDIR/oe-which.sh
