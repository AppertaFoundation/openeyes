#!/bin/bash -l

# Output to the following file path by default
outfile="sample_db"

# Compression ration 1-9 (9 is highest)
# can be overidden by cli switch
compressionratio=9

nozip=0
extraparams=""

# A list of tables that do not require their data backing up
# E.g, the event_image table (lightning imagaes), which is VERY large, but the data
# Can be regenerated again after disaster recovery (using the createimages command)
EXCLUDED_TABLES=(
  event_image
)

# A list of tables that should not be backed-up at all (i.e, schema will not exist in backup)
IGNORED_TABLES=(
  ignore_none
)

ignore_archive=0

PARAMS=()
while [[ $# -gt 0 ]]; do
  p="$1"

  case $p in
  -o | --output-file)
    ## output to a custom file path
    outfile=$2
    shift
    ;;
  -h | --help)
    echo -e "\nBacks up database to ${outfile}\n\nUse -o to specify a custom output file"
    exit 0
    ;;
  --no-exclude)
    # do not exclude any tables from the backup
    EXCLUDED_TABLES=("ignore_none")
    ;;
  --exclude-extra)
    extra_exclude=($2)
    EXCLUDED_TABLES=("${EXCLUDED_TABLES[@]}" "${extra_exclude[@]}")
    shift
    ;;
  --ignore)
    extra_ignore=($2)
    IGNORED_TABLES=("${IGNORED_TABLES[@]}" "${extra_ignore[@]}")
    ;;
  --ignore-archive)
    ignore_archive=1
    ;;
  --compression | -z)
    # compression ration, 1-9
    compressionratio=$2
    shift
    ;;
  --no-zip)
    # Will not zip the output
    nozip=1
    ;;
  *)
    # Add as extra params to mysqldump
    extraparams+="$p"
    ;;
  esac
  shift # move to next parameter
done

tmpfile="$outfile.sql"
outfile="$outfile.zip"

## default DB connection variables
# If database user / pass are empty then set from environment variables of from docker secrets (secrets are the recommended approach)
# Note that this script ignores the old db.conf method. If you are still using this deprecated
# method, then you'll need to manually set the relevant environment variables to match your db.conf
if [ ! -z "$MYSQL_ROOT_PASSWORD" ]; then
  dbpassword="-p$MYSQL_ROOT_PASSWORD"
elif [ -f "/run/secrets/MYSQL_ROOT_PASSWORD" ]; then
  dbpassword="-p$(</run/secrets/MYSQL_ROOT_PASSWORD)"
else
  dbpassword=""
fi

if [ ! -z "$MYSQL_SUPER_USER" ]; then
  username="$MYSQL_SUPER_USER"
elif [ -f "/run/secrets/MYSQL_SUPER_USER" ]; then
  username="$(</run/secrets/MYSQL_SUPER_USER)"
else
  # fallback to using root for deleting and restoring DB
  username="root"
fi

port=${DATABASE_PORT:-"3306"}
host=${DATABASE_HOST:-"localhost"}
# If we're using docker secrets, override DATABASE_PASSWORD with the secret. Else the environment variable will use it's default value
[ -f /run/secrets/DATABASE_PASSWORD ] && pass="-p$(</run/secrets/DATABASE_PASSWORD)" || pass="-p${DATABASE_PASSWORD:-openeyes}"

DATABASE=${DATABASE_NAME:-openeyes}

echo "*** Backing up ${DATABASE} ***"

if [ $ignore_archive == 1 ]; then
  # remove all tables starting with archive_
  SQL_STRING='SHOW TABLES LIKE "archive_%";'
  DBS=$(echo "$SQL_STRING" | mysql -h "${host}" -u ${username} "${dbpassword}" --port="${port}" -Bs --database="${DATABASE}")
  #-B is for batch - tab-separated columns, newlines between rows
  #-s is for silent - produce less output
  #both result in escaping special characters

  readarray -t TABLES <<<"$DBS"
  for table in "${TABLES[@]}"; do
    IGNORED_TABLES=("${IGNORED_TABLES[@]}" "$table")
  done

fi

DONT_CALCULATE_LIST=""

# setup the command for excluding data from excluded tables
EXCLUDED_TABLES_STRING=''
for TABLE in "${EXCLUDED_TABLES[@]}"; do
  :
  if [ "${TABLE}" != "ignore_none" ]; then
    echo "Excluding data for: ${DATABASE}.${TABLE}"
    EXCLUDED_TABLES_STRING+=" --ignore-table=${DATABASE}.${TABLE}"
    DONT_CALCULATE_LIST+="'${TABLE}', "
  fi
done

# setup the command for ignoring ignored tables
IGNORED_TABLES_STRING=''
for TABLE in "${IGNORED_TABLES[@]}"; do
  :
  if [ "${TABLE}" != "ignore_none" ]; then
    echo "Ignoring: ${DATABASE}.${TABLE}"
    IGNORED_TABLES_STRING+=" --ignore-table=${DATABASE}.${TABLE}"
    DONT_CALCULATE_LIST+="'${TABLE}', "
  fi
done

# strip trailing ', '
DONT_CALCULATE_LIST=${DONT_CALCULATE_LIST%, }

folder=$(dirname $outfile)
# make sure the directory exists
[ "$folder" != "." ] && mkdir -p "$folder" 2>/dev/null || :

SIZE_BYTES=$(mysql -h ${host} -u ${username} ${dbpassword} --port=${port} --skip-column-names <<<"SELECT ROUND(SUM(data_length) * 0.92) AS "size_bytes" FROM information_schema.TABLES WHERE TABLE_SCHEMA = '${DATABASE}' AND TABLE_NAME NOT IN ($DONT_CALCULATE_LIST);")
echo "Data size = approx $(expr $SIZE_BYTES / 1024 / 1024) mb"
echo "Dumping structure..."
eval mysqldump -h ${host} -u ${username} ${dbpassword} --port=${port} --routines --events --triggers --single-transaction ${IGNORED_TABLES_STRING} --no-data ${extraparams} ${DATABASE} | pv >${tmpfile}

echo "Dumping content..."
eval mysqldump -h ${host} -u ${username} ${dbpassword} --port=${port} --routines --events --triggers --single-transaction --no-create-info --skip-triggers "${EXCLUDED_TABLES_STRING}" "${IGNORED_TABLES_STRING}" $extraparams ${DATABASE} | pv --progress --size $SIZE_BYTES >>${tmpfile}

if [[ $nozip -eq 0 ]]; then
  echo "Zipping to ${outfile}..."

  pv ${tmpfile} | gzip -${compressionratio} >${outfile}

  echo "Deleting temp files..."
  rm ${tmpfile}

  echo "Testing ${outfile}..."
  gzip -t -v ${outfile}
else
  echo "Finshed: $tmpfile"
fi
