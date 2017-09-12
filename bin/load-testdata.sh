#!/bin/sh -e

timestamp() {
    date +%Y%m%d%H%M%S
}

BKUP_FILE=/tmp/openeyes_bkup_$(timestamp).sql
echo "Backing up to: $BKUP_FILE ..."
/usr/bin/mysqldump -u openeyes -poe_test openeyes > $BKUP_FILE
echo "Loading test data ..."
/usr/bin/mysql -u openeyes -poe_test openeyes -e "drop database openeyes; create database openeyes;"
/usr/bin/mysql -u openeyes -poe_test openeyes < /var/www/features/testdata.sql
/var/www/protected/yiic migrate --interactive=0 --testdata
/var/www/protected/yiic migratemodules --interactive=0 --testdata
