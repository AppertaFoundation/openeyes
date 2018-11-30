#!/bin/bash

### Copies contents of profile.d to /etc/profile.d and expands variables

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

shopt -s nullglob
for f in $(ls $SCRIPTDIR | sort -V)
do
  echo "importing $f"
  # Use sed to expand variable for home dir
  sed "s|\$SCRIPTDIR|$SCRIPTDIR|" $f | sudo tee /etc/profile.d/$f
  sudo chmod 755 /etc/profile.d/$f
done
