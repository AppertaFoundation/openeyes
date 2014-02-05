#!/bin/bash
CSDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
# possible sh alternative DIR=$(readlink -f $(dirname $0))
echo "Current script dir: $CSDIR"

$CSDIR/modules_yii_config.sh

vagrant ssh -c 'cd /var/www;  echo "running oe-migrate"; /var/www/protected/yiic migrate --interactive=0 --connectionID=testdb; \
/var/www/protected/yiic migratemodules --interactive=0 --connectionID=testdb;exit;'

# Run tests
vagrant ssh -c 'cd /var/www/protected/tests/; /var/www/bin/phpunit --configuration phpunit_ci.xml --testsuite all '
