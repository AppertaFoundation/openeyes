#!/bin/bash
mesg n
cd ..
vagrant up
ssh -t -t vagrant@127.0.0.1 -p 2222 'pwd; cd /var/www/protected/tests/; cp -f ../config/local/console.sample.php  ../config/local/console.php; pwd; ../yiic migrate --connectionID=testdb --interactive=0;../../bin/phpunit .'