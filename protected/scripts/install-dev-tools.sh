#!/bin/bash

## Installs and configures additional packages needed to build openeyes components
## In a docker environment, these can be installed on the developer machine to
## support running of the various oe-* helper scripts and for building eyedraw, etc.

## This script should work natively in debian based linux or Ubuntu on Windows
## Subsystem for Linux environments. Alternative scripts will be made available for
## MacOS and native Windows environments

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

sudo apt-get install -y $(<$SCRIPTDIR/.packages-dev.conf)

# Setup npm and compass (currently these are not needed in production environments)

sudo npm install -g npm

# Install grunt
echo "installing global npm dependencies"
sudo npm install -g grunt-cli

