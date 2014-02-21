#!/bin/bash
# define all modules to test
if [ $# -eq 1 ] && [ "$1" != 'all' ] && [ "$1" != 'Modules' ]
  then
    echo "Module Yii config adding $1"
    echo $1 > .enabled-modules
  elif [ $# -eq 1 ] && [ "$1" == "all" ]
  then
    echo "Module Yii config adding all modules"
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
  elif [ $# -eq 1 ] && [ "$1" == "Modules" ]
  then
      echo 'No module set up required, just running single module!'
      exit 0
  else
  echo "" > .enabled-modules
  echo 'No module set up required, just running core!'
  exit 0
fi

enabled_modules=".enabled-modules"
modules_path="protected/modules"
modules_conf_string=""

#git clone modules
echo "Cloning/checkout modules"
bin/clone-modules.sh develop
bin/oe-git pull

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
