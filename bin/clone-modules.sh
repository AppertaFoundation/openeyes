#!/usr/bin/env sh

function setupModule {
    $modules_path=$1
    $module=$2
    $running_path=$3
    if [ ! -d "$modules_path/$module" ]; then
            echo "clone-modules.sh: Cloning $module module..." git@github.com:openeyes/$module.git
            #git clone git@github.com:openeyes/$module.git $modules_path/$module
            git clone https://github.com/openeyes/$module $modules_path/$module
    else
        cd $modules_path/$module
        echo "clone-modules.sh: Switching module $module branch to $current_branch..."
        git checkout $current_branch
        echo "clone-modules.sh: git reset --hard origin/$current_branch and then pull"
        git reset --hard origin/$current_branch
        git pull
        echo "clone-modules.sh: git clean -xdf"
        git clean -xdf
    fi
    echo "clone-modules.sh: cd $running_path"
    cd $running_path
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
        setupModule $modules_path $module $running_path
    fi
done < $enabled_modules






