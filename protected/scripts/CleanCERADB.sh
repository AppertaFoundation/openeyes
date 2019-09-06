mysql -h $1 -u $DATABASE_USER -p$DATABASE_PASS < CleanCERANormalTables_2.sql
mysql -h $1 -u $DATABASE_USER -p$DATABASE_PASS < CleanCERAVersionTables.sql
