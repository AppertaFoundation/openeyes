#!/bin/bash -l
## Extracts user information from one databse and extracts to another

## NOTE: This script assumes it is in protected/scripts. If you move it then relative paths will not work!

# Find full folder path where this script is located, then find root folder
SOURCE="${BASH_SOURCE[0]}"
while [ -h "$SOURCE" ]; do # resolve $SOURCE until the file is no longer a symlink
    DIR="$(cd -P "$(dirname "$SOURCE")" && pwd)"
    SOURCE="$(readlink "$SOURCE")"
    [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE" # if $SOURCE was a relative symlink, we need to resolve it relative to the path where the symlink file was located
done
# DEtermine root folder for site - all relative paths will be built from here
SCRIPTDIR="$(cd -P "$(dirname "$SOURCE")" && pwd)"
WROOT="$(cd -P "$SCRIPTDIR/../../" && pwd)"

## Default DB Connection variables
# If database user / pass are empty, then set from environment variables from docker secrets
if [ -n "$MYSQL_ROOT_PASSWORD" ]; then
    dbpassword="$MYSQL_ROOT_PASSWORD"
elif [ -f "/run/secrets/MYSQL_ROOT_PASSWORD" ]; then
    dbpassword="$(</run/secrets/MY_SQL_PASSWORD)"
else
    dbpassword=""
fi

if [ -n "$MYSQL_SUPER_USER" ]; then
    username="$MYSQL_SUPER_USER"
elif [ -f "/run/secrets/MYSQL_SUPER_USER" ]; then
    username="$(</run/secrets/MYSQL_SUPER_USER)"
else
    # Fallback to using root for deleting and restoring DB
    username="root"
fi

port=${DATABASE_PORT:-"3306"}
host=${DATABASE_HOST:-"localhost"}
# Override database password with docker secrets if it is set. Else the environment variable will user it's default value
[ -f /run/secrets/DATABASE_PASSWORD ] && pass="$(</run/secrets/DATABASE_PASSWORD)" || pass=${DATABASE_PASSWORD:-"openeyes"}

# Add -p to front of dbpassword (deals with blank dbpassword)
if [ ! -z $dbpassword ]; then
    dbpassword="-p$dbpassword"
fi

# Process commandline parameters

extraction=0
injection=0
sethost=0
setdatabase=0
setfile=0
showhelp=0
showpreview=0
error=0

while [[ $# -gt 0 ]]; do
    p="$1"

    case $p in
    --extraction | -e)
        extraction=1
        ## Do the extraction process of the script
        ;;
    --injection | -i)
        injection=1
        ## Do the injection process of the script
        ;;
    --help)
        showhelp=1
        ## Help
        ;;
    --host | -h)
        sethost=1
        restorehost="$2"
        shift
        ## Set the host for injection
        ;;
    --database | -db)
        setdatabase=1
        restoredatabase="$2"
        shift
        ## Set the database for injection
        ;;
    --file | -f)
        setfile=1
        restorefile="$2"
        shift
        ## Set the file for injection
        ;;
    --preview | -p)
        showpreview=1
        ## Preview the user table before injection
        ;;
    *) ;;
    esac
    shift

done

# Show error if no process has been chosen
if [[ $extraction == 0 ]] && [[ $injection == 0 ]] && [[ $showhelp == 0 ]]; then
    echo ""
    echo "ERROR: Please select extraction or injection process"
    echo ""
    error=1
    showhelp=1
fi

if [ $showhelp = 1 ]; then
    if [ $error = 0 ]; then
        echo ""
        echo "DESCRIPTION:"
        echo "Extract and inject the user and associated tables from one database to another"
        echo ""
        echo "usage: $0 [--help] [--extraction | -e] [--injection | -i {OPTIONS} ] [--preview | -p] [--host | -h hostname] [--database | -db databasename] [--file | -file filename]"
        echo ""
    fi
    echo ""
    echo "COMMAND OPTIONS:"
    echo " --help       : Display this help text"
    echo " --extraction "
    echo "        | -e : Extract user and associated tables from database"
    echo " --injection "
    echo "        | -i : Inject the user and associated tables from a file"
    echo ""
    echo "INJECTION OPTIONS:"
    echo "--preview "
    echo "        | -p : Preview the contents of user table to be injected from the file"
    echo " --host "
    echo "        | -h : Host name where the tables need to be injected"
    echo " --database "
    echo "        | -db : Database name where the tables need to be injected"
    echo " --file "
    echo "        | -f  : Use a custom encrypted .enc file to inject the tables"
    echo ""
    exit 1
fi

if [ $extraction = 1 ]; then
    echo ""
    echo "****NOTICE****"
    echo "This script will extract information from user and associated tables except admin and docman users"
    echo ""
    echo "Starting MySQL extraction process..."
    echo ""

    # Read the password from the command prompt
    echo -n "Enter the password to encrypt the file: "
    read -r -s encryptionpassword
    echo ""

    # Dump command to extract user table and other tables related to it
    dbdumpuser="mysqldump -u $username $dbpassword --port=$port --host=$host ${DATABASE_NAME:-'openeyes'} user --where=\"username not in ('admin','docman_user')\" --single-transaction --skip-add-drop-table --replace --extended-insert=FALSE | sed 's/^CREATE TABLE /CREATE TABLE IF NOT EXISTS /' > dump.sql;"
    dbdumpcontact="mysqldump -u $username $dbpassword --port=$port --host=$host ${DATABASE_NAME:-'openeyes'} contact --where=\"id in (select contact_id from user where username not in ('admin','docman_user'))\" --skip-add-drop-table --single-transaction --replace --extended-insert=FALSE | sed 's/^CREATE TABLE /CREATE TABLE IF NOT EXISTS /' >> dump.sql"
    dbdumpfirmuserassignment="mysqldump -u $username $dbpassword --port=$port --host=$host ${DATABASE_NAME:-'openeyes'} firm_user_assignment --where=\"user_id in (select id from user where username not in ('admin','docman_user'))\" --skip-add-drop-table --single-transaction --replace --extended-insert=FALSE | sed 's/^CREATE TABLE /CREATE TABLE IF NOT EXISTS /' >> dump.sql"
    dbdumpuserroles="mysqldump -u $username $dbpassword --port=$port --host=$host ${DATABASE_NAME:-'openeyes'} authassignment --where=\"userid in (select id from user where username not in ('admin','docman_user'))\" --single-transaction --skip-add-drop-table --replace --extended-insert=FALSE | sed 's/^CREATE TABLE /CREATE TABLE IF NOT EXISTS /' >> dump.sql"

    eval "$dbdumpuser"
    eval "$dbdumpcontact"
    eval "$dbdumpfirmuserassignment"
    eval "$dbdumpuserroles"

    # Encrypt the mysqldump file
    eval "openssl enc -aes-256-cbc -pbkdf2 -salt -in dump.sql -out dump.sql.enc -k $encryptionpassword"

    echo ""
    echo "Showing the contents of the user table that were extracted"
    echo ""
    echo -e " USERNAME\t\t\t\t| FIRST_NAME\t\t\t\t| LAST_NAME\t\t\t\t| EMAIL "
    printf -- '-%.0s' {1..150}
    echo ""
    preview="awk '/REPLACE INTO \`user\` VALUES /' dump.sql | cut -d\",\" -f2,3,4,5 | sed 's/,/\t|/g' | expand -40 | sed \"s/'/ /g\""
    eval "$preview"
    echo ""
    echo -n "The total number of user records to be injected are: "
    eval "$preview | wc -l"
    echo ""

    # Remove the unencrypted sql file
    if eval "rm dump.sql"; then
        echo ""
        echo "...Done"
        echo ""
        echo "The extracted file encrypted with password is ==> dump.sql.enc"
        echo ""
        echo ""
    fi
    exit 1
fi

if [ $injection = 1 ]; then

    # Give the warning message to the user
    echo ""
    echo "****WARNING****"
    echo "This script will overwrite the user and associated tables to it. It is recommended to run this in an empty environment."
    read -r -p "Do you wish to continue?[y/n]: " input

    if [[ $input == "n" || $input == "N" || $input == "no" ]]; then
        echo ""
        exit 1
    elif [[ $input == "y" || $input == "Y" || $input == "yes" ]]; then
        echo ""
    else
        echo "Invalid input. Quitting..."
        echo ""
        exit 1
    fi

    # Get host and database name
    if [ $sethost = 1 ]; then
        host=$restorehost
    fi
    if [ $setdatabase = 1 ]; then
        database=$restoredatabase
    else
        database=${DATABASE_NAME:-'openeyes'}
    fi

    # Get injection file
    if [ $setfile = 1 ]; then
        if [[ $restorefile == *.enc ]]; then
            file=$restorefile
        else
            echo ""
            echo "ERROR: File format not supported. Please enter the path of .enc file"
            echo ""
            exit 1
        fi
    else
        file="dump.sql.enc"
    fi

    # Read the decryption password from the command prompt
    echo -n "Enter the password to decrypt the file: "
    read -r -s decryptionpassword
    echo ""

    # Decrypt the mysqldump file
    decryptionstring="openssl enc -aes-256-cbc -pbkdf2 -d -in $file -out dump.sql -k $decryptionpassword"
    if ! eval "$decryptionstring"; then
        echo ""
        exit 0
    fi

    # Extract the user table from the file and preview some of its fields
    if [ $showpreview = 1 ]; then
        echo ""
        echo "Showing the contents of the user table from $file: "
        echo ""
        # Format the table to be displayed
        echo -e " USERNAME\t\t\t\t| FIRST_NAME\t\t\t\t| LAST_NAME\t\t\t\t| EMAIL "
        printf -- '-%.0s' {1..150}
        echo ""
        preview="awk '/REPLACE INTO \`user\` VALUES /' dump.sql | cut -d\",\" -f2,3,4,5 | sed 's/,/\t|/g' | expand -40 | sed \"s/'/ /g\""
        eval "$preview"
        echo ""
        echo -n "The total number of user records to be injected are: "
        eval "$preview | wc -l"
        echo ""
        read -r -p "Do you wish to continue with the injection process?[y/n]: " input
        # Continue with the injection process
        if [[ $input == "n" || $input == "N" || $input == "no" ]]; then
            eval "rm dump.sql"
            echo ""
            exit 1
        elif [[ $input == "y" || $input == "Y" || $input == "yes" ]]; then
            echo ""
        else
            echo "Invalid input. Quitting..."
            echo ""
            eval "rm dump.sql"
            exit 1
        fi
    fi

    echo ""
    echo "Starting MySQL injection process..."
    echo ""

    # Connect to databse to deisable and enable foreign keys
    dbconnectionstring="mysql -u $username $dbpassword --port=$port --host=$host --database=$database"

    # Temporarily disable foreign key constraints
    dbforeignkeyconstraintdisable="SET FOREIGN_KEY_CHECKS=0"
    eval "$dbconnectionstring -e \"$dbforeignkeyconstraintdisable\""

    # Inject the tables to the database
    dbdumpinjectstring="mysql -u $username $dbpassword --port=$port --host=$host --database=$database < dump.sql;"
    eval "$dbdumpinjectstring"

    # Update some of the columns of the user table to null
    dbupdateusercolumns="UPDATE user SET last_firm_id = NULL, last_site_id = NULL, signature_file_id = NULL"
    eval "$dbconnectionstring -e \"$dbupdateusercolumns\""

    # Enable foreign key constraints again
    dbforeignkeyconstraintenable="SET FOREIGN_KEY_CHECKS=1"
    eval "$dbconnectionstring -e \"$dbforeignkeyconstraintenable\""

    # Remove the sql file after injection process and only keep the encrypted file
    if eval "rm dump.sql"; then
        echo ""
        echo "...Done"
        echo ""
    fi
    exit 1
fi
