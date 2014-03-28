#!/usr/bin/env sh
#make sure selenium is running before going ahead
#SELENIUM=`ps aux | grep -c selenium`
#if [ $SELENIUM -gt 1 ]
#  then
#    echo GOOD - Looks like selenium is running
#  else
#    echo ERR - looks like selenium is not running. PLS run ./bin/run-selenium.sh
#    exit 1
#fi
#
# define all modules to test
echo "OphCiExamination
OphDrPrescription
OphTrOperationbooking
OphTrOperationnote
OphTrConsent
OphCiPhasing
OphLeEpatientletter
eyedraw
OphCoCorrespondence
OphOuAnaestheticsatisfactionaudit
OphTrIntravitrealinjection
OphCoTherapyapplication
OphTrLaser
" > .enabled-modules

enabled_modules=".enabled-modules"
modules_path="protected/modules"
modules_conf_string=""

#git clone modules
echo "Cloning/checkout modules"
bin/clone-modules.sh develop
bin/oe-git pull

# install Yii
git submodule update --init

#set up modules in conf
while read module
do
    echo "attempting to add module $module"s
    if [ ! -e $module ]; then
        echo "Adding $module to conf string..."
        modules_conf_string="$modules_conf_string '$module',\
        \
        "
    fi
done < $enabled_modules
echo "Modules $modules_conf_string"
#'modules' => array(
sed "s/\/\/PLACEHOLDER/$modules_conf_string/g" protected/config/local/common.autotest.php > protected/config/local/common.php
echo 'Moved config files'

echo "import test sql - delete/create db"
vagrant ssh -c '/usr/bin/mysql -u openeyes -poe_test openeyes -e "drop database openeyes; create database openeyes;";'
echo "import test sql - import testdata.sql"
vagrant ssh -c '/usr/bin/mysql -u openeyes -poe_test openeyes < /var/www/features/testdata.sql;'
#vagrant ssh -c 'cd /var/www;  echo "running cleanup addresses"; /var/www/protected/yiic cleanupaddresses';
echo "run migrations"
vagrant ssh -c 'cd /var/www;  echo "running oe-migrate"; /var/www/protected/yiic migrate --interactive=0 --testdata; \
/var/www/protected/yiic migratemodules --interactive=0 --testdata;exit;'

#echo "generate sessions for Operation Booking"
#vagrant ssh -c 'cd /var/www; /var/www/protected/yiic generatesessions;exit;'

#make sure phantomjs is set up and running
#PHANTOM=`ps aux | grep -c phantom`
#if [ "$PHANTOM" = "2" ]; then
#    echo "Phantomjs is already running"
#else
#    ~/phantomjs/phantomjs-1.9.2/bin/phantomjs --webdriver=8643 & PHANTOM=`ps aux | grep -c phantom`
#    if [ "$PHANTOM" = "2" ]; then
#        echo "Phantomjs has been started"
#    else
#        echo "Error starting phantomjs"
#        exit 126;
#    fi
#fi

if [ $# -eq 1 ]
  then
    PROFILE=$1
  else
    PROFILE=phantomjs-ci
fi

#run tests
vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=setup --profile=$PROFILE --expand --config=/var/www/behat.yml"
#bin/behat --tags=confidence --profile=$PROFILE --expand

vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=regression --profile=$PROFILE --expand --config=/var/www/behat.yml"

#vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=asa --profile=$PROFILE --expand --config=/var/www/behat.yml"
#vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=consent --profile=$PROFILE --expand --config=/var/www/behat.yml"
#vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=Intravitreal --profile=$PROFILE --expand --config=/var/www/behat.yml"
#vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=operationbooking --profile=$PROFILE --expand --config=/var/www/behat.yml"
#vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=diagnosis --profile=$PROFILE --expand --config=/var/www/behat.yml"
#vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=phasing --profile=$PROFILE --expand --config=/var/www/behat.yml"
#vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=prescription --profile=$PROFILE --expand --config=/var/www/behat.yml"
#vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=scenario --profile=$PROFILE --expand --config=/var/www/behat.yml"

exit
