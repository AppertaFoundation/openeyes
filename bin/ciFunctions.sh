#!/bin/bash
global_args=("$@");
function argValue {
    #echo " "
    #echo " "
    #echo "argValue checked is $1"
    for word in ${global_args[@]}
    do
        arrIN=(${word//=/ })
        if [ "$1" == "${arrIN[0]}" ]
        then
            #echo "$1 "
            #echo " argument is ${arrIN[0]} ${arrIN[1]}";
            echo "${arrIN[1]}"
            return;
        fi
    done
}