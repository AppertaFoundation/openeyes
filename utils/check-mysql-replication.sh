#!/bin/bash
mysql_user="root"
mysql_pass="openeyesdevel"
lag_threshold=30

test1=`mysql --user=$mysql_user --password=$mysql_pass -e 'show slave status\G' | grep Slave_IO_Running |cut -f2 -d: |cut -d ' ' -f2`
test2=`mysql --user=$mysql_user --password=$mysql_pass -e 'show slave status\G' | grep Slave_SQL_Running |cut -f2 -d: |cut -d ' ' -f2`
lag=`mysql --user=$mysql_user --password=$mysql_pass -e 'show slave status\G' | grep Seconds_Behind_Master |cut -f2 -d: |cut -d ' ' -f2`

if [ $test1 != "Yes" -o $test2 != "Yes" -o $lag -ge $lag_threshold ] ; then
	echo "ERROR:"
	if [ $test1 != "Yes" ] ; then
		echo " Slave_IO not running"
	fi
	if [ $test2 != "Yes" ] ; then
		echo " Slave_SQL not running"
	fi
	if [ $lag -ge $lag_threshold ] ; then
		echo " Replication lag above threshold: $lag (threshold: $lag_threshold)"
	fi
else
	echo "OK"
fi
