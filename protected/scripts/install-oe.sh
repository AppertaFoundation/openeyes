#!/bin/bash

# First makes a temporary copy of the runinstall.sh script and then calls it
# Calling directly from inside the scripts folder could case the script to be
# overwriten by the checkout before it completes!

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

# Pass the script and root directories to the runinstall script
params="-sd $SCRIPTDIR -wr $WROOT"

# parse all other CLI parameters to pass to runcheckout script
for i in "$@"
do
    params="$params $i"
done

# Copy the runinstall script to /tmp, make it executeable and then run
sudo mkdir -p /tmp && sudo cp $SCRIPTDIR/runinstall.sh /tmp/
sudo chown "$USER":www-data /tmp/runinstall.sh
sudo chmod 774 /tmp/runinstall.sh

bash /tmp/runinstall.sh $params
