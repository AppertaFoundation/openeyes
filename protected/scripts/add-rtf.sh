#!/bin/bash

## adds or removes rtf module

## NOTE: This script assumes it is in protected/scripts. If you move it then relative paths will not work!

# Find full folder path where this script is located, then find root folder
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
YII=$WROOT/protected/yiic

# Determine user, if sudo is required
if [ $(whoami) != root ]; then
    echo ""
    echo "Need root permissions."
    echo ""
    sudo -i
fi

# Test command parameters
remove=0
showhelp=0
delete=0

for i in "$@"
do
case $i in
	--remove|-remove|-r|-uninstall|--uninstall|-u|--disable|-disable) remove=1
	;;
    --delete|-delete|-d) delete=1 remove=1
    ;;
    --help|-help|-h) showhelp=1
    ;;
	*)  echo "Unknown command line: $i"
    ;;
esac
done

# Show help text
if [ $showhelp = 1 ]; then
    echo ""
    echo "DESCRIPTION:"
    echo "Adds or removes the rtf generation module."
    echo ""
    echo "usage: $0 [--remove ] [--delete] [--help] "
    echo ""
    echo "COMMAND OPTIONS:"
	echo ""
	echo "  --help         : Show this help"
    echo "  --remove       : Remove the rtf generation module"
    echo "  --delete       : Remove the rtf generation module and deletes the files in the submodule"
	echo ""
    exit 1
fi

if [ ! $remove = 1 ]; then
# Enable rtf module
    echo "Installing Java..."
    apt-get update >/dev/null 2>&1 &&
    apt-get install default-jre -y >/dev/null 2>&1 &&
    echo "Installed Java" ||
    { echo "Cannot install Java"; exit 1; }

    echo "Checking if rtf module has been cloned..."
    { [ -d "$WROOT/protected/modules/RTFGeneration/" ] && echo "Module exists"; } ||
    { echo """Module does not exist, run the following command in protected/modules/ (outside the container):
            git clone git@github.com:ToukanLabs/RTFGeneration.git"""; exit 1; }

    echo "Running migrations..."
    yes | $YII migrate up --migrationPath=application.modules.RTFGeneration.migrations >> $WROOT/protected/runtime/application.log 2>&1
    echo "Migration successful"

    echo "Enabling rtf generation module..."
    sed -i "s#//'RTFGeneration',#'RTFGeneration',#" $WROOT/protected/config/local/common.php
    echo "Enabled rtf generation module"
else
    ## Disable rtf module
    echo "Removing Java..."
    apt-get purge default-jre -y >/dev/null 2>&1 &&
    apt-get autoremove -y >/dev/null 2>&1 &&
    echo "Removed Java" ||
    echo "Cannot remove Java or it is not installed"

    if [ $delete = 1 ]; then
        echo "Deleting rtf module files..."
        rm -rf $WROOT/protected/modules/RTFGeneration
        echo "Deleted rtf module files"
    fi

    echo "Undoing migrations..."
    yes | $YII migrate down --migrationPath=application.modules.RTFGeneration.migrations  >> $WROOT/protected/runtime/application.log 2>&1
    echo "Migrations are undone"

	echo "Disabling rtf generation module..."
    if grep -q "//'RTFGeneration'" $WROOT/protected/config/local/common.php 2>/dev/null ; then
        echo "rtf generation already disabled"
    else
        sed -i "s#'RTFGeneration',#//'RTFGeneration',#" $WROOT/protected/config/local/common.php
        echo "Disabled rtf generation module"
    fi
fi