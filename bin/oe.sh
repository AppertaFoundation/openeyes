#!/usr/bin/env bash


# Check we're in the root of the OpenEyes directory.
if [ ! -e ".git/config" ] || ! grep -Fq "openeyes/OpenEyes.git" ".git/config"; then
	echo "Error: You need to be in the root of the OpenEyes repository to run this script."
	exit 1
fi

# Vars
ACTION="install"
MODULE_REGEX="Oph"
MODULE_PATH="protected/modules"
CONFIG_FILE="protected/config/local/common.php"
BRANCH=`git symbolic-ref --short HEAD`

GITHUB_ORG="openeyes"
GITHUB_API="https://api.github.com"

DB_NAME="openeyes"
DB_USER="openeyes"
DB_PASSWORD="oe_test"
DB_PORT="3333"
DB_HOST="127.0.0.1"

#
# Action: install
# Install modules from github.
#
action_install() {
	prompt_modules
}

#
# Action: migrate
# Run the migrations for all modules.
#
action_migrate() {
	migrate_core_and_modules $1
}

#
# Action: sample_data
# Clones the sample data and imports the SQL to the database.
#
action_sample_data() {
	prompt_sample_data
}

#
# Action: import_data
# Imports data from a SQL file into the database.
#
action_import_data() {
	import_data $1
}

#
# Action: setup
# Performs some setup tasks on the repository and ensure vagrant is up. This action should take place
# after the OpenEyes repository has been cloned for the first time.
#
action_setup() {
	git submodule update --init --recursive
	check_php_settings
	change_app_environment "dev"
	start_vm
	echo "Setup completed!"
}

#
# Action: change_branch
# Switches to specified branch for core and all modules.
#
action_change_branch() {

    if [ -z "$1" ]; then
        echo "Error: Please specify a branch to change to."
        exit 1
    fi

    echo -n "Core..."
    git checkout $1
    if [ $? -ne 0 ]; then
        echo "Aborted. Please fix errors."
        exit 1
    fi

    loop_modules_and_sample_data change_module_branch $1

    echo -e "\nYou are now on branch: $1"
}

action_merge() {
    git merge "$1"
    loop_modules merge_branch $1
}

merge_branch() {
    echo -n "$1..."
    cd "$MODULE_PATH/$1"
    git merge "$2"
    cd ../../../
}

#
# Action: update
# Pulls down new changes from github for core and all modules.
#
action_update() {

    echo -n "Core..."
    git pull

    if [ $? -ne 0 ]; then
        echo "Aborted. Please fix errors."
        exit 1
    fi

    loop_modules git_pull_module
}

#
# Action: reset
# Resets all changes for core and all modules.
#
action_reset() {

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
# Action: changes
# Shows changed files for core and for all modules.
#
action_changes() {
    echo "Changes for core:"
    git status
    loop_modules check_module_status
}

#
# Action: diff
# Shows diffs of changes for all modules.
#
action_diff() {
    echo "Diff for core:"
    git diff
    loop_modules module_diff
}

#
# Action: change app environment
# Changes the app environment in the common.php config file.
#
action_change_app_environment() {
	change_app_environment $1
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
# Runs the migrations for both core and all installed modules
#
migrate_core_and_modules() {
	if [ -n "$1" ]; then
		migrate_module $1
	else
		migrate_core
		migrate_modules
	fi
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

	echo -n "Warning! This will drop and recreate the database! Are you sure you want to do this? [Y/n]: "
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

	echo "Done! Running migrations for modules..."
	action_migrate
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
# Handle execution
#
main() {

	echo -e "\nYou are on branch: $BRANCH\n"

	# Set action
	if [ -n "$1" ]; then
		ACTION=$1
	fi

	# Start action
	case "$ACTION" in
		"setup")
			action_setup
			;;
		"install")
			action_install
			;;
		"migrate")
			action_migrate $2
			;;
		"sample_data")
			action_sample_data
			;;
		"import_data")
			action_import_data $2
			;;
		"environment")
			action_change_app_environment $2
			;;
        "change_branch")
            action_change_branch $2
            ;;
        "reset")
            action_reset
            ;;
        "update")
            action_update
            ;;
        "changes")
            action_changes
            ;;
        "merge")
            action_merge $2
            ;;
        "diff")
            action_diff
            ;;
		*)
			echo "Error: Action not supported."
			exit 1
			;;
	esac
	exit 0
}

main $1 $2