#!/usr/bin/env sh

local_config_file="protected/config/local/common.php"
modules_path="protected/modules"
running_path=${PWD}
enabled_modules="$running_path/.enabled-modules"

if [ "$1" ]; then
     current_branch=$1
else
     current_branch=`git symbolic-ref --short HEAD`
fi

git submodule update --init

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
        cd $running_path
        if [ -d "$modules_path/$module" ]; then
            echo "Switching module branch to $current_branch..."
            git reset --hard
            cd $modules_path/$module
            git checkout $current_branch
            cd $running_path
	else 
            echo "Installing $module module..."
            git clone https://github.com/openeyes/$module --branch $current_branch $modules_path/$module
            if ! grep -q $module $local_config_file ; then             
                sed -i '' -e '/return $config/ i \
                              $config["modules"][] = "'"$module"'";\
                             ' $local_config_file

            fi 
        fi 

        if [ -d "$modules_path/$module/migrations" ]; then
            protected/yiic migrate --interactive=0 --migrationPath=$module.migrations     
        fi
    fi
done < $enabled_modules




