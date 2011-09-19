#!/bin/bash
if [ `whoami` != "root" ] ; then
	echo "You must be root to run this script."
	exit 0
fi

echo
echo -n "Do you want to make this server NOT OpenEyes? [y/N] "
read choice

if [ "$choice" == "y" ] ; then

	echo
	echo "Relenquishing OpenEyes IP ..."
	echo
	ifdown eth0:0 1>/dev/null 2>/dev/null
	cp /etc/network/interfaces.notopeneyes /etc/network/interfaces
	echo "This server is no longer OpenEyes."
	echo
else
	echo
	echo "Ok then, just asking."
	echo
fi
