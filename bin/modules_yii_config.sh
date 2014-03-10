#!/bin/bash
CSDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
# possible sh alternative DIR=$(readlink -f $(dirname $0))
echo "Current script dir: $CSDIR"

. $CSDIR/ciFunctions.sh

branchVal=$(argValue branch)

if [ "${#branchVal}" == "0" ]
then
    branchVal=develop
fi

echo "BbranchVal is $branchVal"

execVal=$(argValue exec)
echo "BexecVal is $execVal"

moduleNameVal=$(argValue moduleName)
echo "BmoduleNameVal is $moduleNameVal"

# define all modules to test
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
    " > .enabled-modules
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

#git clone modules
echo "Cloning/checkout modules"
bin/clone-modules.sh $branchVal
bin/oe-git pull