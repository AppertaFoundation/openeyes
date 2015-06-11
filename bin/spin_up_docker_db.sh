#!/bin/sh


docker run -d --name $1 -p 3306 openeyestestdb
docker exec $1 /usr/local/bin/import.sh

mysqlport=`docker ps -a | grep $1 | grep -Po '(\d+)->' | grep -o "[0-9]*"`

if [ "$mysqlport" ]; then
    echo "setting config port to be $mysqlport"

    sed -i.bak 's/port=[0-9]\+;/port='$mysqlport';/g' protected/config/local/common.php
    sed -i.bak 's/port=[0-9]\+;/port='$mysqlport';/g' protected/config/local/test.php
    sed -i.bak 's/port=[0-9]\+;/port='$mysqlport';/g' protected/config/local/console.php
fi