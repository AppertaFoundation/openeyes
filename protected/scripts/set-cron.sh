#!/bin/bash -l

### Copies contents of .cron to /etc/cron.d and expands variables

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


# Copy each file in ./.cron to /etc/cron.d, expand variables and set source
shopt -s nullglob
for f in $(ls $SCRIPTDIR/.cron | sort -V)
do

  echo "importing $f"
  sed "s|\$SCRIPTDIR|$SCRIPTDIR|; s|\$WROOT|$WROOT|" $SCRIPTDIR/.cron/$f | sudo tee /etc/cron.d/$f &> /dev/null
  sudo chmod 0644 /etc/cron.d/$f

done
