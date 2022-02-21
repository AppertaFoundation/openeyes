#!/bin/bash -l
## Resets various caches and configs

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

testtorun=""
phpunitconfigxml="$WROOT/protected/tests/phpunit.xml"
phpunitpath="$WROOT/vendor/phpunit/phpunit/phpunit"
groupOrExclude=false

while [[ $# -gt 0 ]]; do
  p="$1"

  case $p in
  --configuration | -config)
    phpunitconfigxml="$2"
    shift
    ;;
  --group=* | --exclude-group=*)
    groupOrExclude=true
    testtorun="$testtorun $p"
    ;;
  *)
    testtorun="$testtorun $p"
    # pass all remaining commands to phpunit
    ;;
  esac

  shift # move to next parameter
done
status=1
if [ "$OE_TEST_NO_GROUP" != "1" ] && [ "$groupOrExclude" = false ] && [ -z $testtorun ]; then
  echo "***************************************************************************"
  echo "*** Running all tests sets.                                             ***"
  echo "*** To run specific sets, use the relevant --group or --exclude options ***"
  echo "***************************************************************************"

  echo ""
  echo "*** Starting tests with sample data... ***"
  echo ""
  eval php $phpunitpath --configuration $phpunitconfigxml --group=sample-data $testtorun
  status=$? # Remember output status from first run

  echo ""
  echo "*** Starting tests with fixtures... ***"
  echo ""
  if ! eval php $phpunitpath --configuration $phpunitconfigxml --exclude=sample-data,functional,undefined $testtorun; then
    status=1 # If tests fail, set output status to failed - otherwise let result from previous run fall through
  fi

else
  eval php $phpunitpath --configuration $phpunitconfigxml $testtorun
  status=$?
fi

exit $status
