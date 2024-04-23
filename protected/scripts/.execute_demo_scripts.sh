#!/bin/bash
# This script is used to execute the demo scripts in the correct order

## NOTE: This script assumes it is in protected/scripts. If you move it then relative paths will not work!

if [[ -z $SCRIPTDIR ]]; then
    # shellcheck source="/var/www/openeyes/protected/scripts/.source_config.sh"
    source "$(dirname "${BASH_SOURCE[0]}")/.source_config.sh"
fi

# get the database connection details
if [[ -z $dbconnectionstring ]]; then
    # shellcheck source="/var/www/openeyes/protected/scripts/.db_connection.sh"
    source "$(dirname "${BASH_SOURCE[0]}")/.db_connection.sh"
fi

function execute_demo_scripts() {
    folder="$1"
    basefolder="$MODULEROOT/sample/sql/demo"

    find "$basefolder" "$basefolder/$folder" -maxdepth 1 -type f -printf '%f\0%p\n' | sort -t '\0' -V | awk -F '\0' '{print $2}' | while read -r f; do
        if [[ $f == *.sql ]]; then
            echo "importing $f"
            eval "$dbconnectionstring -D ${DATABASE_NAME:-'openeyes'} < $f"
        elif [[ $f == *.sh ]]; then
            echo "running $f"
            bash -l "$f"
        fi
    done
}

if [ -n $1 ]; then
    execute_demo_scripts "$1"
fi
