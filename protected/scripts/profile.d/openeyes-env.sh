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
# OE_MODE="LIVE" ## DEV, LIVE, BUILD

## Controls the git organisation that the helper scripts will pull updates from
## Default is OpenEyes for latest code, but use AppertaFoundation for Gold
## Master builds. If you wish to use your own fork of OpenEyes, put your
## github username here
#OE_GITROOT="openeyes"

## Database connection credentials:
## It is STRONGLY recommended to change the password for production environments
# DATABASE_HOST="fqdn.of.host" # default is localhost
# DATABASE_NAME="openeyes" # default is openeyes
# DATABASE_PORT="3306" # default is 3306
# DATABASE_USER="openeyes" # default is OpenEyes
# DATABASE_PASS="" # it is strongly recommended to change the db password

## MySQL/MariaDB root login credentials (used for install / reset scripts)
## It is strongly recommended that you use docker secrets or similar methods
## To set these, sa opposed to adding them as plain text here!
## It is also STRONGLY recommended to change your root password in production
## environments
# MYSQL_ROOT_PASSWORD="" # default is blank
# MYSQL_SUPER_USER="root" # default is root

## OE_NO_DB is used to tell the installer that MariaDB should not be installed
## Locally. Instead it will assume you are using a remote database server
# OE_NO_DB="false"
