#!/usr/bin/env bash
#
# OpenEyes
#
# (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
# (C) OpenEyes Foundation, 2011-2013
# This file is part of OpenEyes.
# OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
# OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
# You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
#
# @package OpenEyes
# @link http://www.openeyes.org.uk
# @author OpenEyes <info@openeyes.org.uk>
# @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
# @copyright Copyright (c) 2011-2013, OpenEyes Foundation
# @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0

# Find OpenEyes root
while [ $PWD != "/" ];
do
	if [ -e ".git/config" ] && grep -Fq "openeyes/OpenEyes.git" ".git/config"; then break;	fi
	cd ..
done
if [ ! -e ".git/config" ] || ! grep -Fq "openeyes/OpenEyes.git" ".git/config"; then
  echo "Error: Cannot find root of the OpenEyes repository to run this script."
  exit 1
fi

# Vars
COMMAND="install"
MODULE_REGEX="Oph"
MODULE_PATH="protected/modules"
CONFIG_FILE="protected/config/local/common.php"
BRANCH=`git symbolic-ref --short HEAD`
GITHUB_ORG="openeyes"
GITHUB_API="https://api.github.com"

getPHPDBConfig() {
  php << EOF
<?php
\$config = include "protected/config/local/common.php";
echo \$config["components"]["db"]["$1"];
EOF
}

CONNECTION_STRING=`getPHPDBConfig "connectionString" | sed 's/^.*://'`
DB_NAME=`echo $CONNECTION_STRING | cut -d';' -f3 | sed 's/.*=//'`
DB_PORT=`echo $CONNECTION_STRING | cut -d';' -f2 | sed 's/.*=//'`
DB_HOST=`echo $CONNECTION_STRING | cut -d';' -f1 | sed 's/.*=//'`
DB_USER=`getPHPDBConfig "username"`
DB_PASSWORD=`getPHPDBConfig "password"`

usage() {
cat << EOF
usage: $0 [options] [command]

  This interactive script is used to perform various tasks on the OpenEyes application.

  Commands:
    setup           Performs some setup tasks on the repository and ensures vagrant is up
    install         Interactively install modules from github
    migrate         Run the migrations for core and all installed modules
    sample_data     Clones the sample data and imports the SQL to the database
    import_data     Imports data from a SQL file into the database
    environment     Changes the app environment in the common.php config file
    change_branch   Switches to specified branch for core and all modules
    reset           Resets all changes for core and all modules
    update          Pulls down new changes from github for core and all modules
    changes         Shows changed files for core and for all modules
    merge           Merges the specified branch for core and all modules
    diff            Shows diffs of changes for core and all modules

  Options:
    --db_port      Set the database port
    --db_user      Set the database user
    --db_host      Set the database host
    --db_pass      Set the database password
    -h|--help      Show this message
EOF
}

#
# Command: install
# Install modules from github.
#
command_install() {
  prompt_modules
}

#
# Command: migrate
# Run the migrations for all modules.
#
command_migrate() {
  migrate_core_and_modules
}

#
# Command: sample_data
# Clones the sample data and imports the SQL to the database.
#
command_sample_data() {
  prompt_sample_data
}

#
# Command: import_data
# Imports data from a SQL file into the database.
#
command_import_data() {
  echo -n "Please enter the path to the SQL file you wish to import: "
  read file
  import_data "$file"
}

#
# Command: setup
# Performs some setup tasks on the repository and ensure vagrant is up. This command should take place
# after the OpenEyes repository has been cloned for the first time.
#
command_setup() {
  git submodule update --init --recursive
  check_php_settings
  change_app_environment "dev"
  start_vm
  echo "Setup completed!"
}

#
# Command: change_branch
# Switches to specified branch for core and all modules.
#
command_change_branch() {

  echo -n "Please enter the branch you want to change to: "
  read branch

  echo -n "Core..."
  git checkout "$branch"
  if [ $? -ne 0 ]; then
      echo "Aborted. Please fix errors."
      exit 1
  fi

  loop_modules_and_sample_data change_module_branch "$branch"

  echo -e "\nYou are now on branch: $branch"
}

#
# Command: merge
# Merges the specified branch for core and all modules.
#
command_merge() {
  echo -n "Please enter the branch you want to merge from: "
  read branch
  git merge "$branch"
  loop_modules merge_branch "$branch"
}

merge_branch() {
  echo -n "$1..."
  cd "$MODULE_PATH/$1"
  git merge "$2"
  cd ../../../
}

#
# Command: update
# Pulls down new changes from github for core and all modules.
#
command_update() {

  echo -n "Core..."
  git pull

  if [ $? -ne 0 ]; then
    echo "Aborted. Please fix errors."
    exit 1
  fi

  loop_modules git_pull_module
}

#
# Command: reset
# Resets all changes for core and all modules.
#
command_reset() {

  echo -n "Core..."
  git reset HEAD --hard
  git fetch origin
  git reset --hard origin/"$BRANCH"

  if [ $? -ne 0 ]; then
    echo "Aborted. Please fix errors."
    exit 1
  fi

  loop_modules git_reset_module
}

#
# Command: changes
# Shows changed files for core and for all modules.
#
command_changes() {
  echo "Changes for core:"
  git status
  loop_modules check_module_status
}

#
# Command: diff
# Shows diffs of changes for core and all modules.
#
command_diff() {
  echo "Diff for core:"
  git diff
  loop_modules module_diff
}

#
# Command: change app environment
# Changes the app environment in the common.php config file.
#
command_change_app_environment() {
  if [ -z $1 ]; then
    echo -n "Please enter the environment: "
    read env
  fi
  change_app_environment "$env"
}

#
# Change the git branch of a module
#
change_module_branch() {
  echo -n "$1..."
  cd "$MODULE_PATH/$1"
  git checkout $2
  cd ../../../
}

#
# Does a 'git status' on a module folder
#
check_module_status() {
  echo -e "\nChanges for module $1:"
  cd "$MODULE_PATH/$1"
  git status
  cd ../../../
}

#
# Does a 'git' diff on a module folder
#
module_diff() {
  echo -e "\nDiff for module $1:"
  cd "$MODULE_PATH/$1"
  git diff
  cd ../../../
}

#
# Git pulls a module
#
git_pull_module() {
  echo -n "$1..."
  cd "$MODULE_PATH/$1"
  git pull
  if [ $? -ne 0 ]; then
    cd ../../../
    echo "Aborted. Please fix errors."
    exit 1
  fi
  cd ../../../
}

#
# Git resets a module
#
git_reset_module() {

  echo -n "$1..."
  cd "$MODULE_PATH/$1"

  git reset HEAD --hard
  git fetch origin
  git reset --hard "origin/$BRANCH" > /dev/null 2>&1

  if [ $? -ne 0 ]; then
    echo "Skipped $MODULE_PATH/$1 due to errors... Probably this branch doesn't exist for this repo."
  fi

  cd ../../../
}

#
# Loops through all modules and execute a calllback function for each module
#
loop_modules() {

  # Here we ensure OphTrIntravitrealinjection is first in the list to ensure
  # migrations don't break
  local files=(`ls $MODULE_PATH`)
  local i=0
  for file in "${files[@]}"; do
    if [ "$file" = "OphTrIntravitrealinjection" ]; then
      local tmp="${files[0]}"
      files[0]="$file"
      files["$i"]="$tmp"
    fi
    ((i++))
  done

  for file in "${files[@]}"; do
    if [ -d "$MODULE_PATH/$file" ] && [[ $file =~ ^$MODULE_REGEX ]]; then
      $1 "$file" $2
    fi
  done
}

#
# Loops through all modules and sample data and executed a calllback function for each module
#
loop_modules_and_sample_data() {

  loop_modules $1 $2

  if [ ! -e "$MODULE_PATH/Sample" ]; then
    echo "Error: Sample module does not exist."
  else
    $1 "Sample" $2
  fi
}

#
# Checks that the correct PHP settings have been set
#
check_php_settings() {
  if [[ -n `php -d error_reporting=E_ERROR -i | grep "date.timezone" | grep "no value"` ]]; then
    echo "Error: date.timezone is not set. Please set this value in the php.ini configuration file before continuing."
    echo "Example: date.timezone = Europe/London"
    exit 1
  fi
}

#
# Change the application environment
#
change_app_environment() {
  sed -i bak -e "s/'environment' => '.*'/'environment' => '$1'/" "$CONFIG_FILE"
  echo "Application environment changed to '$1'"
}

#
# Starts up the VM using vagrant
#
start_vm() {
  echo -n "Checking VM status..."
  if [[ -z `vagrant status | grep "default\s*running"` ]]; then
    echo -n "not running, starting up the VM..."
    vagrant up
  fi
  echo "done."
}

#
# Runs the migrations for core all installed modules, or a specific module.
#
migrate_core_and_modules() {
  migrate_core
  migrate_modules
  clean_cache
}

#
# Imports data from a SQL file into the database
#
import_data() {
  if [ ! -e "$1" ]; then
    echo "Error: File does not exist: $1"
    exit 1
  fi

  cat "$1" | mysql -u "$DB_USER" -p"$DB_PASSWORD" --port="$DB_PORT" --host="$DB_HOST" "$DB_NAME"

  echo "Data successfully imported."
}

#
# Prompts the user for confirmation to install sample data
#
prompt_sample_data() {

  echo "Database port: $DB_PORT"
  echo "Database host: $DB_HOST"
  echo "Database user: $DB_USER"
  echo "Database name: $DB_NAME"

  echo -en "\nWarning! This will drop and recreate the database! Are you sure you want to do this? [Y/n]: "
  read answer

  case "$answer" in
    n*|N*)
      echo "Good-bye"
      exit 0
      ;;
    *)
      install_sample_data
      ;;
  esac
}

#
# Installs the sample data
#
install_sample_data() {

  # Ensure the Sample module is cloned
  clone_module "Sample"

  # Drop the current db
  echo "drop database $DB_NAME; create database $DB_NAME;" | mysql -u "$DB_USER" -p"$DB_PASSWORD" --port="$DB_PORT" --host="$DB_HOST"

  echo "Database re-created, importing sample data..."

  # Add sample data to db
  import_data "$MODULE_PATH/Sample/sql/openeyes+ophtroperationbooking.sql"

  # echo "Done! Running migrations for modules..."
  command_migrate
}

#
# Cleans the cache folders
#
clean_cache() {
  rm -rf cache/*
  rm -rf protected/cache/*
}

#
# Run the core migrations
#
migrate_core() {
  echo "Migrating core..."
  protected/yiic migrate
}

#
# Runs the migrations for all modules
#
migrate_modules() {

  echo -e "\nMigrating modules..."

  echo -e "\nWarning! There is (currently) no way to track dependencies between modules."
  echo "Some modules depend on other modules, and this is not enforced in the migrations."
  echo "There is a very real possibilty you can mess up the DB schema if the wrong migrations are run first."

  echo -ne "\nAre you sure you want to continue? [Y/n]: "

  read answer

  case "$answer" in
    n*|N*)
      echo "Skipping migrating modules..."
      ;;
    *)
      loop_modules migrate_module
      ;;
  esac
}

#
# Run a specific module migrations
#
migrate_module() {
  echo -e "\nRunning migration for module $1"
  protected/yiic migrate --migrationPath=application.modules.$1.migrations
}

#
# Enables the module by adding it to the config file
#
enable_module() {
  if ! grep -q $1 $CONFIG_FILE ; then
    sed -i '' -e '/return $config/ i \
      $config["modules"][] = "'"$1"'";\
      ' $CONFIG_FILE
  fi
}

#
# Clone a module
#
clone_module() {

  local module_branch="$BRANCH"

  if [ ! -e "$MODULE_PATH/$1" ]; then

    local module_repo="https://github.com/openeyes/$1"

    # First check if the remote branch exists for this module
    if [[ -z `git ls-remote --heads "$module_repo" | grep "$module_branch"` ]]; then
      echo -e "\nWARNING! Remote branch \"$module_branch\" does not exist for module $1"
      echo -e "Reverting to the \"develop\" branch.\n"
      module_branch="develop"
    fi

    git clone https://github.com/openeyes/$1 --branch $module_branch "$MODULE_PATH/$1"
    if [ $? -ne 0 ]; then
      echo "Error cloning module!"
    fi
  else
    # Ensure we're on the correct branch
    local dir=`pwd`
    cd "$MODULE_PATH/$1"
    git checkout "$module_branch"
    cd "$dir"
  fi
}

#
# Install a module
#
install_module() {
  clone_module $1
  migrate_module $1
  enable_module $1
  clean_cache
  echo "Module installed!"
}

#
# Prompt for modules to install
#
prompt_modules() {

  echo "Getting module list..."

  local REPOLIST=`curl --silent ${GITHUB_API}/orgs/${GITHUB_ORG}/repos?per_page=100 -q | grep -o '"name": ".*"' | sed 's/"name": "//g' | sed 's/"//'`

  # Allow user to install multiple modules iteratively
  while [ 1 ]; do

    echo -e "\nAvailable modules:"
    echo "-----------------------"
    echo "$REPOLIST"
    echo "-----------------------"

    echo -n "What module would you like to install? "
    read module

    [ -z "$module" ] && {
      echo "Good-bye"
      break
    }

    if ! grep -qw "$module" <<< $REPOLIST; then
      echo "Error! Invalid module!"
    else
      echo -e "\nPlease read the notices carefully throughout this process!\n"
      install_module "$module"
    fi
  done
}

#
# Handle command execution
#
main() {

  echo -e "\nYou are on branch: $BRANCH\n"

  case "$COMMAND" in
    "setup")
      command_setup
      ;;
    "install")
      command_install
      ;;
    "migrate")
      command_migrate
      ;;
    "sample_data")
      command_sample_data
      ;;
    "import_data")
      command_import_data
      ;;
    "environment")
      command_change_app_environment
      ;;
    "change_branch")
      command_change_branch
      ;;
    "reset")
      command_reset
      ;;
    "update")
      command_update
      ;;
    "changes")
      command_changes
      ;;
    "merge")
      command_merge $2
      ;;
    "diff")
      command_diff
      ;;
    *)
      echo "Error: Command not supported. ($COMMAND)"
      exit 1
      ;;
  esac
  exit 0
}

#
# Parse option arguments
#
while test $# != 0; do
  case "$1" in
    -h|--help)
      usage
      exit 0
      ;;
    --db_port)
      echo "DB PORT"
      DB_PORT=$2
      shift
      ;;
    --db_user)
      DB_USER=$2
      shift
      ;;
    --db_pass)
      DB_PASS=$2
      shift
      ;;
    --db_name)
      DB_NAME=$2
      shift
      ;;
    *)
      COMMAND="$1"
      ;;
  esac
  shift
done

main
