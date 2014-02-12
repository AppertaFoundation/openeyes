#!/bin/bash
CSDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
# possible sh alternative DIR=$(readlink -f $(dirname $0))
echo "Current script dir: $CSDIR"

testsuite=all
if [ $# -gt 0 ]
  then
    if [ "$1" == 'all' ] || [ "$1" == 'Modules' ]
    then
        if [ "$1" == 'Modules' ]
        then
            testsuite=Modules
            echo "calling  $CSDIR/modules_yii_config.sh $2"
            $CSDIR/modules_yii_config.sh $2
        else
            echo "calling  $CSDIR/modules_yii_config.sh all"
            $CSDIR/modules_yii_config.sh all
        fi
    else
        echo "calling  $CSDIR/modules_yii_config.sh $1"
        $CSDIR/modules_yii_config.sh $1
    fi
else
    echo "calling  $CSDIR/modules_yii_config.sh"
    $CSDIR/modules_yii_config.sh
    testsuite=core
fi

# install Yii
git submodule update --init

vagrant ssh -c 'cd /var/www;  echo "running oe-migrate"; /var/www/protected/yiic migrate --interactive=0 --connectionID=testdb; \
/var/www/protected/yiic migratemodules --interactive=0 --connectionID=testdb;exit;'

echo "cd /var/www/protected/tests/; /var/www/bin/phpunit --configuration phpunit_ci.xml --testsuite $testsuite "

# Run tests
vagrant ssh -c "cd /var/www/protected/tests/; /var/www/bin/phpunit --configuration phpunit_ci.xml --testsuite $testsuite "
