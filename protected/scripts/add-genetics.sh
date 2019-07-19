#!/bin/bash

## adds or removes genetics modules

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

# Test command parameters
remove=0
showhelp=0

for i in "$@"
do
case $i in
	--remove|-remove|-r|-uninstall|--uninstall|-u|--disable|-disable) remove=1
	;;
    --help) showhelp=1
    ;;
	*)  echo "Unknown command line: $i"
    ;;
esac
done

# Show help text
if [ $showhelp = 1 ]; then
    echo ""
    echo "DESCRIPTION:"
    echo "Adds or removes the genetics modules. NOTE: You must have built from the V2.0 sample config for this to work"
    echo ""
    echo "usage: $0 [--remove ] [--help] "
    echo ""
    echo "COMMAND OPTIONS:"
	echo ""
	echo "  --help         : Show this help"
    echo "  --remove       : Remove the genetics modules (default it to add)"
	echo "                   NOTE: This will not remove any database migrations"
    echo "                   or data"
	echo ""
    exit 1
fi

if [ ! $remove = 1 ]; then
# Enable genetics modules
    echo "enabling genetics modules..."

    sudo sed -i "s#/\*'Genetics',#'Genetics',#" $WROOT/protected/config/local/common.php
    sudo sed -i "s#'OphInGeneticresults',\*/#'OphInGeneticresults',#" $WROOT/protected/config/local/common.php
else
    ## Disable genetics modules
	echo "disabling genetics modules..."
    if grep -q "/\*'Genetics'" $WROOT/protected/config/local/common.php 2>/dev/null ; then
        echo "genetics already disabled"
    else
        sudo sed -i "s#'Genetics',#/\*'Genetics',#" $WROOT/protected/config/local/common.php
    fi

    if grep -q "'OphInGeneticresults',\*/" $WROOT/protected/config/local/common.php 2>/dev/null ; then
        echo ""
    else
        sudo sed -i "s#'OphInGeneticresults',#'OphInGeneticresults',\*/#" $WROOT/protected/config/local/common.php
    fi
fi