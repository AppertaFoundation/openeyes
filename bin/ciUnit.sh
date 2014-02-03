#!/bin/sh
CSDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
echo "Current script dir: $CSDIR"

$CSDIR/modules_yii_config.sh

# Run tests
vagrant ssh -c 'cd /var/www/protected/tests/; /var/www/bin/phpunit --configuration phpunit_ci.xml --testsuite all '
