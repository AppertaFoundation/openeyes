#!/bin/sh -e

/usr/bin/mysql -u openeyes -poe_test openeyes -e "drop database openeyes; create database openeyes;"
/usr/bin/mysql -u openeyes -poe_test openeyes < /var/www/features/testdata.sql
/var/www/protected/yiic migrate --interactive=0 --testdata
/var/www/protected/yiic migratemodules --interactive=0 --testdata
