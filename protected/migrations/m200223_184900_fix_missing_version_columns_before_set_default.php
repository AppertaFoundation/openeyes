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
         );

         foreach( $columns as $column ){
             $table=$this->dbConnection->createCommand("SHOW TABLES LIKE '" . $column[0] . "';")->queryAll();

             if (count($table) > 0){
                 $field=$this->dbConnection->createCommand("SHOW COLUMNS FROM `" . $column[0] . "` LIKE '". $column[1] . "'")->queryAll();
                 if (count($field) == 0){
                     $this->addColumn($column[0], $column[1], $column[2]);
                 }
             }

         }

    }

    public function down()
    {
        // GENERAL EVENT
        $this->alterOEColumn('event', 'delete_pending', 'TINYINT(1) UNSIGNED NOT NULL', true);

        // FIX DELETED DEFAULTS
        $tables_without_default_deleted = Yii::app()->db->createCommand("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = 'deleted' AND IS_NULLABLE = 'NO' AND COLUMN_DEFAULT IS NULL AND TABLE_NAME NOT LIKE '%_version'")->queryAll();
        foreach ($tables_without_default_deleted as $table) {
            $this->alterOEColumn($table['TABLE_NAME'], 'deleted', 'TINYINT(1) UNSIGNED NOT NULL', true);
        }

        // FIX DISPLAY_ORDER DEFAULTS
        $tables_without_default_display_order = Yii::app()->db->createCommand("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = 'display_order' AND IS_NULLABLE = 'NO' AND COLUMN_DEFAULT IS NULL AND TABLE_NAME NOT LIKE '%_version'")->queryAll();
        foreach ($tables_without_default_display_order as $table) {
            $this->alterOEColumn($table['TABLE_NAME'], 'display_order', 'INT(8) NOT NULL', true);
        }
    }
}
