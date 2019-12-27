#!/bin/bash

echo "Adding openeyes user..."
mysql -u root -Bse "CREATE USER '${DATABASE_TEST_USER:-openeyes}' IDENTIFIED BY '${DATABASE_TEST_PASS:-openeyes}';
CREATE DATABASE \`${DATABASE_TEST_NAME:-openeyes_test}\`;
CREATE DATABASE \`${DATABASE_NAME:-openeyes}\`;
GRANT ALL PRIVILEGES ON *.* TO '${DATABASE_TEST_USER:-openeyes}';
FLUSH PRIVILEGES;"
echo "Done."
#echo "Importing sample data..."
#echo "Importing dev data..."
#mysql $DATABASE_NAME -u root < protected/modules/sample/sql/openeyes_sample_data.sql
#echo "Importing test data..."
#mysql $DATABASE_TEST_NAME -u root < protected/modules/sample/sql/openeyes_sample_data.sql
#echo "Done."