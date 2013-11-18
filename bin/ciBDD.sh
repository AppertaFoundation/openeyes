#!/usr/bin/env sh
# define all modules to test
echo "OphCiPhasing
OphCiExamination
MEHPAS
OphCoCorrespondence
OphCoTherapyapplication
OphDrPrescription
OphLeEpatientletter
OphLeIntravitrealinjection
OphOuAnaestheticsatisfactionaudit
OphTrConsent
OphTrIntravitrealinjection
OphTrLaser
OphTrOperationanaesthetic
OphTrOperationbooking
OphTrOperationnote" > .enabled-modules

enabled_modules=".enabled-modules"
modules_path="protected/modules"
modules_conf_string=""

#git clone modules
bin/clone-modules.sh

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

# migrate up all modules
vagrant ssh -c 'cd /var/www; bin/migrate-all.sh; exit;'

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

#run tests
bin/behat --tags=diagnosis --profile=phantomjs --expand
exit