#!/usr/bin/env sh

error_exit()
{
	echo "$1" 1>&2
	exit 1
}

# Absolute path this script is in
SCRIPTPATH=$(dirname $0)

sh $SCRIPTPATH/check-selenium.sh

if [ $? -gt 0 ]; then
    # TODO: need to work out the version of selenium we're trying to run
    sh $SCRIPTPATH/run-selenium.sh
    if [ $? ]; then
        error_exit "unable to start selenium. Aborting."
    fi
fi

sh $SCRIPTPATH/load-testdata.sh

$SCRIPTPATH/../protected/yiic generatesessions "+1 week"

# added regression tag to get around the fact that it's not clear what
# test data should be loaded for the other tests to run
$SCRIPTPATH/behat --tags=regression --stop-on-failure --profile=selenium-local
