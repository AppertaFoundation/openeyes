#!/bin/bash -l

set -o pipefail

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

## Process command parameters
quiet=0
showhelp=0
ignorewarnings=0
connectionstring="--connectionID=db"

while [[ $# -gt 0 ]]; do
	p="$1"

	case $p in
	--quiet | -q)
		quiet=1
		;;
	--help)
		showhelp=1
		;;
	--ignore-warnings)
		ignorewarnings=1
		;;
	--connectionID)
		connectionstring="--connectionID=$2"
		shift
		;;
	*)
		echo "Unknown command line: $p"
		;;
	esac
	shift # move to next parameter
done

# Show help text
if [ $showhelp = 1 ]; then
	echo ""
	echo "DESCRIPTION:"
	echo "Migrates database to latest schema"
	echo ""
	echo "usage: $0 [--help] [--quiet | -q] [--connectionID conn]"
	echo ""
	echo "COMMAND OPTIONS:"
	echo ""
	echo "  --help           : Show this help"
	echo "  --quiet | -q     : Do not show console output"
	echo "  --connectionID   : Apply migrations to a secondary configured database"
	echo "  --ignore-warnings: Don't break on warnings"
	echo ""
	exit 1
fi

touch $WROOT/protected/runtime/migrate.log

# disable log to browser during migrate, otherwise it can cause extraneous trace output on the CLI
export LOG_TO_BROWSER=""

migratelog="$WROOT/protected/runtime/migrate.log"

# Show output on screen AND write to log
outputredirectnew="2>&1 | tee ${migratelog}"
outputredirectappend="2>&1 | tee -a ${migratelog}"

if [ $quiet -eq 1 ]; then
	# write to log only
	outputredirectnew=">${migratelog}"
	outputredirectappend=">>${migratelog}"
fi

founderrors=0

if ! eval "php $WROOT/protected/yiic migrate --interactive=0 $outputredirectnew"; then
	founderrors=1
fi

if [ $founderrors -ne 1 ] && ! eval "php $WROOT/protected/yiic migratemodules --interactive=0 $outputredirectappend"; then
	founderrors=1
fi

if [ $ignorewarnings = "0" ]; then
	if grep -i 'error\|exception.[^al]\|warning\*' $WROOT/protected/runtime/migrate.log; then
		founderrors=1
	fi
else
	if grep -i 'error\|exception.[^al]' $WROOT/protected/runtime/migrate.log; then
		founderrors=1
	fi
fi

if [ $founderrors = 1 ]; then
	printf "\n\e[5;41;1m\n\nMIGRATE ENCOUNTERED ERRORS - PLEASE SEE LOG - $WROOT/protected/runtime/migrate.log\n\n\n \e[0m\n"
	echo "The following migration errors were encountered:"
	grep -B 2 -A 7 -in 'error\|exception.[^al]\|warning\*' $WROOT/protected/runtime/migrate.log
	printf "\n\nTo continue with the reset of the script, select option 1"
	echo "To exit, select option 2"

	printf "\e[41m\e[97m  MIGRATE ERRORS ENCOUNTERED  \e[0m \n"
	echo ""

	select yn in "Continue" "Exit"; do
		case $yn in
		Continue)
			echo "

Continuing. System is in unknown state and further errors may be encountered...

			"
			break
			;;
		Exit)
			echo "
Exiting. Please fix errors and try again...
			"
			exit 1
			;;
		esac
	done

elif
	grep -q "applied" $WROOT/protected/runtime/migrate.log >/dev/null
then
	echo "Migrations applied - see $WROOT/protected/runtime/migrate.log for more details"
else
	echo "No new migrations to apply - see $WROOT/protected/runtime/migrate.log for more details"
fi
