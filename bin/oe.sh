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
#
# PLEASE NOTE:
# This script has only been tested on OSX. There's no guarantee it will work on
# GNU/Linux. Use this script at your own risk!

# Ensure we're in the OpenEyes root.
while [ $PWD != "/" ];
do
	if [ -e ".git/config" ] && grep -Fq "openeyes/OpenEyes.git" ".git/config"; then
		break
	fi
	cd ..
done

# Global Vars
COMMAND=""
MODULE_REGEX="Oph"
MODULE_PATH="protected/modules"
CONFIG_FILE="protected/config/local/common.php"
CORE_BRANCH=`git symbolic-ref --short HEAD`
GITHUB_ORG="openeyes"
GITHUB_API="https://api.github.com"
ROOT_DIR=`pwd`

#
# Shows the usage documentation.
#
usage() {
	cat << EOF
usage: $0 [options] [command]

This interactive script is used to perform various tasks on the OpenEyes application.

Commands:
	setup                     Performs some setup tasks on the repository and ensures vagrant is up.
	install                   Interactively install modules from github.
	migrate                   Run the migrations for core and all installed modules.
	sample_data               Clones the sample data and imports the SQL to the database.
	import_data <filepath>    Imports data from a SQL file into the database.
	environment <env>         Changes the app environment in the common.php config file.
	git <command> [options]   Runs the git command on core and all module files.

Options:
	--db-port <port>      Set the database port.
	--db-user <user>      Set the database user.
	--db-host <host>      Set the database host.
	--db-pass <pass>      Set the database password.
	-h|--help             Show this message.
EOF
}

#
# Reads the database config from the PHP config file.
#
dbconfig() {
	php << EOF
<?php
	\$config = include "protected/config/local/common.php";
	echo \$config["components"]["db"]["$1"];
EOF
}

# Sets default values for the global vars.
set_default_values() {
	local connection_string=`dbconfig "connectionString" | sed 's/^.*://'`
	DB_NAME=`echo $connection_string | cut -d';' -f3 | sed 's/.*=//'`
	DB_PORT=`echo $connection_string | cut -d';' -f2 | sed 's/.*=//'`
	DB_HOST=`echo $connection_string | cut -d';' -f1 | sed 's/.*=//'`
	DB_USER=`dbconfig "username"`
	DB_PASSWORD=`dbconfig "password"`
}

write_log() {

	local level="$1"
	local color
	shift

	case "$level" in
		success)
			color="\x1B[92m"
			;;
		fail|error)
			color="\x1B[91m"
			;;
		notice|info)
			color="\x1B[93m"
			;;
	esac

	level=`echo $level | tr '[a-z]' '[A-Z]'`
	# echo -e "$color$level: $@"
	echo -e "$color$@"
	tput sgr0
}

#
# Command: install
# Install modules from github.
#
command_install() {

	echo "Getting module list from github, please wait..."

	local repolist=`curl --silent ${GITHUB_API}/orgs/${GITHUB_ORG}/repos?per_page=100 -q | grep -o '"name": ".*"' | sed 's/"name": "//g' | sed 's/"//'`

	while [ 1 ]; do

		echo -e "\nAvailable modules:"
		echo "-----------------------"
		echo "$repolist"
		echo "-----------------------"

		echo -n "Which module would you like to install? "
		read module

		[ -z "$module" ] && {
			echo "Good-bye"
			break
		}

		if ! grep -qw "$module" <<< $repolist; then
			write_log "error" "Error! Invalid module!"
		else
			write_log "info" "\nPlease read the notices carefully throughout this process!\n"
			install_module "$module"
		fi
	done
}

#
# Command: migrate
# Run the migrations for all modules.
#
command_migrate() {

	write_log "info" "Migrating core..."
	protected/yiic migrate

	write_log "info" "Migrating modules.."
	loop_modules migrate_module
	clean_cache
}

#
# Command: sample_data
# Clones the sample data and imports the SQL to the database.
#
command_sample_data() {

	echo "Database port: $DB_PORT"
	echo "Database host: $DB_HOST"
	echo "Database user: $DB_USER"
	echo "Database name: $DB_NAME"

	write_log "info" "\nWarning! This will drop and recreate the database!"
	echo -en "Are you sure you want to do this? [Y/n]: "
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
# Command: import_data
# Imports data from a SQL file into the database.
#
command_import_data() {
	import_data "$1"
}

#
# Command: git
# Run git commands on core and all modules.
#
command_git() {
	loop_core_and_modules git_run "$@"
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
	write_log "success" "Setup completed!"
}

#
# Command: change app environment
# Changes the app environment in the common.php config file.
#
command_change_app_environment() {
	local $env="$1"
	if [ -z "$env" ]; then
		echo -n "Please enter the environment: "
		read env
	fi
	change_app_environment "$env"
}

#
# Loops through core and all modules and executes a callback function for each directory.
#
loop_core_and_modules() {
	local callback="$1"
	shift
	$callback "." "$@"
	loop_modules "$callback" "$@"
}

#
# Loops through all modules and execute a calllback function for each module.
#
loop_modules() {

	local callback="$1"
	shift

	# Here we ensure OphTrIntravitrealinjection is first in the list to ensure
	# migrations don't break
	local dirs=(`ls $MODULE_PATH`)
	local i=0
	for dir in "${dirs[@]}"; do
		if [ "$dir" = "OphTrIntravitrealinjection" ]; then
			local tmp="${dirs[0]}"
			dirs[0]="$dir"
			dirs["$i"]="$tmp"
		fi
		((i++))
	done

	for dir in "${dirs[@]}"; do
		$callback "$MODULE_PATH/$dir" "$@"
	done
}

#
# Checks that the correct PHP settings have been set
#
check_php_settings() {
	if [[ -n `php -d error_reporting=E_ERROR -i | grep "date.timezone" | grep "no value"` ]]; then
		write_log "error" "Error: date.timezone is not set. Please set this value in the php.ini configuration file before continuing."
		write_log "error" "Example: date.timezone = Europe/London"
		exit 1
	fi
}

#
# Change the application environment
#
change_app_environment() {
	sed -i bak -e "s/'environment' => '.*'/'environment' => '$1'/" "$CONFIG_FILE"
	write_log "success" "Application environment changed to '$1'"
}

#
# Starts up the VM using vagrant
#
start_vm() {
	echo -n "Checking VM status..."
	if [[ -z `vagrant status | grep "default\s*running"` ]]; then
		write_log "info" "not running, starting up the VM..."
		vagrant up
	fi
	echo "done."
}

#
# Imports data from a SQL file into the database
#
import_data() {

	if [ -z "$1" ]; then
		write_log "error" "Please specify a file path!"
		exit 1
	fi
	if [ ! -e "$1" ]; then
		write_log "error" "File does not exist: $1"
		exit 1
	fi

	cat "$1" | mysql -u "$DB_USER" -p"$DB_PASSWORD" --port="$DB_PORT" --host="$DB_HOST" "$DB_NAME"

	write_log "success" "Data successfully imported."
}

#
# Installs the sample data
#
install_sample_data() {

	# Ensure the Sample module is cloned
	clone_module "Sample"

	# Drop the current db
	echo "drop database $DB_NAME; create database $DB_NAME;" | mysql -u "$DB_USER" -p"$DB_PASSWORD" --port="$DB_PORT" --host="$DB_HOST"

	write_log "success" "Database re-created, importing sample data..."

	# Add sample data to db
	import_data "$MODULE_PATH/Sample/sql/openeyes+ophtroperationbooking.sql"

	# echo "Done! Running migrations for modules..."
	command_migrate
}

#
# Cleans the cache folders.
#
clean_cache() {
	rm -rf cache/*
	rm -rf protected/cache/*
}

#
# Run a specific module migrations.
#
migrate_module() {

	local module=`friendly_dir $1`
	local found=0
	local skip_modules=(Sample eyedraw)
	local m

	for m in "${skip_modules[@]}";
	do
		if [ "$m" = "$module" ]; then found=1; fi
	done;

	if [[ $found -eq 1 ]]; then
		write_log "notice" "Skipping module $module"
	else
		write_log "info" "Running migrations for module $module"
		protected/yiic migrate --migrationPath=application.modules.$module.migrations
	fi
}

#
# Enables the module by adding it to the config file.
#
enable_module() {
	if ! grep -q $1 $CONFIG_FILE ; then
		sed -i '' -e '/return $config/ i \
			$config["modules"][] = "'"$1"'";\
			' $CONFIG_FILE
	fi
}

#
# Clone a module.
#
clone_module() {

	local module_branch="$CORE_BRANCH"

	if [ ! -e "$MODULE_PATH/$1" ]; then

		local module_repo="https://github.com/openeyes/$1"

		# First check if the remote branch exists for this module
		if [[ -z `git ls-remote --heads "$module_repo" | grep "$module_branch"` ]]; then
			write_log "info" "WARNING! Remote branch \"$module_branch\" does not exist for module $1"
			if [[ -z `git ls-remote --heads "$module_repo" | grep develop` ]]; then
				module_branch="master"
			else
				module_branch="develop"
			fi
			write_log "info" "Reverting to the \"$module_branch\" branch.\n"
		fi

		git clone https://github.com/openeyes/$1 --branch $module_branch "$MODULE_PATH/$1"
		if [ $? -ne 0 ]; then
			write_log "error" "Error cloning module!"
		else
			write_log "success" "Module $1 cloned"
		fi
	else
		# Ensure we're on the correct branch
		cd "$MODULE_PATH/$1"
		git checkout "$module_branch"
		cd "$ROOT_DIR"
	fi
}

#
# Install a module.
#
install_module() {
	clone_module $1
	migrate_module $1
	enable_module $1
	clean_cache
	write_log "success" "Module $1 installed!"
}

#
# Print a friendly version of the specified directory.
#
friendly_dir() {
	local dir="$1"
	if [ "$dir" = "." ]; then
		dir="Core"
	fi
	echo ${dir/$MODULE_PATH\//}
}

#
# Run git commands from within a specified folder.
#
git_run() {

	local dir="$1"
	local git_command="$2"
	shift 2

	# TODO
	# We should prevent certain git commands on the Sample data module.

	cd "$dir"
	echo `friendly_dir $dir`":"
	git "$git_command" "$@"

	if [ $? -ne 0 ]; then
		write_log "error" "Aborted! Please fix the errors."
		exit 1
	fi

	echo
	cd "$ROOT_DIR"
}

exec_command() {

	COMMAND="$1"
	shift

	if [ -z "$COMMAND" ]; then
		usage
		exit 0
	fi

	echo -e "You are on branch: $CORE_BRANCH\n"

	case "$COMMAND" in
		"setup")
			command_setup "$@"
			;;
		"install")
			command_install "$@"
			;;
		"migrate")
			command_migrate "$@"
			;;
		"sample_data")
			command_sample_data "$@"
			;;
		"import_data")
			command_import_data "$@"
			;;
		"environment")
			command_change_app_environment "$@"
			;;
		"git")
			command_git "$@"
			;;
		*)
			write_log "error" "ERROR: Command not supported. ($COMMAND)"
			echo "Run $0 --help for usage instructions."
			exit 1
			;;
	esac

	write_log "success" "$COMMAND task completed."
	echo
	exit 0
}

#
# Main script entry point.
#
main() {

	set_default_values

	# Parse the global options.
	# Global options are defined before the command: $0 [global_options] [command]
	# http://mywiki.wooledge.org/BashFAQ/035
	while :
	do
		case "$1" in
			-h|--help)
				usage
				exit 0
				;;
			--db-port)
				DB_PORT=$2
				shift 2
				;;
			--db-user)
				DB_USER=$2
				shift 2
				;;
			--db-pass)
				DB_PASS=$2
				shift 2
				;;
			--db-name)
				DB_NAME=$2
				shift 2
				;;
			--) # End of all options.
				shift
				break
				;;
			-*)
				echo "WARN: Unknown option (ignored): $1" >&2
				shift
				;;
			*) # No more options. Stop while loop.
				break
				;;
		esac
	done

	exec_command "$@"
}

#
# Begin execution.
#
main "$@"