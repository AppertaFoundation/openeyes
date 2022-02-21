<?php

class m200223_184900_fix_missing_version_columns_before_set_default extends OEMigration
{
    public function up()
    {
        /**
         * This runs before m200223_184911_fix_columns_with_no_defaults_that_arent_set to patch some
         * customer databases that were missing correctly migrated _version tables for some tables
         *
         * NOTE: The migration MUST check for the existance of the table AND the column before attempting to add it
         */

         // table name, field name, type
         $columns=array(
            array('ophcocataractreferral_intraocularpressure_reading_version', 'deleted', 'tinyint(1) unsigned'),
            array('event_type_version', 'display_order', 'int(8) NOT NULL'),
         );

         foreach ( $columns as $column ) {
             $table=$this->dbConnection->createCommand("SHOW TABLES LIKE '" . $column[0] . "';")->queryAll();

             if (count($table) > 0) {
                 $field=$this->dbConnection->createCommand("SHOW COLUMNS FROM `" . $column[0] . "` LIKE '". $column[1] . "'")->queryAll();
                 if (count($field) == 0) {
                     $this->addColumn($column[0], $column[1], $column[2]);
                 }
             }
         }
    }

    public function down()
    {
        echo "down not supported";
    }
}
