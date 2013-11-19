#!/usr/bin/env sh
# define all modules to test
echo "eyedraw
OphCiExamination
OphCiPhasing
OphCoCorrespondence
OphCoTherapyapplication
OphDrPrescription
OphLeIntravitrealinjection
OphOuAnaestheticsatisfactionaudit
OphTrConsent
OphTrIntravitrealinjection
OphTrLaser
OphTrOperationbooking
OphTrOperationnote" > .enabled-modules

enabled_modules=".enabled-modules"
modules_path="protected/modules"
modules_conf_string=""

#git clone modules
bin/clone-modules.sh develop

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

# import test sql and migrate up all modules
vagrant ssh -c '/usr/bin/mysql -u openeyes -poe_test openeyes < /var/www/features/testdata.sql;\
    cd /var/www; echo "running oe-git-pull"; bin/oe-git-pull; \
    echo "running migrate-all"; bin/migrate-all.sh; exit;'

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
    PROFILE=phantomjs
fi

#run tests
bin/behat --tags=setup --profile=$PROFILE --expand
bin/behat --tags=confidence --profile=$PROFILE --expand
bin/behat --tags=regression --profile=$PROFILE --expand
exit