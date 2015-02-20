#!/bin/sh


sudo docker run -d --name $1 -p 3306 openeyesdb

#Fix on Monday ;)
sudo docker ps -a | grep $1 | grep -Po '(\d+)->' | grep -o "[0-9]*"