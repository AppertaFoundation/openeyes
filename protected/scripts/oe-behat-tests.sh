#!/bin/bash -l
## Resets various caches and configs

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

testtorun="$WROOT/features/"
configxml="$WROOT/behat.yml"
behatpath="$WROOT/vendor/behat/behat/bin/behat"
otherparams=""

while [[ $# -gt 0 ]]
do
    p="$1"

    case $p in
        --configuration|-config|--config)
            configxml="$2"
            shift
            ;;
	    --test|-test)
            testtorun="$2"
            shift
            ;;
        *)  
            otherparams="$otherparams $p"
            # pass all remaining commands to phpunit
            ;;
    esac

shift # move to next parameter
done

# Check selenium server is available
maxseconds=60
statusurl=${SELENIUM_WD_HOST:-'http://host.docker.internal:4444/wd/hub'}/status
echo waiting $maxseconds for Selenium at "$statusurl" to become ready...
i=1
while ! curl -sSL "$statusurl" 2>&1 | jq -r '.value.ready' 2>&1 | grep "true" >/dev/null; do
    echo "Waiting for the Grid ($i)"
    sleep 1
    [ $i -eq $maxseconds ] && { echo "Unable to contact Selenium after $maxseconds seconds. Giving up..."; exit 1; } || :
    i=$((i+1))
done


echo -e "\nStarting behat tests with:\n CONFIG: $configxml ${otherparams:+\n ADDITIONAL PARAMETERS: $otherparams}\n TESTS: $testtorun${BEHAT_PARAMS:+\n BEHAT_PARAMS: $BEHAT_PARAMS}\n\n"
eval $behatpath --config $configxml $otherparams $testtorun