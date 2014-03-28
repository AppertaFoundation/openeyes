#!/usr/bin/env sh

modules_path="protected/modules"
running_path=${PWD}
enabled_modules="$running_path/.enabled-modules"

if [ "$1" ]; then
     current_branch=$1
else
     current_branch=`git symbolic-ref --short HEAD`
fi

if [ ! -f $enabled_modules ]; then
    echo "File $enabled_modules doesn't exists. Please create one."
    exit 1
fi

if [ ! -f $local_config_file ]; then
    echo "File $local_config_file doesn't exists. Please create one."
    exit 1
fi

while read module
do
    if [ ! -e $module ]; then 
        if [ ! -d "$modules_path/$module" ]; then
            echo "Cloning $module module..." git@github.com:openeyes/$module.git
            #git clone git@github.com:openeyes/$module.git $modules_path/$module
            git clone https://github.com/openeyes/$module $modules_path/$module
        else
            # Fetch latest version
            git fetch origin
        fi
        cd $modules_path/$module
        echo "Switching module $module branch to $current_branch..."
        git checkout $current_branch
        git reset --hard HEAD
        git clean -xdf
        cd $running_path
    fi
done < $enabled_modules




