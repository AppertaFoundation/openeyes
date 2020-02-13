<?php

class m200213_111658_fix_columns_with_no_defaults_that_arent_set extends OEMigration
{
	public function up()
    {
        // GENERAL EVENT
        $this->alterVersionedColumn('event', 'delete_pending', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0');

        // EXAMINATION EVENT
        $this->alterVersionedColumn('et_ophdrprescription_details', 'print', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0');

        // CORRESPONDENCE LETTER EVENT
        $this->alterVersionedColumn('et_ophcocorrespondence_letter', 'fax', 'VARCHAR(64) NOT NULL DEFAULT ""');
        $this->alterVersionedColumn('document_instance_data', 'start_datetime', 'DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00"');
        $this->alterVersionedColumn('document_instance_data', 'date', 'DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00"');

        // CONSENT FORM
        $this->alterVersionedColumn('et_ophtrconsent_other', 'information', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0');

        // DOCUMENT
        $this->alterVersionedColumn('protected_file', 'description', 'VARCHAR(64) NOT NULL DEFAULT ""');

        // LASER
        $this->alterVersionedColumn('et_ophtrlaser_anteriorseg', 'right_eyedraw', 'TEXT NULL');
        $this->alterVersionedColumn('et_ophtrlaser_anteriorseg', 'left_eyedraw', 'TEXT NULL');

        // OPERATION BOOKING
        $this->alterVersionedColumn('et_ophtroperationbooking_operation', 'cancellation_comment', 'VARCHAR(200) NULL');
        $this->alterVersionedColumn('ophtroperationbooking_operation_booking', 'cancellation_comment', 'VARCHAR(200) NULL');

        // FIX DELETED DEFAULTS
        $tables_without_default_deleted = Yii::app()->db->createCommand("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = 'deleted' AND IS_NULLABLE = 'NO' AND COLUMN_DEFAULT IS NULL")->queryAll();
        foreach ($tables_without_default_deleted as $table) {
            $this->alterVersionedColumn($table['TABLE_NAME'], 'deleted', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0');
        }

        // FIX DISPLAY_ORDER DEFAULTS
        $tables_without_default_display_order = Yii::app()->db->createCommand("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = 'display_order' AND IS_NULLABLE = 'NO' AND COLUMN_DEFAULT IS NULL")->queryAll();
        foreach ($tables_without_default_display_order as $table) {
            $this->alterVersionedColumn($table['TABLE_NAME'], 'display_order', 'INT(8) NOT NULL DEFAULT 0');
        }
	}

	public function down()
	{
        // GENERAL EVENT
        $this->alterVersionedColumn('event', 'delete_pending', 'TINYINT(1) UNSIGNED NOT NULL');

        // EXAMINATION EVENT
        $this->alterVersionedColumn('et_ophdrprescription_details', 'print', 'TINYINT(1) UNSIGNED NOT NULL');

        // CORRESPONDENCE LETTER EVENT
        $this->alterVersionedColumn('et_ophcocorrespondence_letter', 'fax', 'VARCHAR(64) NOT NULL');
        $this->alterVersionedColumn('document_instance_data', 'start_datetime', 'DATETIME NOT NULL');
        $this->alterVersionedColumn('document_instance_data', 'date', 'DATETIME NOT NULL');

        // CONSENT FORM
        $this->alterVersionedColumn('et_ophtrconsent_other', 'information', 'TINYINT(1) UNSIGNED NOT NULL');

        // DOCUMENT
        $this->alterVersionedColumn('protected_file', 'description', 'VARCHAR(64) NOT NULL');

        // LASER
        $this->alterVersionedColumn('et_ophtrlaser_anteriorseg', 'right_eyedraw', 'TEXT NOT NULL');
        $this->alterVersionedColumn('et_ophtrlaser_anteriorseg', 'left_eyedraw', 'TEXT NOT NULL');

        // OPERATION BOOKING
        $this->alterVersionedColumn('et_ophtroperationbooking_operation', 'cancellation_comment', 'VARCHAR(200) NOT NULL');
        $this->alterVersionedColumn('ophtroperationbooking_operation_booking', 'cancellation_comment', 'VARCHAR(200) NOT NULL');

        // FIX DELETED DEFAULTS
        $tables_without_default_deleted = Yii::app()->db->createCommand("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = 'deleted' AND IS_NULLABLE = 'NO' AND COLUMN_DEFAULT IS NULL")->queryAll();
        foreach ($tables_without_default_deleted as $table) {
            $this->alterVersionedColumn($table['TABLE_NAME'], 'deleted', 'TINYINT(1) UNSIGNED NOT NULL');
        }

        // FIX DISPLAY_ORDER DEFAULTS
        $tables_without_default_display_order = Yii::app()->db->createCommand("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = 'display_order' AND IS_NULLABLE = 'NO' AND COLUMN_DEFAULT IS NULL")->queryAll();
        foreach ($tables_without_default_display_order as $table) {
            $this->alterVersionedColumn($table['TABLE_NAME'], 'display_order', 'INT(8) NOT NULL');
        }
	}
}
