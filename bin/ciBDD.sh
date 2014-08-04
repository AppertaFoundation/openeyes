#!/bin/bash
CSDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
# possible sh alternative DIR=$(readlink -f $(dirname $0))
echo "Current script dir: $CSDIR"
. "$CSDIR/ciFunctions.sh"


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

branchVal=$(argValue branch)

if [ "${#branchVal}" == "0" ]
then
    branchVal=develop
fi

#git clone modules
echo "Cloning/checkout modules branch=$branchVal"
bin/clone-modules.sh "$branchVal"

# install Yii
git submodule update --init

#set up modules in conf
while read module
do
    echo "attempting to add module $module"s
    if [ ! -e "$module" ]; then
        echo "Adding $module to conf string..."
        if [ "$module" = "OphCiExamination" ]; then
            modules_conf_string="$modules_conf_string '$module' => array('class' => '\\OEModule\\OphCiExamination\\OphCiExaminationModule'),\
            \
            "
        else
            modules_conf_string="$modules_conf_string '$module',\
            \
            "
        fi
    fi
done < "$enabled_modules"
echo "Modules $modules_conf_string"
#'modules' => array(
sed "s/\/\/PLACEHOLDER/${modules_conf_string//\\/\\\\}/g" protected/config/local.sample/common.autotest.php > protected/config/local/common.php
echo 'Moved config files'
echo "import test sql - delete/create db"
vagrant ssh -c '/usr/bin/mysql -u openeyes -poe_test openeyes -e "drop database openeyes; create database openeyes;";'

if [ "$OE_VAGRANT_SUBFOLDER" = "yes" ]; then
    echo "import test sql - import testdata.sql - subfolder"
    vagrant ssh -c '/usr/bin/mysql -u openeyes -poe_test openeyes < /var/www/subfolder/features/testdata.sql;'
    #vagrant ssh -c 'cd /var/www;  echo "running cleanup addresses"; /var/www/protected/yiic cleanupaddresses';
    echo "run migrations - subfolder"
    vagrant ssh -c 'cd /var/www;  echo "running oe-migrate"; /var/www/subfolder/protected/yiic migrate --interactive=0 --testdata; \
    /var/www/subfolder/protected/yiic migratemodules --interactive=0 --testdata;exit;'

    echo "generating sessions"
    vagrant ssh -c 'cd /var/www; /var/www/subfolder/protected/yiic generatesessions;exit;'
else
    echo "import test sql - import testdata.sql"
    vagrant ssh -c '/usr/bin/mysql -u openeyes -poe_test openeyes < /var/www/features/testdata.sql;'
    #vagrant ssh -c 'cd /var/www;  echo "running cleanup addresses"; /var/www/protected/yiic cleanupaddresses';
    echo "run migrations"
    vagrant ssh -c 'cd /var/www;  echo "running oe-migrate"; /var/www/protected/yiic migrate --interactive=0 --testdata; \
    /var/www/protected/yiic migratemodules --interactive=0 --testdata;exit;'

    echo "generating sessions"
    vagrant ssh -c 'cd /var/www; /var/www/protected/yiic generatesessions;exit;'
fi



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

profileVal=$(argValue profile)

if [ "${#profileVal}" == "0" ]
then
    if [ "$OE_VAGRANT_SUBFOLDER" = "yes" ]; then
        profileVal=phantomjs-ci-sub
    else
        profileVal=phantomjs-ci
    fi
fi

if [ "$OE_VAGRANT_SUBFOLDER" = "yes" ]; then
    echo "Calling behat in subfolder mode"
    #run tests
    vagrant ssh -c "cd /var/www; /var/www/subfolder/bin/behat --tags=setup --profile=$profileVal --expand --config=/var/www/subfolder/behat.yml"
    #bin/behat --tags=confidence --profile=$profileVal --expand

    vagrant ssh -c "cd /var/www; /var/www/subfolder/bin/behat --tags=regression --profile=$profileVal --expand --config=/var/www/subfolder/behat.yml"

    #vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=asa --profile=$profileVal --expand --config=/var/www/behat.yml"
    #vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=consent --profile=$profileVal --expand --config=/var/www/behat.yml"
    #vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=Intravitreal --profile=$profileVal --expand --config=/var/www/behat.yml"
    #vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=operationbooking --profile=$profileVal --expand --config=/var/www/behat.yml"
    #vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=diagnosis --profile=$profileVal --expand --config=/var/www/behat.yml"
    #vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=phasing --profile=$profileVal --expand --config=/var/www/behat.yml"
    #vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=prescription --profile=$profileVal --expand --config=/var/www/behat.yml"
    #vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=scenario --profile=$profileVal --expand --config=/var/www/behat.yml"
else
    #run tests
    vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=setup --profile=$profileVal --expand --config=/var/www/behat.yml"
    #bin/behat --tags=confidence --profile=$profileVal --expand

    vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=regression --profile=$profileVal --expand --config=/var/www/behat.yml"

    #vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=asa --profile=$profileVal --expand --config=/var/www/behat.yml"
    #vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=consent --profile=$profileVal --expand --config=/var/www/behat.yml"
    #vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=Intravitreal --profile=$profileVal --expand --config=/var/www/behat.yml"
    #vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=operationbooking --profile=$profileVal --expand --config=/var/www/behat.yml"
    #vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=diagnosis --profile=$profileVal --expand --config=/var/www/behat.yml"
    #vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=phasing --profile=$profileVal --expand --config=/var/www/behat.yml"
    #vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=prescription --profile=$profileVal --expand --config=/var/www/behat.yml"
    #vagrant ssh -c "cd /var/www; /var/www/bin/behat --tags=scenario --profile=$profileVal --expand --config=/var/www/behat.yml"
fi

exit
