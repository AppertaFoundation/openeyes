#!/usr/bin/env sh

modules_path="protected/modules"
pwd
current_branch=`git symbolic-ref --short HEAD`

if [ ! -d $modules_path/sample ]; then
    git clone https://github.com/openeyes/Sample.git $modules_path/sample
fi

cd $modules_path/sample && git checkout $current_branch
