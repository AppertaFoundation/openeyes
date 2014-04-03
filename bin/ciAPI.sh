#!/bin/sh -e

vagrant destroy --force
vagrant up

vagrant ssh -c '/var/www/bin/load-testdata.sh'
vagrant ssh -c 'cd /var/www/protected/tests && /var/www/bin/phpunit api/'

vagrant destroy --force
