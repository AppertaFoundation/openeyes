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

noenvs=0

while [[ $# -gt 0 ]]
do
    p="$1"

    case $p in
	    --no-envs) noenvs=1
        ;;
	    *)  [ ! -z $p ] && echo "Unknown command line: $p" || :
        ;;
    esac

shift # move to next parameter
done


# Copy each file in ./profile.d to /etc/profile.d, expand variables and set source
shopt -s nullglob
for f in $(ls $SCRIPTDIR/profile.d | sort -V)
do
  # If we're not updaing envs, then skip that file
  [[ $noenvs = 1 && $f == openeyes-env.sh ]] && continue || :

  if [[ $f == *.sh ]]; then
    echo "importing $f"
    sed "s|\$SCRIPTDIR|$SCRIPTDIR|; s|\$WROOT|$WROOT|" $SCRIPTDIR/profile.d/$f | sudo tee /etc/profile.d/$f &> /dev/null
    sudo chmod 755 /etc/profile.d/$f
    source /etc/profile.d/$f
  fi

done

echo -e "\n* New settings will be available on next login *\n"
