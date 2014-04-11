#!/bin/bash
CSDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
# possible sh alternative DIR=$(readlink -f $(dirname $0))
echo "modules_yii_config: Current script dir: $CSDIR"

. $CSDIR/ciFunctions.sh

branchVal=$(argValue branch)

echo "modules_yii_config: branchVal is $branchVal"

execVal=$(argValue exec)
echo "modules_yii_config: execVal is $execVal"

moduleNameVal=$(argValue moduleName)
echo "modules_yii_config: moduleNameVal is $moduleNameVal"

echo "modules_yii_config: define all modules to test"
if  [ "$execVal" == "all" ]
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
    mehpas" > .enabled-modules
elif [ "$execVal" == "SingleModule" ]
then
      #as single unit test modules are set in a parent script in jenkins their code is
      # checked out in that script so we dont need to run this
      echo 'No module set up required, just running single module!'
      exit 0
elif [ "$execVal" == "Modules" ]
then
      echo "$moduleNameVal" > .enabled-modules
else
  echo "" > .enabled-modules
  echo 'No module set up required, just running core!'
  #exit 0
fi

enabled_modules=".enabled-modules"
modules_path="protected/modules"
modules_conf_string=""

#set up modules in conf
while read module
do
    echo "modules_yii_config: attempting to add module $module"
    if [ ! -e $module ]; then
        echo "modules_yii_config: Adding $module to conf string..."
        modules_conf_string="$modules_conf_string '$module',\
        \
        "
        if [ -r $modules_path/$module/moduledeps ];then
            echo "modules_yii_config: Setting up $module dependencies in common.php"
            while read -r moduledep || [[ -n "$moduledep" ]]
            do
                echo "modules_yii_config: configuring dependency: $moduledep "
                if grep -q $moduledep "$enabled_modules";then
                    echo "modules_yii_config: $moduledep  ALREADY enabled"
                else
                    modules_conf_string="'$moduledep', $modules_conf_string "
                fi
            done < "$modules_path/$module/moduledeps"
        fi
    fi
done < $enabled_modules
echo "modules_yii_config: Modules $modules_conf_string"
#'modules' => array(
sed "s/\/\/PLACEHOLDER/$modules_conf_string/g" protected/config/local/common.autotest.php > protected/config/local/common.php
echo 'modules_yii_config: Moved config files'

#git clone modules
echo "modules_yii_config: Cloning/checkout modules with branch: $branchVal"
bin/clone-modules.sh $branchVal
#git checkout $branchVal
#bin/oe-git pull