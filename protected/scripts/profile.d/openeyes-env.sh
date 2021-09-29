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

## Will turn on automatic daily import of GP/Practice/CCG data (UK Only).
## Options are england, scotland, ni
# export OE_HSCIC_REGION="england"

## Authentication options
# export AUTH_SOURCE="BASIC" # OIDC, SAML, BASIC or LDAP;

# export SSO_BASE_URL='http://localhost'
# export SSO_ENTITY_ID=''
# export SSO_APP_EMBED_LINK=''
# export ## Credentials necessary for single-sign-on using OpenID-Connect
# export SSO_PROVIDER_URL=''
# export SSO_CLIENT_ID=''
# export SSO_CLIENT_SECRET''
# export SSO_ISSUER_URL
# export SSO_REDIRECT_URL='http://localhost'
# export SSO_RESPONSE_TYPE='code'
# export SSO_IMPLICIT_FLOW='true'
# export SSO_USER_ATTRIBUTES=''
# export SSO_CUSTOM_CLAIMS=''
# export STRICT_SSO_ROLES_CHECK='true'

## Set the endpoint for an LDAP server. Also automatically changes authentication from 'BASIC' to 'LDAP'
# export OE_LDAP_SERVER='ldap.example.com'
# export OE_LDAP_PORT='389'
# export OE_LDAP_ADMIN_DN=""
# export OE_LDAP_PASSWORD=""
# export OE_LDAP_DN=""
# export OE_LDAP_METHOD
# export OE_LDAP_UPDATE_NAME
# export OE_LDAP_UPDATE_EMAIL

## Set password format restrictions
# export PW_RES_MIN_LEN=8
# export PW_RES_MIN_LEN_MESS='Passwords must be at least 8 characters long'
# export PW_RES_MAX_LEN=70
# export PW_RES_MAX_LEN_MESS='Passwords must be at most 70 characters long'
# export PW_RES_STRENGTH='%^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[\W]).*$%'
# export PW_RES_STRENGTH_MESS='Passwords must include an upper case letter, a lower case letter, a number, and a special character'

## Number of password tries before triggering status change (any integer or 0 to disable)
# export PW_STAT_TRIES =3

# Amount of time before account is automatically unlocked after failed PW_STAT_TRIES - 0 to disable - (0, X days, X months, X Years,  see https://www.php.net/manual/en/function.strtotime.php  for more options)
# export PW_SOFTLOCK_TIMEOUT='10 mins'

## Password status after trigger by number of tries exceeded or after password changed by admin  -  ('current', 'stale', 'expired', 'locked')
# export PW_STAT_TRIES_FAILED= 'locked'
# export PW_STAT_ADMIN_CHANGE = 'stale'

## Number of days before password stales, expires or locks - 0 to disable - (0, X days, X months, X Years,  see https://www.php.net/manual/en/function.strtotime.php  for more options)
# export PW_STAT_DAYS_STALE= '15 days'
# export PW_STAT_DAYS_EXPIRE = '30 days'
# export PW_STAT_DAYS_LOCK =  '45 days'

## Set hos num parameters
# export OE_HOS_NUM_REGEX='/^([a-zA-Z]*[0-9]*)$/'
# export OE_HOS_NUM_PAD='%07s'

##    * Filename format for the PDF and XML files output by the docman export. The strings that should be replaced
##    * with the actual values needs to be enclosed in curly brackets such as {event.id}. The supported strings are -
##    *
##    * {prefix}, {event.id}, {patient.hos_num}, {random}, {gp.nat_id}, {document_output.id}, {event.last_modified_date}, {date}.
##    *
##    */
# export DOCMAN_FILENAME_FORMAT='OPENEYES_{prefix}{patient.hos_num}_{event.id}_{random}'
# export DOCMAN_GENERATE_XML=true

## Some overrides for database settings of visual mode indicators
# export OE_TRAINING_MODE=""
# export OE_USER_BANNER_SHORT=""
# export OE_USER_BANNER_LONG=""
# export OE_ADMIN_BANNER_SHORT=""
# export OE_ADMIN_BANNER_LONG=""

## Training hub variables
# export OE_TRAINING_HUB_TEXT=""
# export OE_TRAINING_HUB_URL=""