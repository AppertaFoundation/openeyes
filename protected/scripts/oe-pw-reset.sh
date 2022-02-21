#!/bin/bash -l
## Resets various caches and configs

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

## NOTE: This script assumes it is in protected/scripts. If you move it then relative paths will not work!
#pass an argument of pw and it will also reset the password
if [ "$#" -gt "0" ]
then
    php $WROOT/protected/yiic resetuserlock --username='admin' --password="$1"
else 
    php $WROOT/protected/yiic resetuserlock --username='admin'
fi
echo ""
echo "...Done"
echo ""
