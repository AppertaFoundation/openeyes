#!/bin/bash
global_args=("$@");
function argValue {
    for word in ${global_args[@]}
    do
        arrIN=(${word//=/ })
        if [ "$1" == "${arrIN[0]}" ]
        then
            echo "${arrIN[1]}"
            return;
        fi
    done
}