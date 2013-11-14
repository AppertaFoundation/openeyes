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
        modules_conf_string="$modules_conf_string '$module',\n"
    fi
done < $enabled_modules
echo "Modules $modules_conf_string"
#'modules' => array(
sed 's/\/\/PLACEHOLDER/$modules_conf_string/g' protected/config/local/test.txt  > protected/config/local/test2.txt

vagrant ssh -c 'cd /var/www; bin/migrate-all.sh; exit;'