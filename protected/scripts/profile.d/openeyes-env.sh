###############################################################################
### This file is part of OpenEyes
###
### It contains the various environment variables used to control
### your OpenEyes installation. You can set them in this file, by uncommenting
### relevant lines, or you can set them via various other methods, including
### Docker secrets
###
### Note that this file replaces /etc/openeyes/env.conf & /etc/openeyes/db.conf
### from versions prior to OpenEyes v3.0 - although db.conf will still be used
### if it exists (but should be considered deprecated)
###############################################################################

## OpenEyes operating mode. Use LIVE for production environments
## DEV will install development tools and show more meaningful error messages
# export OE_MODE="LIVE" ## DEV, LIVE, BUILD

## Controls the git organisation that the helper scripts will pull updates from
## Default is OpenEyes for latest code, but use AppertaFoundation for Gold
## Master builds. If you wish to use your own fork of OpenEyes, put your
## github username here
# export OE_GITROOT="openeyes"

## Database connection credentials:
## It is STRONGLY recommended to change the password for production environments
# export DATABASE_HOST="fqdn.of.host" # default is localhost
# export DATABASE_NAME="openeyes" # default is openeyes
# export DATABASE_PORT="3306" # default is 3306
# export DATABASE_USER="openeyes" # default is OpenEyes
# export DATABASE_PASS="" # it is strongly recommended to change the db password

## MySQL/MariaDB root login credentials (used for install / reset scripts)
## It is strongly recommended that you use docker secrets or similar methods
## To set these, sa opposed to adding them as plain text here!
## It is also STRONGLY recommended to change your root password in production
## environments
# export MYSQL_ROOT_PASSWORD="" # default is blank
# export MYSQL_SUPER_USER="root" # default is root

## OE_NO_DB is used to tell the installer that MariaDB should not be installed
## Locally. Instead it will assume you are using a remote database server
# export OE_NO_DB="false"

## OE_IOLM_FILE_WATCHER_PATH tells the IOLMasterImport module where to find
## the file watcher command. Default is /var/www/openeyes/protected/cli_commands/file_watcher
# export OE_IOLM_FILE_WATCHER_PATH="/var/www/openeyes/protected/cli_commands/file_watcher"

## Set the institution code (used in common.php to determine the defrault institution)
# export OE_INSTITUTION_CODE="NEW"

## Config for various external services
# export OE_DOCMAN_EXPORT_DIRECTORY="/tmp/docman"
# export OE_PORTAL_URI=""
# export OE_PORTAL_EXTERNAL_URI=""
# export OE_PORTAL_USERNAME=""
# export OE_PORTAL_PASSWORD=""
# export OE_PORTAL_CLIENT_ID=""
# export OE_PORTAL_CLIENT_SECRET=""
# export OE_SIGNATUE_APP_URL=""
