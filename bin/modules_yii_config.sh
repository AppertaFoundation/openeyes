#!/bin/sh
# define all modules to test
if [ $# -eq 1 ]
  then
    echo $1 > .enabled-modules
  else
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
fi

enabled_modules=".enabled-modules"
modules_path="protected/modules"
modules_conf_string=""

#git clone modules
echo "Cloning/checkout modules"
bin/clone-modules.sh develop

echo "hard reset all and pull"
#bin/oe-git "reset --hard"
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
