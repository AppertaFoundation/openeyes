#!/bin/bash
CSDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
# possible sh alternative DIR=$(readlink -f $(dirname $0))
echo "Current script dir: $CSDIR"

. $CSDIR/ciFunctions.sh

branchVal=$(argValue branch)
echo "branchVal is $branchVal length ${#branchVal}  "

execVal=$(argValue exec)
echo "execVal is $execVal length ${#execVal} "

moduleNameVal=$(argValue moduleName)
echo "moduleNameVal is $moduleNameVal length ${#moduleNameVal} "


if [ $# -gt 0 ]
  then
    if [ "$execVal" == 'all' ]
    then
        echo "calling  $CSDIR/modules_yii_config.sh exec=all branch=$branchVal"
        $CSDIR/modules_yii_config.sh exec=all branch=$branchVal
        testsuite=all
    elif [ "$execVal" == 'Modules' ]
    then
        testsuite=Modules
        echo "calling  $CSDIR/modules_yii_config.sh exec=Modules branch=$branchVal moduleName=$moduleNameVal"
        $CSDIR/modules_yii_config.sh moduleName=$moduleNameVal branch=$branchVal exec=Modules
    else
        echo "calling  $CSDIR/modules_yii_config.sh exec=$execVal branch=$branchVal"
        $CSDIR/modules_yii_config.sh exec=$execVal branch=$branchVal
        testsuite=core
    fi
else
    echo "just running core no need to call modules_yii_config.sh"
    testsuite=core
fi

# install Yii
git submodule update --init

vagrant ssh -c 'cd /var/www;  echo "running oe-migrate"; /var/www/protected/yiic migrate --interactive=0 --connectionID=testdb; \
/var/www/protected/yiic migratemodules --interactive=0 --connectionID=testdb;exit;'

echo "cd /var/www/protected/tests/; /var/www/bin/phpunit --configuration phpunit_ci.xml --testsuite $testsuite "

# Run tests
vagrant ssh -c "cd /var/www/protected/tests/; /var/www/bin/phpunit --configuration phpunit_ci.xml --testsuite $testsuite "
