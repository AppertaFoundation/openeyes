#!/bin/bash

# Check that we are running from the /tmp folder. If not, exit
SOURCE="${BASH_SOURCE[0]}"
while [ -h "$SOURCE" ]; do # resolve $SOURCE until the file is no longer a symlink
    DIR="$(cd -P "$(dirname "$SOURCE")" && pwd)"
    SOURCE="$(readlink "$SOURCE")"
    [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE" # if $SOURCE was a relative symlink, we need to resolve it relative to the path where the symlink file was located
done
# Determine root folder for site - all relative paths will be built from here
DIR="$(cd -P "$(dirname "$SOURCE")" && pwd)"

if [ "$DIR" != "/tmp" ]; then
    echo "Must be run from /tmp directory. Use oe-update.sh instead"
    exit 1
fi

git config core.fileMode false 2>/dev/null

# Process commandline parameters
SCRIPTROOT="" # will be passed in from oe-checkout.sh
WROOT=""      # will be passed in from oe-checkout.sh

# parse SCRIPTDIR and WROOT first. Strip from list of params
PARAMS=()
while [[ $# -gt 0 ]]; do
    p="$1"

    case $p in
    -sd) # Set SCRIPTDIR
        SCRIPTDIR=$2
        shift
        shift
        ;;
    -wr) # Set WROOT
        WROOT=$2
        shift
        shift
        ;;
    *) # add everything else to the params array for processing in the next section
        PARAMS+=("$1")
        shift
        ;;
    esac
done
set -- "${PARAMS[@]}" # restore positional parameters

force=0
migrate=1
fix=1
compile=1
nosummary=0
fixparams=""
showhelp=0
ignorelocal=0

# Read in stored github config (username, root, usessh, etc)
source $SCRIPTDIR/git.conf 2>/dev/null

for i in "$@"; do
    case $i in
    -f | -force | --force)
        force=1
        ## Force will ignore any uncomitted changes and checkout over the top
        ;;
    --nomigrate | --no-migrate | --n | -n)
        fixparams="$fixparams --no-migrate"
        ## nomigrate will prevent database migrations from running automatically at the end of checkout
        ;;
    --no-summary)
        nosummary=1
        ## don't show summary at completion
        ;;
    --no-fix)
        fix=0
        ## don't run oe-fix at completion
        ;;
    --no-compile)
        fixparams="$fixparams --no-compile"
        ## don't compile java
        ;;
    -y | --ignore-local)
        ignorelocal=1
        ## ignore local modifications and attempt to update
        ;;
    --help)
        showhelp=1
        ;;
    *)
        if [ ! -z "$i" ]; then
            # pass anything else on to oe-fix script
            fixparams="$fixparams $i"
        fi
        ;;
    esac
done

# Show help text
if [ $showhelp = 1 ]; then
    echo ""
    echo "DESCRIPTION:"
    echo "Updates all modules to latest version (of current branch)"
    echo ""
    echo "usage: oe-update [--help] [--force | -f] [--no-migrate | -n] [--no-compile] [--no-summary]"
    echo ""
    echo "COMMAND OPTIONS:"
    echo "  --help         : Display this help text"
    echo "  --no-migrate "
    echo "          | -n   : Prevent database migrations running automatically after"
    echo "                   update"
    echo "  --force | -f   : forces the checkout, even if local changes are uncommitted"
    echo "  --ignore-local"
    echo "            | -y : ignore local modifications and attempt to pull anyway"
    echo "  --no-compile   : Do not complile java modules after Checkout"
    echo "  --no-summary   : Do not display a summary of the checked-out modules after "
    echo "                   completion"
    echo ""
    exit 1
fi

# Check that SCRIPTROOT and WROOT are set appropriately
if [ "$SCRIPTDIR" = "" ] || [ "$SCRIPTDIR" = "setme" ] || [ "$WROOT" = "" ] || [ "$WROOT" = "" ]; then
    echo "Directories not set correctly. Please use oe-checkout.sh"
fi

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
if [ -d "$MODULEROOT/sample" ]; then modules=(${modules[@]} sample); fi # Add sample DB to list if it exists

if [ "$force" = 0 ]; then
    echo ""
    echo "checking for uncommited changes"

    changes=0
    modulelist=""

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

            if ! git -C $MODGITROOT diff --quiet; then
                changes=1
                modulelist="$modulelist $module"
            fi
        fi

    done

    #  If we have unstaged changes, then abort and warn which modules are affected
    if [ "$changes" = "1" ]; then
        printf "\e[41m\e[97m  WARNING  \e[0m \n"
        echo "There are uncommitted changes in the following modules: $modulelist"
        echo "To continue and attempt to merge, select option 1"
        echo "To cancel and review changes, select option 2"
        printf "To discard these changes, run: \e[1m oe-update.sh -f \e[0m \n"
        echo "Alternatively, manually git reset --hard to ignore, or git stash to keep, etc"
        printf "\e[41m\e[97m  WARNING  \e[0m \n"
        echo ""

        if [ "$ignorelocal" = "0" ]; then
            select yn in "Continue" "Cancel"; do
                case $yn in
                Continue)
                    echo "

    Continuing update and attempting to merge...
    If errors are encounted, you will need to fix manually or use oe-update - f to discard local changes

                    "
                    accept="1"
                    break
                    ;;
                Cancel)
                    echo "
    Cancelling...
                    "
                    exit 1
                    ;;
                esac
            done
        else
            echo "--ignore-local switch applied - continuing"
        fi
    fi
fi

# Pull all modules (including openeyes)
# Note that the "%=*" removes any namespace definitions (i.e, given OphInTesme=\OEModule\OphInTestme\OphInTestme::class, it would only return OphInTestme)
for module in "${modules[@]%=*}"; do

    # Determine if module already exists (ignoring openeyes). If not, clone it
    if [ ! -d "$MODULEROOT/$module" ] && [ "$module" != "openeyes" ]; then

        printf "\e[31m$module: Doesn't currently exist - Please clone it first - or use oe-checkout.sh\e[0m\n"
        echo "Quitting..."
        exit 1
    fi

    processgit=1

    # deal with openeyes not being a real module!
    if [ "$module" = "openeyes" ]; then MODGITROOT=$WROOT; else MODGITROOT=$MODULEROOT/$module; fi

    if [ ! -d "$MODGITROOT/.git" ]; then processgit=0; fi

    if [ $processgit = 1 ]; then
        printf "\e[32m$module: \e[0m"
        if [ "$force" = "1" ]; then
            echo "Resetting. Any uncomitted changes have been lost..."
            git -C $MODGITROOT reset --hard
        fi
        git -C $MODGITROOT pull
        git -C $MODGITROOT submodule update --init --force
    fi

done

# update composer
sudo -E composer self-update

# Now reset/relink various config files etc
if [ "$fix" = "1" ]; then bash $SCRIPTDIR/oe-fix.sh $fixparams; fi

printf "\e[42m\e[97m  UPDATE COMPLETE  \e[0m \n"
if [ ! "$nosummary" = 1 ]; then bash $SCRIPTDIR/oe-which.sh; fi
echo ""
