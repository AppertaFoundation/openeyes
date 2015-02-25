#!/bin/sh


sudo docker run -d --name $1 -p 3306 openeyesdb

#Fix on Monday ;)
mysqlport=`sudo docker ps -a | grep $1 | grep -Po '(\d+)->' | grep -o "[0-9]*"`

echo "setting config port to be $mysqlport"

sudo sed -i.bak 's/port=[0-9]\+;/port='$mysqlport';/g' protected/config/local/common.php