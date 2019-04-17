#!/bin/bash -l


# Check that we are running from the /tmp folder. If not, exit
SOURCE="${BASH_SOURCE[0]}"
while [ -h "$SOURCE" ]; do # resolve $SOURCE until the file is no longer a symlink
  DIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"
  SOURCE="$(readlink "$SOURCE")"
  [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE" # if $SOURCE was a relative symlink, we need to resolve it relative to the path where the symlink file was located
done
# Determine root folder for site - all relative paths will be built from here
DIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"

if [ "$DIR" != "/tmp" ]; then
    echo "Must be run from /tmp directory. Use oe-checkout.sh instead"
    exit 1
fi

# Process commandline parameters
SCRIPTROOT="" # will be passed in from oe-checkout.sh
WROOT="" # will be passed in from oe-checkout.sh
force=0
killmodules=0
resetconfig=0
killconfigbackup=0
migrate=1
fix=1
compile=1
nosummary=0
# Set default branch from environment. Else, if in LIVE mode, fall back to master branch. otherwise fallback to develop branch
defaultbranch=$OE_DEFAULT_BRANCH
if [ -z $defaultbranch ]; then
    [ "$OE_MODE" = "LIVE" ] && defaultbranch="master" || defaultbranch="develop"
fi
branch=$defaultbranch
sshuserstring="git"
fixparams=""
showhelp=0
sample=0
sampleonly=0
usessh=""
changesshid=0

# parse SCRIPTDIR and WROOT first. Strip from list of params
PARAMS=()
while [[ $# -gt 0 ]]
do
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

# Read in stored git config and modules config
source $SCRIPTDIR/git.conf
source $SCRIPTDIR/modules.conf

# store original ssh value, needed for updating remotes during pull
previousssh=$usessh

if [ -z "$1" ]; then showhelp=1; fi

PARAMS=()
while [[ $# -gt 0 ]]
do
    p="$1"

    case $p in
    	-f|-force|--force) force=1
    		## Force will ignore any uncomitted changes and checkout over the top
    	;;
    	-fc|--reset-config) resetconfig=1; fixparams="$fixparams --reset-config"
    	## remove local config files and either restore from backup (if available) or reset to sample configuration
        ;;
    	-fff) force=1; killmodules=1; killconfigbackup=1
    		## killmodules should only be used when moving backwards from versions 1.12.1 or later to version 1.12 or earlier - removes the /protected/modules folder and re-clones all modules
    	;;
    	-ffc) resetconfig=1; killconfigbackup=1; fixparams="$fixparams --reset-config"
    	## Delete backups and reset config
    	;;
    	--delete-backup) killconfigbackup=1
    	## Delete configuration backups from /etc/openeyes
    	;;
    	--develop|--d|-d) defaultbranch=develop
    		## develop will use develop baranches when the named branch does not exist for a module
    	;;
        --master|--m|-m) defaultbranch=master
    		## will use master baranches when the named branch does not exist for a module
    	;;
    	--nomigrate|--no-migrate|--n|-n|-nm) fixparams="$fixparams --no-migrate"
    		## nomigrate will prevent database migrations from running automatically at the end of checkout
    	;;
    	--root|-r|--r|--remote)
            gitroot=$2
            shift #shift pass parameter
    	   ;;
    	--no-summary) nosummary=1
    		## don't show summary of checkout at completion
    	   ;;
    	--no-fix) fix=0
    		## don't run oe-fix at completion
    	;;
    	--no-pull|--nopull) nopull=1
    		## Do not issue git pull after checkout
    	;;
    	--no-compile) compile=0
    		## don't compile java
    	;;
        --help) showhelp=1
        ;;
    	--sample) sample=1
    	;;
    	--sample-only) sampleonly=1
    	;;
        --no-oe) # Don't checkout the openeyes repo
            delete=(openeyes)
            modules=( "${modules[@]/$delete}" ) # removes openeyes from modules list
        ;;
    	*)  if [ ! -z "$1" ]; then
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
if  [ ${#PARAMS[@]} -gt 0 ]; then
    echo "Unknown Parameters:"
    for i in "${PARAMS[@]}"
    do
        echo $i
    done
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
	echo ""
    exit 1
fi

# Check that SCRIPTROOT and WROOT are set appropriately
if [ "$SCRIPTDIR" = "" ] || [ "$SCRIPTDIR" = "setme" ] || [ "$WROOT" = "" ] || [ "$WROOT" = "" ]; then
    echo "Directories not set correctly. Please use oe-checkout.sh"
fi

echo ""
echo "Checking out branch $branch..."
echo ""

$(ssh-agent)  2>/dev/null

# attempt ssh authentication. If it fails, revert to https
ssh git@github.com -T
[ "$?" == "1" ] && usessh=1 || usessh=0

# Backwards comaptibility, use OE_GITROOT if it exists and GIT_ORG if not
# If both exist, GIT_ORG takes preference
[ ! -z "$OE_GITROOT" ] && GIT_ORG=${GIT_ORG:-$OE_GITROOT}

# If GIT_ORG is not specified then - If using https we defualt to appertafoundation. If using ssh we default to openeyes
[ -z "$GIT_ORG" ] && { [ "$usessh" == "0" ] && gitroot="appertafoundation" || gitroot="openeyes";} || gitroot=$GIT_ORG

# Set the base string for SSH or HTTP accordingly
[ "$usessh" == "1" ] && basestring="git@github.com:$gitroot" || basestring="https://github.com/$gitroot"

# store git settings out to disk
echo "usessh=$usessh" | sudo tee $SCRIPTDIR/git.conf > /dev/null

# Set to cache password in memory (should only ask once per day or each reboot)
git config --global credential.helper 'cache --timeout=86400'

git config --global core.fileMode false 2>/dev/null

MODULEROOT=$WROOT/protected/modules

git config core.fileMode false 2>/dev/null

# Add sample DB to checkout if it exists or if --sample has been set
if [[ -d "$MODULEROOT/sample" ]] || [[ $sample = 1 ]]; then modules=(${modules[@]} sample); fi

# if in sample only mode, we want only the sample module and nothing else
if [ $sampleonly = 1 ]; then modules=(sample); javamodules=(); fi

######################################################
# update remote if changing from https to ssh method #
######################################################
if [ ! "$usessh" == "$previousssh" ]; then

	for module in ${modules[@]}; do
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
				git -C $MODGITROOT remote set-url origin $basestring/$module.git

			  fi
	done

fi
##### END update remote #####

if [ ! "$force" = "1" ]; then
    echo ""
	echo "checking for uncommited changes"

	  changes=0
	  modulelist=""

	  for module in ${modules[@]}; do
		if [ ! -d "$MODULEROOT/$module" ]; then
			if [ ! "$module" = "openeyes" ]; then printf "\e[31mModule $module not found\e[0m\n"
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
		printf "\e[41m\e[97m  CHECKOUT ABORTED  \e[0m \n"
		echo "There are uncommitted changes in the following modules: $modulelist"
		printf "To ignore these changes, run: \e[1m oe-checkout.sh $branch -f \e[0m \n"
		echo "Alternatively, manually git reset --hard to ignore, or git stash to keep, etc"
		printf "\e[41m\e[97m  CHECKOUT ABORTED  \e[0m \n";
		echo ""
		exit 1
	  fi
fi

# make sure modules directory exists
mkdir -p $MODULEROOT

for module in ${modules[@]}; do

  # Determine if module already exists (ignoring openeyes). If not, clone it
	if [ ! -d "$MODULEROOT/$module" ] && [ "$module" != "openeyes" ]; then

        printf "\e[32m$module: Doesn't currently exist - cloning from : ${basestring}/${module}.git \e[0m"

        git -C $MODULEROOT clone ${basestring}/${module}.git $module
	fi

	processgit=1

	# deal with openeyes not being a real module!
	if [ "$module" = "openeyes" ]; then MODGITROOT=$WROOT; else MODGITROOT=$MODULEROOT/$module; fi

	if [ ! -d "$MODGITROOT/.git" ]; then processgit=0; fi

	if [ $processgit = 1 ]; then
		printf "\e[32m$module: \e[0m"
		git -C $MODGITROOT reset --hard
		git -C $MODGITROOT fetch --all

		if ! git -C $MODGITROOT checkout tags/$branch 2>/dev/null; then
		      if ! git -C $MODGITROOT checkout $branch 2>/dev/null; then
                echo "no branch $branch exists, switching to $defaultbranch"
                git -C $MODGITROOT checkout $defaultbranch 2>/dev/null
            fi
        fi

		## fast forward to latest head
		if [ ! "$nopull" = "1" ]; then
			echo "Pulling latest changes: "
			git -C $MODGITROOT pull
			git -C $MODGITROOT submodule update --init --force
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

echo ""
