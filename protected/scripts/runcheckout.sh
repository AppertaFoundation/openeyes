#!/bin/bash -l

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
    echo "Must be run from /tmp directory. Use oe-checkout.sh instead"
    exit 1
fi

# Process commandline parameters
SCRIPTDIR="" # will be passed in from oe-checkout.sh
WROOT=""     # will be passed in from oe-checkout.sh
force=0
resetconfig=0
fix=1
nosummary=0
# Set default branch from environment. Else, if in LIVE mode, fall back to master branch. otherwise fallback to develop branch
defaultbranch=$OE_DEFAULT_BRANCH
if [ -z $defaultbranch ]; then
    [[ "${OE_MODE^^}" = "LIVE" || "${OE_MODE^^}" = "TEST" ]] && defaultbranch="master" || defaultbranch="develop"
fi
branch="$defaultbranch"
fixparams=""
nopull=0
showhelp=0
sample=0
usessh=""
cloneparams=""
fetchparams=""
depth="2000" # by default always shallow clone to this depth (use git fetch --unshallow to revert to full depth after)
mergebranch=""
mergefailed=0
unshallowsample=0

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

## Read in stored git config and modules config
source "$SCRIPTDIR"/git.conf 2>/dev/null
# if a custom config has been supplied (e.g, by a docker config) then use it, else use the default
[ -f "/config/modules.conf" ] && MODULES_CONF="/config/modules.conf" || MODULES_CONF="$SCRIPTDIR/modules.conf"
source "$MODULES_CONF"

# Set the modules path
MODULEROOT=$WROOT/protected/modules

# store original ssh value, needed for updating remotes during pull
previousssh=$usessh

if [ -z "$1" ]; then showhelp=1; fi

PARAMS=()
while [[ $# -gt 0 ]]; do
    p="$1"

    case $p in
    -f | -force | --force)
        force=1
        ## Force will ignore any uncomitted changes and checkout over the top
        ;;
    -fc | --reset-config)
        resetconfig=1
        fixparams="$fixparams --reset-config"
        ## remove local config files and either restore from backup (if available) or reset to sample configuration
        ;;
    -fff)
        force=1
        ;;
    -ffc)
        resetconfig=1
        fixparams="$fixparams --reset-config"
        ## Delete backups and reset config
        ;;
    --develop | --d | -d)
        defaultbranch=develop
        ## develop will use develop baranches when the named branch does not exist for a module
        ;;
    --master | --m | -m)
        defaultbranch=master
        ## will use master baranches when the named branch does not exist for a module
        ;;
    --merge) #merge an upstream branch after checkout
        mergebranch=$2
        shift
        ;;
    --nomigrate | --no-migrate | --n | -n | -nm)
        fixparams="$fixparams --no-migrate"
        ## nomigrate will prevent database migrations from running automatically at the end of checkout
        ;;
    --root | -r | --r | --remote)
        gitroot=$2
        shift #shift pass parameter
        ;;
    --no-summary)
        nosummary=1
        ## don't show summary of checkout at completion
        ;;
    --no-fix)
        fix=0
        ## don't run oe-fix at completion
        ;;
    --no-pull | --nopull)
        nopull=1
        ## Do not issue git pull after checkout
        ;;
    --help)
        showhelp=1
        ;;
    --sample)
        sample=1
        ;;
    --no-sample)
        sample=-1
        ;;
    --sample-only)
        # if in sample only mode, we want only the sample module and nothing else
        modules=(sample)
        ;;
    --no-oe) # Don't checkout the openeyes repo
        delete=(openeyes)
        modules=("${modules[@]/$delete/}") # removes openeyes from modules list
        ;;
    --depth)
        depth="$2"
        shift
        ;;
    --single-branch)
        cloneparams="$cloneparams --single-branch"
        ;;
    --unshallow-sample)
        unshallowsample=1
        ;;
    *)
        if [ -n "$1" ]; then
            if [ "$branch" == "$defaultbranch" ]; then
                branch=$1
            else
                # add everything else to the params array for processing later
                PARAMS+=("$1")
            fi
        fi
        ;;
    esac

    shift # move to next parameter
done

# List out any unkown parameters
if [ ${#PARAMS[@]} -gt 0 ]; then
    echo "Unknown Parameters:"
    for i in "${PARAMS[@]}"; do
        echo "$i"
    done
    echo "continuing in 5 seconds..."
    sleep 5
fi

# Show help text
if [ $showhelp = 1 ]; then
    echo ""
    echo "DESCRIPTION:"
    echo "Checks-out all modules of a specified branch. If a module does not exist locally then it will be cloned"
    echo ""
    echo "usage: $0 <branch> [--help] [--force | -f] [--no-migrate | -n] [--kill-modules | -ff ] [--no-compile] [--no-pull] [-r <remote>] [--no-summary] [--develop | -d] [-u<username>]  [-p<password>]"
    echo ""
    echo "COMMAND OPTIONS:"
    echo "  <branch>       : Checkout / clone the specified <branch>"
    echo "  --help         : Display this help text"
    echo "  --no-migrate "
    echo "          | -n   : Prevent database migrations running automatically after"
    echo "                   checkout"
    echo "	--no-pull		: Prevent automatic fast-forward to latest remote head"
    echo "  --force | -f   : forces the checkout, even if local changes are uncommitted"
    echo "	--reset-config "
    echo "		| -fc	   : Reset config/local/common.php to default settings"
    echo "				   : WARNING: Will destroy existing config"
    echo "  --delete-backup : Deletes backups from /etc/openeyes. Use in "
    echo "					  conjunction with --reset-config to fully reset config"
    echo "  --no-compile   : Do not complile java modules after Checkout"
    echo "  -r <remote>    : Use the specifed remote github fork - defaults to openeyes"
    echo "  --develop "
    echo "           |-d   : If specified branch is not found, fallback to develop branch"
    echo "                   - default woud fallback to master"
    echo "  --no-summary   : Do not display a summary of the checked-out modules after "
    echo "                   completion"
    echo "  --depth <int>  : Only clone/fetch to the given depth"
    echo "  --merge <branch> : Perform a merge of the given upstream branch into the checked-out code"
    echo "  --unshallow-sample : By default, the sample modile always uses a depth of 1. Setting this flag will use the same depth as all other modules"
    echo ""
    exit 1
fi

# Check that SCRIPTROOT and WROOT are set appropriately
if [ "$SCRIPTDIR" = "" ] || [ "$SCRIPTDIR" = "setme" ] || [ "$WROOT" = "" ] || [ "$WROOT" = "" ]; then
    echo "Directories not set correctly. Please use oe-checkout.sh"
fi

#######################################################################################
## Add sample module to checkout if it pre-exists exists or if --sample has been set ##
#######################################################################################
# first expression checks that sample module is not already in the sample list
if [[ $(printf '%s\n' "${modules[@]}" | grep -P '^sample$' >/dev/null 2>&1)$? -eq 1 ]]; then
    # If sample is not already in the modules list, then add it if either the sample folder exists or if sample=1 is explicitly set
    if [[ (-d "$MODULEROOT/sample" && $sample -eq 0) || ($sample -eq 1) ]]; then
        echo -e "\nAdding sample module..."
        modules=("${modules[@]}" sample)
    fi
fi

echo ""
echo "Checking out branch $branch..."
echo "for modules:"
printf '%s\n' "${modules[@]}"
echo ""

if [ -n "$cloneparams" ]; then
    echo "Using the following parameters for clone:"
    echo "${cloneparams}"
    echo ""
fi

if [ -n "$depth" ]; then
    echo "To a depth of: $depth"
fi

if [ $nopull -eq 1 ]; then
    echo -"Will not pull latest after checkout"
fi

if [ $fix -eq 0 ]; then
    echo "Will not run oe-fix after checkout"
elif [ -n "$fixparams" ]; then
    echo "Using the following parameters for oe-fix:"
    echo "${fixparams}"
    echo ""
fi

if [ $force -eq 1 ]; then
    echo "Forcing reset - any uncomitted changes will be lost"
fi

echo -e "\nWill fallback to $defaultbranch if $branch does not exist"

if [[ -n "$mergebranch" && "$mergebranch" != "$branch" ]]; then
    echo "Will merge with $mergebranch after checkout"
fi

echo ""

ssh-agent >/dev/null 2>&1

testgit=$(ssh git@github.com -T 2>&1 | grep -oP --color=never "Hi \K[^\!]*")
if [ -n "$testgit" ]; then
    usessh=1
    echo "AUTHENTICATED TO GITHUB WITH SSH AS: $testgit"
else
    usessh=0
    echo "!COULD NOT AUTHENTICATE TO GITHUB WITH SSH, FALLING BACK TO HTTPS!"
fi

# Backwards comaptibility, use OE_GITROOT if it exists and GIT_ORG if not
# If both exist, GIT_ORG takes preference
[ -n "$OE_GITROOT" ] && GIT_ORG=${GIT_ORG:-$OE_GITROOT}

# If GIT_ORG is not specified then - If using https we defualt to appertafoundation. If using ssh we default to openeyes
[ -z "$GIT_ORG" ] && { [ "$usessh" == "0" ] && gitroot="appertafoundation" || gitroot="openeyes"; } || gitroot=$GIT_ORG

# Set the base string for SSH or HTTP accordingly
[ "$usessh" == "1" ] && basestring="git@github.com:$gitroot" || basestring="https://github.com/$gitroot"

# store git settings out to disk
echo "usessh=$usessh" | sudo tee "$SCRIPTDIR"/git.conf >/dev/null

# Set to cache password in memory (should only ask once per day or each reboot)
git config --global credential.helper 'cache --timeout=86400'

# Set fileMode to false, to prevent windows machines removing the execute bit on checkin
git config --global core.fileMode false 2>/dev/null

######################################################
# update remote if changing from https to ssh method #
######################################################
if [ ! "$usessh" == "$previousssh" ]; then

    for module in "${modules[@]}"; do
        # only run if module exists
        if [ ! -d "$MODULEROOT/$module" ]; then
            if [ ! "$module" = "openeyes" ]; then
                break
            fi
        fi
        echo "updating remote for $module"

        # deal with openeyes not being a real module!
        if [ "$module" = "openeyes" ]; then MODGITROOT=$WROOT; else MODGITROOT=$MODULEROOT/$module; fi

        # check if this is a git repo (and exists)
        if [ -d "$MODGITROOT/.git" ]; then

            # change the remote to new basestring
            git -C "$MODGITROOT" remote set-url origin "$basestring"/"$module".git

        fi
    done

fi
##### END update remote #####

if [ ! "$force" = "1" ]; then
    echo ""
    echo "checking for uncommited changes"

    changes=0
    modulelist=""

    for module in "${modules[@]}"; do
        if [ ! -d "$MODULEROOT/$module" ]; then
            if [ ! "$module" = "openeyes" ]; then
                printf "\e[31mModule %s not found\e[0m\n" "$module"
                break
            fi
        fi

        # deal with openeyes not being a real module!
        if [ "$module" = "openeyes" ]; then MODGITROOT=$WROOT; else MODGITROOT=$MODULEROOT/$module; fi

        # check if this is a git repo (and exists)
        if [ -d "$MODGITROOT/.git" ]; then

            if ! git -C "$MODGITROOT" diff --quiet; then
                changes=1
                modulelist="$modulelist $module"
            fi
        fi

    done

    #  If we have unstaged changes, then abort and warn which modules are affected
    if [ "$changes" = "1" ]; then
        printf "\e[41m\e[97m  CHECKOUT ABORTED  \e[0m \n"
        echo "There are uncommitted changes in the following modules: $modulelist"
        printf "To ignore these changes, run: \e[1m oe-checkout.sh %s -f \e[0m \n" "$branch"
        echo "Alternatively, manually git reset --hard to ignore, or git stash to keep, etc"
        printf "\e[41m\e[97m  CHECKOUT ABORTED  \e[0m \n"
        echo ""
        exit 1
    fi
fi

# make sure modules directory exists
mkdir -p "$MODULEROOT"

for module in "${modules[@]}"; do
    [ -z $module ] && continue || : # ignore any empty modules is the array

    printf "\e[32m$module: \e[0m"

    # deal with openeyes not being a real module!
    if [ "$module" = "openeyes" ]; then MODGITROOT=$WROOT; else MODGITROOT=$MODULEROOT/$module; fi

    processgit=1
    moduledepth=$depth # allows override of the depth for specific modules (e.g, sample)
    nomodulepull=0     # can override nopull for this module only (used when dealing with tags)

    # override depth for sample module, to save downloading gigs of data,
    # can be overiden by specifying --unshallow-sample
    if [[ "$module" == "sample" && $unshallowsample -eq 0 ]]; then
        moduledepth=1
    fi

    ############################################
    ## Check if branch / tag exists on remote ##
    ## or local                               ##
    ############################################
    echo "testing for existence of remote tag/branch..."
    remoteexists=0
    nofetch=0
    trackbranch=$branch
    if git ls-remote --exit-code ${basestring}/${module}.git refs/tags/"$branch"; then
        echo "Found a tag named $branch."
        remoteexists=1
        trackbranch="tags/$branch"
        nomodulepull=1 # we don't need to do a pull if we're fetching a tag
    elif git ls-remote --exit-code ${basestring}/${module}.git refs/heads/"$branch"; then
        echo "Found a remote branch named $branch."
        remoteexists=1
        trackbranch="$branch"
        # check if branch exists locally - if so then we should not attempt to fetch it
        if [ -d "$MODGITROOT" ] && git -C $MODGITROOT show-ref --verify --quiet refs/heads/"$branch"; then
            nofetch=1
        fi

    else
        nomodulepull=1 # No point pulling if there is no remote to pull from
        # check if branch exists locally - if not, fallback to defaultbranch
        if [ -d "$MODGITROOT" ] && git -C $MODGITROOT show-ref --verify --quiet refs/heads/"$branch"; then
            trackbranch="$branch"
        else
            trackbranch="$defaultbranch"
        fi

        if [ "$trackbranch" != "branch" ]; then
            echo "No branch $branch was found. Falling back to $defaultbranch"
            git -C $MODGITROOT fetch origin $defaultbranch
        fi

    fi

    ##################################
    ##         Start Clone          ##
    ##################################

    # Determine if module already exists (ignoring openeyes). If not, clone it
    if [ ! -d "$MODULEROOT/$module" ] && [ "$module" != "openeyes" ]; then

        printf "Doesn't currently exist - cloning from : ${basestring}/${module}.git"

        # If doing a shallow clone, then make sure to add the branch name
        if [[ -n "$moduledepth" ]]; then
            cloneparams+=" --depth $moduledepth  --branch ${trackbranch#tags/}" # note that branch must be the last thing in the string
            echo "Attempting shallow clone of depth: $moduledepth"
        fi

        if ! git -C $MODULEROOT clone $cloneparams ${basestring}/${module}.git $module; then
            echo "UNABLE TO CLONE $module. Exiting.."
            exit 1
        fi

        nomodulepull=1
    fi

    ##################################
    ##          END Clone           ##
    ##################################

    if [ ! -d "$MODGITROOT/.git" ]; then processgit=0; fi

    if [ $processgit = 1 ]; then
        git -C $MODGITROOT reset --hard
        git -C $MODGITROOT config core.fileMode false 2>/dev/null

        # Make sure the fetch root is correct - for some reason it sometimes get set to a specific
        # branch (e.g, develop), and then matching to upstream branches will break
        git -C $MODGITROOT config remote.origin.fetch +refs/heads/*:refs/remotes/origin/*

        #####################################################################
        ##                            FETCH                                ##
        ##                                                                 ##
        ## Attempt to only fetch the necessary branch (for speedup).       ##
        ## If that fails then try fetching all.                            ##
        ## Note we must not fetch for our current branch                   ##
        ## And we don't need to fetch if the branch already exists locally ##
        #####################################################################

        if [ $remoteexists -eq 1 ] && [ $nofetch -eq 0 ]; then

            # Add depth if specified
            if [[ -n "$moduledepth" ]]; then
                fetchparams+=" --depth=$moduledepth"
                echo "Attempting shallow fetch of depth: $moduledepth"
            fi

            # Now we know the branch exists, we can try to shallow fetch it
            if [ "$(git -C $MODGITROOT branch --show-current)" != "$branch" ]; then
                if ! git -C $MODGITROOT fetch origin $trackbranch:$trackbranch $fetchparams; then
                    # if something goes wrong with the shallow clone, then fall back to a full fetch
                    echo "Could not do a shallow fetch. Fetching full tree instead..."
                    git -C $MODGITROOT fetch --all $fetchparams
                fi
            fi
        fi

        # Try to checkout the branch/tag
        if ! git -C $MODGITROOT checkout $trackbranch 2>/dev/null; then
            echo "Unable to checkout a branch named $trackbranch. It does not exist. Exiting..."
            exit 1
        fi

        ## fast forward to latest head
        if [[ $nopull -eq 0 && $nomodulepull -eq 0 ]]; then
            echo "Pulling latest changes: "
            git -C $MODGITROOT branch --set-upstream-to=origin/$trackbranch

            pullparams=""
            if [ -n "$moduledepth" ]; then
                pullparams="$pullparams --depth=$moduledepth origin $trackbranch"
                echo "Attempting shallow pull to depth: $moduledepth"
            fi
            git -C $MODGITROOT pull $pullparams
            git -C $MODGITROOT submodule update --init --force
        fi

        ## Attempt to merge in an upstream branch (except for sample db)
        mergefailed=0
        if [[ -n "$mergebranch" && "$module" != "sample" ]]; then
            exists_in_remote=$(git -C $MODGITROOT ls-remote --heads origin ${mergebranch})
            if [[ -n ${exists_in_remote} ]]; then
                echo "Attempting to merge $mergebranch...."
                if ! git -C $MODGITROOT pull origin $mergebranch --no-edit 2>/dev/null; then
                    printf "\n\n\e[5;41;1mUNABLE TO MERGE WITH origin/$mergebranch - ROLLING BACK... \e[0m\n\n"
                    git -C $MODGITROOT merge --abort 2>/dev/null
                    mergefailed=1
                else
                    printf "\n\e[42m\e[97m  SUCESSFULLY MERGED %s WITH origin/%s  \e[0m \n" "$module" "$mergebranch"
                fi
            else
                printf "\n\e[43;30m No branch origin/%s exists on remote for %s - Skipping merge \e[0m\n\n" "$mergebranch" "$module"
            fi
        fi
    fi

done

if [ "$resetconfig" = "1" ]; then
    echo "
WARNING: Resetting local config to defaults
    "
    sudo rm -rf $WROOT/protected/config/local/*.php
fi

# Now reset/relink various config files etc
[ "$fix" = "1" ] && bash $SCRIPTDIR/oe-fix.sh $fixparams || :

# Show summary of checkout
if [ ! "$nosummary" = "1" ]; then
    bash $SCRIPTDIR/oe-which.sh
    printf "\e[42m\e[97m  CHECKOUT COMPLETE  \e[0m \n"
fi

# Show final warning if a merge failed (only applicable when the --merge flag is set)
if [ $mergefailed -eq 1 ]; then
    printf "\n\e[5;41;1m  ONE OR MORE MODULES FAILED TO MERGE WITH orgin/$mergebranch - Check the logs above  \e[0m\n"
fi

echo ""
