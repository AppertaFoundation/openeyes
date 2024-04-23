#!/bin/bash

# Database connection settings

if [[ -z $SCRIPTDIR ]]; then
    # shellcheck source="/var/www/openeyes/protected/scripts/.source_config.sh"
    source "$(dirname "${BASH_SOURCE[0]}")/.source_config.sh"
fi

## default DB connection variables
# If database user / pass are empty then set from environment variables of from docker secrets (secrets are the recommended approach)
# Note that this script ignores the old db.conf method. If you are still using this deprecated
# method, then you'll need to manually set the relevant environment variables to match your db.conf
if [ -n "$MYSQL_ROOT_PASSWORD" ]; then
    dbpassword="$MYSQL_ROOT_PASSWORD"
elif [ -f "/run/secrets/MYSQL_ROOT_PASSWORD" ]; then
    dbpassword="$(</run/secrets/MYSQL_ROOT_PASSWORD)"
else
    dbpassword=""
fi

if [ -n "$MYSQL_SUPER_USER" ]; then
    username="$MYSQL_SUPER_USER"
elif [ -f "/run/secrets/MYSQL_SUPER_USER" ]; then
    username="$(</run/secrets/MYSQL_SUPER_USER)"
else
    # fallback to using root for deleting and restoring DB
    username="root"
fi

port=${DATABASE_PORT:-"3306"}
host=${DATABASE_HOST:-"localhost"}
# If we're using docker secrets, override DATABASE_PASS and DATABASE_USER with the secret. Else the environment variable will use it's default value
[ -f /run/secrets/DATABASE_PASS ] && pass="$(</run/secrets/DATABASE_PASS)" || pass=${DATABASE_PASS:-"openeyes"}
[ -f /run/secrets/DATABASE_USER ] && dbuser="$(</run/secrets/DATABASE_USER)" || dbuser=${DATABASE_USER:-"openeyes"}

# Set the coonection string
dbconnectionstring="MYSQL_PWD=${dbpassword} mysql -u '${username}' --port=${port} --host=${host}"
