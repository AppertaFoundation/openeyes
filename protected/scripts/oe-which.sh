#!/bin/bash -l

# Shows which branch each module is currently using

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

function gitbranch {

  if ! branch=$(git -C $MODGITROOT rev-parse --abbrev-ref HEAD 2>/dev/null); then
    branch="";
  else
    if [ "$branch" = "HEAD" ]; then branch=$(git -C $MODGITROOT describe --all 2>/dev/null); fi
    if [ ! "$branch" = "" ]; then echo $branch;
    fi
  fi
}

# load in modules list
source $SCRIPTDIR/modules.conf

MODULEROOT=$WROOT/protected/modules

# TODO: Should be able to replace current modules.conf method with a recursive lookup using gitbranch function to determine which modules to display

# Check modules
if [ -d "$MODULEROOT/sample" ]; then modules=(${modules[@]} sample); fi # Add sample DB to list if it exists

for module in ${modules[@]}; do
	if [ ! -d "$MODULEROOT/$module" ]; then
		if [ ! "$module" = "openeyes" ]; then
            printf "\e[31mModule $module not found\e[0m\n"
            break
        fi
	fi
    # deal with openeyes not being a real module!
    if [ "$module" = "openeyes" ]; then MODGITROOT=$WROOT; else MODGITROOT=$MODULEROOT/$module; fi

    # check if this is a git repo (and exists)
    if [ -d "$MODGITROOT/.git" ]; then
	  b="$(gitbranch)"
	  printf "\e[32m%-20s\e[39m-- $module\n" $b
    fi
done

printf "Done\n\n"
