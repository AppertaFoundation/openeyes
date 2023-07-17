#!/bin/bash

# Script to fetch and process monthly GP/Practice updates from HSCIC into
# OpenEyes. Should be called daily, but will only perform real work once a
# month when HSCIC have published an update.

## NOTE: This script assumes it is in protected/scripts. If you move it then relative paths will not work!

# Find fuill folder path where this script is located, then find root folder
SOURCE="${BASH_SOURCE[0]}"
while [ -h "$SOURCE" ]; do # resolve $SOURCE until the file is no longer a symlink
    DIR="$(cd -P "$(dirname "$SOURCE")" && pwd)"
    SOURCE="$(readlink "$SOURCE")"
    [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE" # if $SOURCE was a relative symlink, we need to resolve it relative to the path where the symlink file was located
done
# Determine root folder for site - all relative paths will be built from here
SCRIPTDIR="$(cd -P "$(dirname "$SOURCE")" && pwd)"
WROOT="$(cd -P "$SCRIPTDIR/../../" && pwd)"

HOSTNAME=$(hostname)
SCRIPT=$(basename "$0")

extraparams=""
region=${OE_HSCIC_REGION:-'england'}
optom="false"

while [[ $# -gt 0 ]]; do
    p="${1,,}"

    case $p in
    -region | --region)
        region=$2
        shift
        ;;
    -optom | --optom)
        optom="true"
        ;;
    *)
        [ -n "$p" ] && extraparams+="${p} " || :
        ;;
    esac
    shift # move to next parameter
done

echo "$SCRIPT: Started at $(date)"

function error_exit() {
    echo
    echo "$SCRIPT: Error: $1"
    echo "$SCRIPT: Failed at $(date)"

    if [[ -n $SUPPORT_EMAIL && -d "/usr/sbin/sendmail" ]]; then
        /usr/sbin/sendmail -t <<EOF
Subject: $HOSTNAME - $SCRIPT - Failed at $(date)
To: $SUPPORT_EMAIL
$1
Please see the log file for further details
EOF
    fi
    exit 1
}

yiicroot="${WROOT}/protected"

if [ ! -z "$HSCIC_GP_DATA_URL" ] && [ ! -z "$HSCIC_PRACTICE_DATA_URL" ]; then

    if ! php "$yiicroot"/yiic processhscicdata downloadAndImportFromUrl --type=gp --interval=full --region="${region,,}" --audit=false --url=$HSCIC_GP_DATA_URL "$extraparams"; then
        error_exit "Failed to import GP data"
    fi

    if ! php "$yiicroot"/yiic processhscicdata downloadAndImportFromUrl --type=practice --interval=full --region="${region,,}" --audit=false --url=$HSCIC_PRACTICE_DATA_URL "$extraparams"; then
        error_exit "Failed to import GP Practice  data"
    fi

    if [ "${optom}" = "true" ] && [ ! -z "$HSCIC_OPTOM_URL" ]; then
        if ! php "$yiicroot"/yiic processhscicdata downloadAndImportFromUrl --type=optom --interval=full --region="${region,,}" --audit=false --url=$HSCIC_OPTOM_URL "$extraparams"; then
            error_exit "Failed to import Optometrist data"
        fi
    fi
else
    # Do quarterly files first - belt and braces catch up in case of previous issues.

    if ! php "$yiicroot"/yiic processhscicdata downloadall --region="${region,,}" "$extraparams"; then
        error_exit "Failed to download all data"
    fi

    if ! php "$yiicroot"/yiic processhscicdata import --type=gp --interval=full --region="${region,,}" --audit=false "$extraparams"; then
        error_exit "Failed to import GP data"
    fi

    if ! php "$yiicroot"/yiic processhscicdata import --type=practice --interval=full --region="${region,,}" --audit=false "$extraparams"; then
        error_exit "Failed to import Practice data"
    fi

    if [ "${optom}" = "true" ]; then
        if ! php "$yiicroot"/yiic processhscicdata import --type=optom --interval=full --region="${region,,}" --audit=false "$extraparams"; then
            error_exit "Failed to import Optometrist data"
        fi
    fi

    # CCG and monthly updates are avialable for England only).
    if [ "${region,,}" = "england" ]; then

        if ! php "$yiicroot"/yiic processhscicdata import --type=ccg --interval=full --audit=false "$extraparams"; then
            error_exit "Failed to import CCG data"
        fi

        if ! php "$yiicroot"/yiic processhscicdata import --type=ccgAssignment --interval=full --audit=false "$extraparams"; then
            error_exit "Failed to import CCG Assignment data"
        fi

        # Now do monthly

        if ! php "$yiicroot"/yiic processhscicdata download --type=gp --interval=monthly "$extraparams"; then
            error_exit "Failed to download monthly GP/Practice data"
        fi

        if ! php "$yiicroot"/yiic processhscicdata import --type=gp --interval=monthly "$extraparams"; then
            error_exit "Failed to import monthly GP/Practice data"
        fi
    fi
fi

echo "$SCRIPT: Finished at $(date)"
