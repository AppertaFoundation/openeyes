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
    DIR="$(cd -P "$(dirname "$SOURCE")" && pwd)"
    SOURCE="$(readlink "$SOURCE")"
    [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE" # if $SOURCE was a relative symlink, we need to resolve it relative to the path where the symlink file was located
done
# Determine root folder for site - all relative paths will be built from here
SCRIPTDIR="$(cd -P "$(dirname "$SOURCE")" && pwd)"
WROOT="$(cd -P "$SCRIPTDIR/../../" && pwd)"
MODULEROOT=$WROOT/protected/modules

## default DB connection variables
# If database user / pass are empty then set from environment variables of from docker secrets (secrets are the recommended approach)
# Note that this script ignores the old db.conf method. If you are still using this deprecated
# method, then you'll need to manually set the relevant environment variables to match your db.conf
if [ -n "$MYSQL_ROOT_PASSWORD" ]; then
    dbpassword="$MYSQL_ROOT_PASSWORD"
elif [ -f "/run/secrets/MYSQL_ROOT_PASSWORD" ]; then
    dbpassword="$(</run/secrets/MYSQL_ROOT_PASSWORD)"
else
    dbpassword=""
fi

if [ -n "$MYSQL_SUPER_USER" ]; then
    username="$MYSQL_SUPER_USER"
elif [ -f "/run/secrets/MYSQL_SUPER_USER" ]; then
    username="$(</run/secrets/MYSQL_SUPER_USER)"
else
    # fallback to using root for deleting and restoring DB
    username="root"
fi

port=${DATABASE_PORT:-"3306"}
host=${DATABASE_HOST:-"localhost"}
# If we're using docker secrets, override DATABASE_PASS and DATABASE_USER with the secret. Else the environment variable will use it's default value
[ -f /run/secrets/DATABASE_PASS ] && pass="$(</run/secrets/DATABASE_PASS)" || pass=${DATABASE_PASS:-"openeyes"}
[ -f /run/secrets/DATABASE_USER ] && dbuser="$(</run/secrets/DATABASE_USER)" || dbuser=${DATABASE_USER:-"openeyes"}

# Process commandline parameters

nobanner=0
migrate=1
clearaudit=0
bannertext="Database reset at $(date)"
branch=0
demo=0
droparchive=0
nofiles=0
showhelp=0
checkoutparams="--sample-only --no-fix --depth 1 --single-branch"
cleanbase=0
migrateparams="-q"
nofix=0
dwservrunning=0
restorefile=""
hscic=0
nopost=0
nopre=0
postpath=${OE_RESET_POST_SCRIPTS_PATH:-"$MODULEROOT/sample/sql/demo/local-post"}
eventimages=1
fallbackbranch=${OE_RESET_FALLBACK_BRANCH:-master}

PARAMS=()
while [[ $# -gt 0 ]]; do
    p="$1"

    case $p in
    -nb | --no-banner)
        nobanner=1
        echo "Banner will not be (re)set"
        ## Do not update the user banner after reset
        ;;
    --no-migrate | -nm | --nomigrate)
        migrate=0
        echo "DB migrations will not run automatically"
        ## nomigrate will prevent database migrations from running automatically at the end of reset
        ;;
    --no-event-images | -ni)
        eventimages=0
        echo "Event Imagaes will not be automatically generated"
        ## Do not generate event lightning images after demo import
        ;;
    --banner) # set banner textr and move to next param
        bannertext="$2"
        shift
        ;;
    --branch | -b) # set branch and move to next param
        branch="$2"
        shift
        ;;
    --demo)
        demo=1
        echo "Demo scripts will be applied"
        ## Install demo scripts (worklists, etc)
        ;;
    --develop | -d)
        fallbackbranch=develop
        ## fallback to the develop branch if the named branch does not exist
        ;;
    --master | -m)
        fallbackbranch=master
        ## fallback to the develop branch if the named branch does not exist
        ;;
    --help)
        showhelp=1
        ;;
    --no-files)
        echo "Protected files will not be reset"
        nofiles=1
        ;;
    --genetics-enable)
        bash "$SCRIPTDIR"/add-genetics.sh
        ;;
    --genetics-disable)
        bash "$SCRIPTDIR"/add-genetics.sh -r
        ;;
    -p) # set dbpassword and move on to next param
        dbpassword="$2"
        shift
        ;;
    -u) # Set username and move to next param
        username="$2"
        shift
        ;;
    --no-fix)
        nofix=1
        ## do not run oe-fix (useful when calling from other scripts)
        ;;
    --host)
        host="$2"
        shift
        ## Reset a different database
        ;;
    --connectionID)
        migrateparams="-q --connectionID $2"
        shift
        ;;
    --no-post)
        nopost=1
        echo "post-migraton demo scripts will not be run"
        ## do not run post reset scripts
        ;;
    --no-pre)
        nopre=1
        ## do not run pre-demo scripts
        echo "Pre-demo scripts will not be run"
        ;;
    --post-path) # change the location of the local-post folder
        postpath="$2"
        shift
        ;;
    --clean-base)
        cleanbase=1
        ## Do not import base data (migrate from clean db instead)
        ;;
    --ignore-warnings)
        migrateparams="$migrateparams $p"
        # Ignore warnings during migrate
        ;;
    -f | --custom-file) # use a custom database backup for the restore
        restorefile="$2"
        shift
        ;;
    --dmd)
        dmdimport="TRUE"
        ## Run the DMD import after database reset
        echo "DMD import will run automatically after reset"
        ;;
    --hscic | -hscic | -gp | --gp)
        hscic=1
        echo "HSCIC import will be run after reset"
        # run the hscic import after reset
        ;;
    --clear-audit)
        # will clean the audit tables after reset
        clearaudit=1
        echo "Audit tables wil be cleared after reset"
        ;;
    --drop-archive)
        # will drop all tables named archive_* after reset
        droparchive=1
        echo "Archive tables will be droped after reset"
        ;;
    *)
        if [ "$p" == "--hard" ]; then
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
if [ ${#PARAMS[@]} -gt 0 ]; then
    if [ "$branch" != "0" ]; then
        for i in "${PARAMS[@]}"; do
            checkoutparams="$checkoutparams $i"
        done
    else
        echo "Unknown Parameter(s):"
        for i in "${PARAMS[@]}"; do
            echo "$i"
        done
        exit 1
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
    echo "	--help         : Display this help text"
    echo "	--no-migrate "
    echo "          | -nm   : Prevent database migrations running automatically after"
    echo "                   checkout"
    echo "  --clear-audit  : Will clear the audit tables after reset"
    echo "	--branch       : Download sample database on the specified branch"
    echo "          | -b      before resetting"
    echo "	--develop    "
    echo "          |-d    : If specified branch is not found, fallback to develop branch"
    echo "                   - default would fallback to master"
    echo "  --drop-archive : Will drop all tables named archive_% after reset"
    echo "  --host <name>  : Specify a different database host"
    echo "	--no-banner  "
    echo "          |-nb   : Remove the user banner text after resetting"
    echo "	--no-files     : Do not clear protected/files during reset"
    echo "	--banner>      : Set the user banner to the specified text after reset"
    echo "                   - default is 'Database reset at <time>'"
    echo "	-p			   : specify root dbpassword for mysql (default is \"dbpassword\")"
    echo "	-u             : specify username for connecting to database (default is 'root')"
    echo "	--demo         : Install additional scripts to set up openeyes for demo"
    echo "	--genetics-enable"
    echo "                  : enable genetics modules (if currently diabled)"
    echo "	--genetics-disable"
    echo "                  : disable genetics modules (if currently enabled)"
    echo "	--clean-base	: Do not import sample data - migrate from clean db instead"
    echo "	--ignore-warnings	: Ignore warnings during migration"
    echo "	--no-fix		: do not run oe-fix routines after reset"
    echo "  --no-post       : do not run post-migration reset scripts"
    echo "	--custom-file"
    echo "			| -f:	: Use a custom .sql file to restore instead of default. e.g; "
    echo "					  'oe-reset -f <filename>.sql' "
    echo "  --hscic         : Run the hscic import after reset"
    echo ""
    exit 1
fi

# add -p to front of dbpassword (deals with blank dbpassword)
if [ -n "$dbpassword" ]; then
    dbpassword="-p'$dbpassword'"
fi

dbconnectionstring="mysql -u '$username' $dbpassword --port=$port --host=$host"

if ps ax | grep -v grep | grep run-dicom-service.sh >/dev/null; then
    dwservrunning=1
    echo "Stopping dicom-file-watcher..."
    sudo service dicom-file-watcher stop
fi

if [[ ! "$branch" = "0" || ! -d $WROOT/protected/modules/sample/sql ]]; then

    ## If no branch is specified, use build branch or fallback to master
    [ "$branch" = "0" ] && branch=${BUILD_BRANCH:-"master"} || :

    ## Checkout new sample database branch
    echo "Downloading database for $branch"

    eval "$SCRIPTDIR"/oe-checkout.sh "$branch" $checkoutparams --${fallbackbranch} || exit 1
fi

if [ -z "$restorefile" ]; then
    # Pick default restore file based on what is available
    [ -f "$MODULEROOT"/sample/sql/openeyes_sample_data.sql ] && restorefile="$MODULEROOT/sample/sql/openeyes_sample_data.sql" || restorefile="$MODULEROOT/sample/sql/sample_db.zip"
fi

# Test to see if the restore file exists before continuing (note that '-' is a special case for when piping stdin)
[[ ! -f "$restorefile" && "$restorefile" != "-" ]] && {
    echo "Restore file was found at: $restorefile.
    Please use --custom-file to specify a valid restore file"
    exit 1
} || :

echo "Clearing current database..."

dbresetsql="drop database if exists openeyes; create database ${DATABASE_NAME:-openeyes}; grant SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, REFERENCES, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE, CREATE VIEW, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, EVENT, TRIGGER on ${DATABASE_NAME:-openeyes}.* to '$dbuser'@'%' identified by '$pass'; flush privileges;"

echo ""
## write-out command to console (helps with debugging)
# echo "$dbconnectionstring -e \"$dbresetsql\""
## run the same command
eval "$dbconnectionstring -e \"$dbresetsql\""
echo ""

if [ $nofiles = "0" ]; then
    echo Deleting protected files
    # remove protected/files
    sudo rm -rf "$WROOT"/protected/files/*
    # remove any docman process files
    sudo rm -rf /tmp/docman
    # remove hscic import history (otherwise hscic import requires --force to run after reset)
    if [ -d "$WROOT"/protected/data/hscic ]; then
        sudo find "$WROOT"/protected/data/hscic ! -name 'temp' -type d -exec rm -rf {} +
    fi
fi

if [ $cleanbase = "0" ]; then

    if [[ $restorefile =~ \.zip$ ]]; then
        restorefilesize=$(numfmt --to=iec-i --suffix=B $(gzip -dc "$restorefile" | wc -c))
        echo "importing $restorefile (Size: $restorefilesize. This can take some time)...."
        # If pv is installed then use it to show progress
        [ $(pv --version >/dev/null 2>&1)$? = 0 ] >/dev/null && importcmd="pv $restorefile | zcat" || importcmd="zcat $restorefile"
        eval "$importcmd | $dbconnectionstring -D ${DATABASE_NAME:-'openeyes'}" || {
            echo -e "\n\nCOULD NOT IMPORT $restorefile. Quiting...\n\n"
            exit 1
        }
    elif [[ $restorefile =~ \.sql$ ]]; then
        restorefilesize=$(numfmt --to=iec-i --suffix=B $(du -b "$restorefile" | cut -f1))
        echo "importing $restorefile (Size: $restorefilesize. This can take some time)...."
        # If pv is installed then use it to show progress
        [ $(pv --version >/dev/null 2>&1)$? = 0 ] >/dev/null && importcmd="pv $restorefile" || importcmd="cat $restorefile"
        eval "$importcmd | $dbconnectionstring -D ${DATABASE_NAME:-'openeyes'}" || {
            echo -e "\n\nCOULD NOT IMPORT $restorefile. Quiting...\n\n"
            exit 1
        }
    elif [[ $restorefile == '-' ]]; then
        # pipe stdin straight through
        eval "cat - | $dbconnectionstring -D ${DATABASE_NAME:-'openeyes'}" || {
            echo -e "\n\nCOULD NOT IMPORT $restorefile. Quiting...\n\n"
            exit 1
        }
    else
        echo -e "\n\nCOULD NOT IMPORT $restorefile. Unrecognised file extension (Only .zip or .sql files are supported). Quiting...\n\n"
        exit 1
    fi

    ## belt and braces reset to the correct user password, in case the PW was altered by the imported sql
    pwresetsql="ALTER USER '$dbuser'@'%' IDENTIFIED BY '$pass';"
    echo ""
    eval "$dbconnectionstring -e \"$pwresetsql\""

fi

# Force default institution code to match common.php (note that white-space is important in the common.php file)
# First checks OE_INSTITUTION_CODE environment variable. Otherwise uses value from common.php
[ -n "$OE_INSTITUTION_CODE" ] && icode="$OE_INSTITUTION_CODE" || icode=$(grep -oP "(?<=institution_code. => getenv\(\'OE_INSTITUTION_CODE\'\) \? getenv\(\'OE_INSTITUTION_CODE\'\) :.\').*?(?=\',)|(?<=institution_code. => \!empty\(trim\(getenv\(\'OE_INSTITUTION_CODE\'\)\)\) \? getenv\(\'OE_INSTITUTION_CODE\'\) :.\').*?(?=\',)|(?<=\'institution_code. => \').*?(?=.,)" "$WROOT"/protected/config/local/common.php)
if [ -n "$icode" ]; then
    checkicodecmd="SELECT id FROM institution WHERE remote_id = '$icode';"

    icodeexists=$(eval "$dbconnectionstring -D ${DATABASE_NAME:-'openeyes'} -e \"$checkicodecmd\"")
    if [ -z "$icodeexists" ]; then

        echo "

		setting institution to $icode

		"

        updateicodecmd="UPDATE institution SET remote_id = '$icode' WHERE id = 1;"

        eval "$dbconnectionstring -D ${DATABASE_NAME:-'openeyes'} -e \"$updateicodecmd\""
    fi
fi

# Run pre-migration demo scripts
if [[ $demo == "1" && $nopre == "0" ]]; then

    echo "RUNNING PRE_MIGRATION SCRIPTS..."

    shopt -s nullglob
    for f in $(ls "$MODULEROOT"/sample/sql/demo/pre-migrate | sort -V); do
        if [[ $f == *.sql ]]; then
            echo "importing $f"
            eval "$dbconnectionstring -D ${DATABASE_NAME:-'openeyes'} < $MODULEROOT/sample/sql/demo/pre-migrate/$f"
        elif [[ $f == *.sh ]]; then
            echo "running $f"
            bash -l "$MODULEROOT/sample/sql/demo/pre-migrate/$f"
        fi
    done
fi

# Run migrations
if [ $migrate == "1" ]; then
    echo Performing database migrations
    bash "$SCRIPTDIR"/oe-migrate.sh "$migrateparams"
    echo "The following migrations were applied..."
    grep applied "$WROOT"/protected/runtime/migrate.log
fi

# Run post-migration demo scripts
# Actual scripts are in sample module, for greater flexibility
if [[ $demo == "1" && $nopost == "0" ]]; then

    echo "RUNNING POST-MIGRATION DEMO SCRIPTS..."

    basefolder="$MODULEROOT/sample/sql/demo"

    find "$basefolder" "$basefolder"/post-migrate/ "$basefolder"/local-post -maxdepth 1 -type f -printf '%f\0%p\n' | sort -t '\0' -V | awk -F '\0' '{print $2}' | while read f; do
        if [[ $f == *.sql ]]; then
            echo "importing $f"
            eval "$dbconnectionstring -D ${DATABASE_NAME:-'openeyes'} < $f"
        elif [[ $f == *.sh ]]; then
            echo "running $f"
            bash -l "$f"
        fi
    done

    # Run genetics scripts if genetics is enabled
    if grep -q "'Genetics'," "$WROOT"/protected/config/local/common.php && ! grep -q "/\*'Genetics'," "$WROOT"/protected/config/local/common.php; then

        echo "RUNNING Genetics files..."

        basefolder="$MODULEROOT/sample/sql/demo/genetics"

        if [ -d "$basefolder" ]; then
            shopt -s nullglob
            for f in $(ls "$basefolder" | sort -V); do
                if [[ $f == *.sql ]]; then
                    echo "importing $f"
                    eval "$dbconnectionstring -D ${DATABASE_NAME:-'openeyes'} < $basefolder/$f"
                elif [[ $f == *.sh ]]; then
                    echo "running $f"
                    bash -l "$basefolder/$f"
                fi
            done
        fi

    fi

fi

# Set banner to confirm reset
if [ $nobanner == "0" ]; then
    echo "setting banner to: $bannertext"
    echo "
	use ${DATABASE_NAME:-'openeyes'};
	UPDATE ${DATABASE_NAME:-"openeyes"}.setting_installation s SET s.value=\"$bannertext\" WHERE s.key=\"watermark\";
    " | sudo tee /tmp/openeyes-mysql-setbanner.sql >/dev/null

    eval "$dbconnectionstring < /tmp/openeyes-mysql-setbanner.sql"
    sudo rm /tmp/openeyes-mysql-setbanner.sql
fi

if [ -n "$dmdimport" ]; then
    if ! "$SCRIPTDIR"/dmd-import.sh; then
        echo "DMD IMPORT FAILD. Aborting..."
        exit 1
    fi
fi

if [ $nofix -eq 0 ]; then
    bash "$SCRIPTDIR"/oe-fix.sh --no-migrate --no-warn-migrate --no-composer --no-permissions #--no-compile --no-restart
fi

if [ $hscic -eq 1 ]; then
    bash "$SCRIPTDIR"/import-hscic-data.sh --force
fi

if [ $droparchive -eq 1 ]; then
    echo "Dropping archive tables..."

    sqlcmd="USE ${DATABASE_NAME:-'openeyes'};
    START TRANSACTION;
    DELIMITER //
    CREATE PROCEDURE IF NOT EXISTS droparchive()
    BEGIN
    SET @n = (SELECT count(table_name) FROM information_schema.tables WHERE table_schema = '${DATABASE_NAME:-openeyes}' AND table_name LIKE 'archive_%');

    IF @n > 0 THEN
        SET FOREIGN_KEY_CHECKS = 0;
        set @s = (SELECT CONCAT( 'DROP TABLE ', GROUP_CONCAT(table_name) , ';' ) FROM information_schema.tables WHERE table_schema = '${DATABASE_NAME:-openeyes}' AND table_name LIKE 'archive_%');
        PREPARE stmt FROM @s;
        EXECUTE stmt;
        SET FOREIGN_KEY_CHECKS = 1;
    END IF;

    END; //
    DELIMITER ;

    CALL droparchive();

    DROP PROCEDURE droparchive;

    COMMIT;"

    eval "echo \"$sqlcmd\" | $dbconnectionstring "
fi

if [ $clearaudit -eq 1 ]; then
    echo "Truncating Audit..."
    sqlcmd="SET FOREIGN_KEY_CHECKS = 0; TRUNCATE TABLE audit_ipaddr; TRUNCATE TABLE audit_server; TRUNCATE TABLE audit_useragent; TRUNCATE TABLE audit; SET FOREIGN_KEY_CHECKS = 1;"
    eval "$dbconnectionstring -D ${DATABASE_NAME:-'openeyes'} -e \"$sqlcmd\""
fi

# Generate lightning event images for demo patients
# Actual script(s) are in sample module, for greater flexibility
if [[ $eventimages == "1" && $demo == "1" ]]; then
    echo "RUNNING EVENT IMAGE GENERATION SCRIPT(S)..."

    basefolder="$MODULEROOT/sample/sql/demo/event-image"
    shopt -s nullglob
    for f in $(ls "$basefolder" | sort -V); do
        if [[ $f == *.sql ]]; then
            echo "importing $f"
            eval "$dbconnectionstring -D ${DATABASE_NAME:-'openeyes'} < $basefolder/$f"
        elif [[ $f == *.sh ]]; then
            echo "running $f"
            bash -l "$basefolder/$f"
        fi
    done
fi

# restart the service if we stopped it
if [ $dwservrunning = 1 ]; then
    echo "Restarting dicom-file-watcher..."
    sudo service dicom-file-watcher start
fi

printf "\e[42m\e[97m  RESET COMPLETE  \e[0m \n"
echo ""

bash "$SCRIPTDIR"/oe-which.sh
