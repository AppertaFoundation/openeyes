#!/bin/bash
if [ `whoami` != "root" ] ; then
	echo "You must be root to run this script."
	exit 0
fi

echo
echo -n "Do you want to make this server OpenEyes? [y/N] "
read choice

if [ "$choice" == "y" ] ; then

	echo
	echo "Halting SQL replication and assuming master status ... "
	echo "STOP SLAVE;" | mysql --user=root --password=openeyesdevel
	echo
	echo "Applying MySQL master configuration and restarting MySQL ... "
	cp /etc/mysql/my.cnf.master /etc/mysql/my.cnf
	/etc/init.d/mysql restart 1>/dev/null 2>/dev/null
	echo
	echo "Assuming OpenEyes IP ... "
	echo
	cp /etc/network/interfaces.openeyes /etc/network/interfaces
	ifup eth0:0 1>/dev/null 2>/dev/null
	echo "This server should now be OpenEyes."
	echo
else
	echo
	echo "Ok then, just asking."
	echo
fi
