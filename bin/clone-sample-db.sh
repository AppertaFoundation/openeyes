#!/usr/bin/env sh

modules_path="protected/modules"

if [ "$1" ]; then
    current_branch=$1
else
    current_branch=`git symbolic-ref --short HEAD`
fi

if [ ! -d $modules_path/sample ]; then
    git clone https://github.com/openeyes/Sample.git $modules_path/sample
fi

cd $modules_path/sample && git checkout $current_branch
