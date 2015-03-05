#!/bin/sh


docker run -d --name $1 -p 3306 openeyesdb

mysqlport=`docker ps -a | grep $1 | grep -Po '(\d+)->' | grep -o "[0-9]*"`

if [ "$mysqlport" ]; then
    echo "setting config port to be $mysqlport"

    sed -i.bak 's/port=[0-9]\+;/port='$mysqlport';/g' protected/config/local/common.php
fi