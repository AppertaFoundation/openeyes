#!/bin/sh

mkdir -p docker
wget https://raw.githubusercontent.com/openeyes/Sample/master/sql/openeyes_sample_data.sql -N $PWD/docker/data.sql

docker run --name $1 -v $PWD/docker:/docker-entrypoint-initdb.d -e MYSQL_ROOT_PASSWORD=rootpass -e MYSQL_DATABASE=openeyes -e MYSQL_USER=openeyes -e MYSQL_PASSWORD=oe_test -p 3306 -d mariadb

mysqlport=`docker ps -a | grep $1 | grep -Po '(\d+)->' | grep -o "[0-9]*"`

if [ "$mysqlport" ]; then
    echo "setting config port to be $mysqlport"

    sed -i.bak 's/port=[0-9]\+;/port='$mysqlport';/g' protected/config/local/common.php
    sed -i.bak 's/port=[0-9]\+;/port='$mysqlport';/g' protected/config/local/test.php
    sed -i.bak 's/port=[0-9]\+;/port='$mysqlport';/g' protected/config/local/console.php
fi

#sleep so the database is ready when the script exits.
sleep 60