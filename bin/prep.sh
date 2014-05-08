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

branch="$1"
if [ -z "$branch" ]; then
    branch="develop"
fi

bin/clone-modules.sh "$branch"

echo "hard reset all and pull"
#bin/oe-git "reset --hard"
bin/oe-git pull

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
echo "import test sql - import testdata.sql"
vagrant ssh -c '/usr/bin/mysql -u openeyes -poe_test openeyes < /var/www/features/testdata.sql;'
echo "run migrations"
vagrant ssh -c 'cd /var/www;  echo "running oe-migrate"; \
/var/www/protected/yiic cleanupaddresses; \
/var/www/protected/yiic migrate --interactive=0; \
/var/www/protected/yiic migratemodules --interactive=0; \
exit;'

#echo "generate sessions for Operation Booking"
#vagrant ssh -c 'cd /var/www; /var/www/protected/yiic generatesessions;exit;'


exit
