#!/bin/bash -l

## This helper script will load in environment variables for cron jobs and output the log to /var/log/cron
## Provide the command to run as the first argument
## It is strongly recommended to surround the command with single quotes to prevent splitting and globbing
echo $(date): running "$@"... >>/var/log/cron
. /env.sh
. /etc/profile.d/openeyes-env.sh 2>/dev/null
eval $@ >> /var/log/cron 2>&1