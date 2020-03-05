<?php

class m200223_184911_fix_columns_with_no_defaults_that_arent_set extends OEMigration
{
    public function up()
    {
        // GENERAL EVENT
        $this->alterOEColumn('event', 'delete_pending', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0', true);

        // FIX DELETED DEFAULTS
        $tables_without_default_deleted = Yii::app()->db->createCommand("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = 'deleted' AND IS_NULLABLE = 'NO' AND COLUMN_DEFAULT IS NULL AND TABLE_NAME NOT LIKE '%_version'")->queryAll();
        foreach ($tables_without_default_deleted as $table) {
            $this->alterOEColumn($table['TABLE_NAME'], 'deleted', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0', true);
        }

        // FIX DISPLAY_ORDER DEFAULTS
        $tables_without_default_display_order = Yii::app()->db->createCommand("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = 'display_order' AND IS_NULLABLE = 'NO' AND COLUMN_DEFAULT IS NULL AND TABLE_NAME NOT LIKE '%_version'")->queryAll();
        foreach ($tables_without_default_display_order as $table) {
            $this->alterOEColumn($table['TABLE_NAME'], 'display_order', 'INT(8) NOT NULL DEFAULT 0', true);
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
