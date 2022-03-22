#!/bin/bash -l

# Shows which branch each module is currently using

## NOTE: This script assumes it is in protected/scripts. If you move it then relative paths will not work!

# Find fuill folder path where this script is located, then find root folder
SOURCE="${BASH_SOURCE[0]}"
while [ -h "$SOURCE" ]; do # resolve $SOURCE until the file is no longer a symlink
    DIR="$(cd -P "$(dirname "$SOURCE")" && pwd)"
    SOURCE="$(readlink "$SOURCE")"
    [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE" # if $SOURCE was a relative symlink, we need to resolve it relative to the path where the symlink file was located
done
# Determine root folder for site - all relative paths will be built from here
SCRIPTDIR="$(cd -P "$(dirname "$SOURCE")" && pwd)"
WROOT="$(cd -P "$SCRIPTDIR/../../" && pwd)"

function gitbranch {
    branch=$(git -C $1 rev-parse --abbrev-ref HEAD)
    if [ "$branch" == "HEAD" ]; then
        branch=$(git -C $1 describe --all 2>/dev/null)
    fi
    echo $branch
}

# load in modules list
# if a custom config has been supplied (e.g, by a docker config) then use it, else use the default
[ -f "/config/modules.conf" ] && MODULES_CONF="/config/modules.conf" || MODULES_CONF="$SCRIPTDIR/modules.conf"
source $MODULES_CONF

# Ensure that openeyes and eyedraw modules are included (the easiest way to do this is delete if they already exist, then add)
delete=(openeyes)
modules=("${modules[@]/$delete/}") # removes openeyes from modules list
delete=(eyedraw)
modules=("${modules[@]/$delete/}") # removes eyedraw from modules list
modules=(openeyes eyedraw "${modules[@]}")

MODULEROOT=$WROOT/protected/modules

# TODO: Should be able to replace current modules.conf method with a recursive lookup using gitbranch function to determine which modules to display

# Check modules
if [ -d "$MODULEROOT/sample" ]; then modules=(${modules[@]} sample); fi # Add sample DB to list if it exists

# Note that the "%=*" removes any namespace definitions (i.e, given OphInTesme=\OEModule\OphInTestme\OphInTestme::class, it would only return OphInTestme)
for module in "${modules[@]%=*}"; do
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
        printf "\e[32m%-20s\e[39m-- $module\n" "$(gitbranch $MODGITROOT)"
    fi
done

printf "Done\n\n"
