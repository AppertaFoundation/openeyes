#!/usr/bin/env sh
#make sure selenium is running before going ahead
SELENIUM=`ps aux | grep -c selenium`
if [ "$SELENIUM" -gt 1 ]
  then
    echo GOOD - Looks like selenium is running
  else
    echo ERR - looks like selenium is not running. PLS run ./bin/run-selenium.sh
    exit 1
fi