#!/usr/bin/env bash

function setupModule {
    loc_modules_path=$1
    loc_module=$2
    loc_running_path=$3
    loc_current_branch=$4
    if [ ! -d "$loc_modules_path/$loc_module" ]; then
        echo "clone-modules.sh: Cloning $loc_module module..." git@github.com:openeyes/$module.git
        #git clone git@github.com:openeyes/$module.git $modules_path/$module
        git clone https://github.com/openeyes/$loc_module $loc_modules_path/$loc_module
    else
        cd $loc_modules_path/$loc_module
        echo "clone-modules.sh: Switching module $loc_module branch to $loc_current_branch..."
        git checkout $loc_current_branch
        echo "clone-modules.sh: git reset --hard origin/$loc_current_branch and then pull"
        git reset --hard origin/$loc_current_branch
        git pull
        echo "clone-modules.sh: git clean -xdf"
        git clean -xdf
    fi
    echo "clone-modules.sh: cd $loc_running_path"
    cd $loc_running_path
}

modules_path="protected/modules"
running_path=${PWD}
enabled_modules="$running_path/.enabled-modules"

if [ "$1" ]; then
     current_branch=$1
else
     current_branch=`git symbolic-ref --short HEAD`
fi

if [ ! -f $enabled_modules ]; then
    echo "clone-modules.sh: File $enabled_modules doesn't exists. Please create one."
    exit 1
fi

if [ ! -f $local_config_file ]; then
    echo "clone-modules.sh: File $local_config_file doesn't exists. Please create one."
    exit 1
fi

while read module
do
    if [ ! -e $module ]; then
        setupModule $modules_path $module $running_path $current_branch
        if [ -r $modules_path/$module/moduledeps ]; then
            moduledeps="$modules_path/$module/moduledeps"
            echo "clone-modules.sh: Setting up $module dependencies: $moduledeps "
            while read -r moduledep || [[ -n "$moduledep" ]]
            do
                echo "clone-modules.sh: configuring dependency: setupModule $modules_path $moduledep $running_path $current_branch"
                setupModule $modules_path $moduledep $running_path $current_branch
            done < "$modules_path/$module/moduledeps"
        fi
    fi
done < $enabled_modules






