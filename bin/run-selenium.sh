#!/usr/bin/env sh
#~/phantomjs/phantomjs-1.9.2/bin/phantomjs --webdriver=8643 &

if [ $# -eq 1 ]
  then
    VERSION="$1"
  else
    VERSION=2.39.0
fi

PATH="$HOME/phantomjs/phantomjs-1.9.2/bin/:$HOME/selenium/:$PATH" java -jar ~/selenium/selenium-server-standalone-$VERSION.jar & echo Trying to start Selenium