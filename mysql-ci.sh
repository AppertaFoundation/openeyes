#!/bin/bash

echo "Adding openeyes user..."
mysql -u root -Bse "CREATE USER '$DATABASE_TEST_USER' IDENTIFIED BY '$DATABASE_TEST_PASS';
CREATE DATABASE \`$DATABASE_TEST_NAME\`;
CREATE DATABASE \`$DATABASE_NAME\`;
GRANT ALL PRIVILEGES ON *.* TO '$DATABASE_TEST_USER';
FLUSH PRIVILEGES;"
echo "Done."
#echo "Importing sample data..."
#echo "Importing dev data..."
#mysql $DATABASE_NAME -u root < protected/modules/sample/sql/openeyes_sample_data.sql
#echo "Importing test data..."
#mysql $DATABASE_TEST_NAME -u root < protected/modules/sample/sql/openeyes_sample_data.sql
#echo "Done."