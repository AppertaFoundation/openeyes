<?php

class m200213_111658_fix_columns_with_no_defaults_that_arent_set extends OEMigration
{
    public function up()
    {
        // GENERAL EVENT
        $this->alterOEColumn('event', 'delete_pending', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0', true);

        // PRESCRIPTION EVENT
        $this->alterOEColumn('et_ophdrprescription_details', 'print', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0', true);

        // EXAMINATION EVENT
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'left_unable_to_assess', 'TINYINT(1) UNSIGNED NULL DEFAULT NULL');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'right_unable_to_assess', 'TINYINT(1) UNSIGNED NULL DEFAULT NULL');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'left_eye_missing', 'TINYINT(1) UNSIGNED NULL DEFAULT NULL');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'right_eye_missing', 'TINYINT(1) UNSIGNED NULL DEFAULT NULL');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'left_notes', 'TEXT NOT NULL DEFAULT ""');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'right_notes', 'TEXT NOT NULL DEFAULT ""');

        // CORRESPONDENCE LETTER EVENT
        $this->alterOEColumn('et_ophcocorrespondence_letter', 'fax', 'VARCHAR(64) NOT NULL DEFAULT ""', true);
        $this->alterOEColumn('document_instance_data', 'start_datetime', 'DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00"', true);
        $this->alterOEColumn('document_instance_data', 'date', 'DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00"', true);

        // CONSENT FORM
        $this->alterOEColumn('et_ophtrconsent_other', 'information', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0', true);

        // DOCUMENT
        $this->alterOEColumn('protected_file', 'description', 'VARCHAR(64) NOT NULL DEFAULT ""', true);

        // LASER
        $this->alterOEColumn('et_ophtrlaser_anteriorseg', 'right_eyedraw', 'TEXT NULL', true);
        $this->alterOEColumn('et_ophtrlaser_anteriorseg', 'left_eyedraw', 'TEXT NULL', true);

        // OPERATION BOOKING
        $this->alterOEColumn('et_ophtroperationbooking_operation', 'cancellation_comment', 'VARCHAR(200) NULL', true);
        $this->alterOEColumn('ophtroperationbooking_operation_booking', 'cancellation_comment', 'VARCHAR(200) NULL', true);

        // TRIALS
        $this->alterOEColumn('trial', 'is_open', 'INT(1) NOT NULL DEFAULT 1', true);

        // THERAPY APPLICATION
        $this->alterOEColumn('ophcotherapya_email', 'last_modified_date', 'DATETIME NOT NULL DEFAULT "1901-01-01 00:00:00"', true);
        $this->alterOEColumn('ophcotherapya_email', 'created_date', 'DATETIME NOT NULL DEFAULT "1901-01-01 00:00:00"', true);

        //CAT PROM5
        $this->alterOEColumn('cat_prom5_question', 'mandatory', 'TINYINT(1) NOT NULL DEFAULT 0', false);

        // FIX DELETED DEFAULTS
        $tables_without_default_deleted = Yii::app()->db->createCommand("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = 'deleted' AND IS_NULLABLE = 'NO' AND COLUMN_DEFAULT IS NULL")->queryAll();
        foreach ($tables_without_default_deleted as $table) {
            $this->alterOEColumn($table['TABLE_NAME'], 'deleted', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0', true);
        }

        // FIX DISPLAY_ORDER DEFAULTS
        $tables_without_default_display_order = Yii::app()->db->createCommand("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = 'display_order' AND IS_NULLABLE = 'NO' AND COLUMN_DEFAULT IS NULL")->queryAll();
        foreach ($tables_without_default_display_order as $table) {
            $this->alterOEColumn($table['TABLE_NAME'], 'display_order', 'INT(8) NOT NULL DEFAULT 0', true);
        }
    }

    public function down()
    {
        // GENERAL EVENT
        $this->alterOEColumn('event', 'delete_pending', 'TINYINT(1) UNSIGNED NOT NULL', true);

        // PRESCRIPTION EVENT
        $this->alterOEColumn('et_ophdrprescription_details', 'print', 'TINYINT(1) UNSIGNED NOT NULL', true);

        // EXAMINATION EVENT
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'left_unable_to_assess', 'TINYINT(1) UNSIGNED NULL');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'right_unable_to_assess', 'TINYINT(1) UNSIGNED NULL');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'left_eye_missing', 'TINYINT(1) UNSIGNED NULL');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'right_eye_missing', 'TINYINT(1) UNSIGNED NULL');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'left_notes', 'TEXT NOT NULL');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'right_notes', 'TEXT NOT NULL');

        // CORRESPONDENCE LETTER EVENT
        $this->alterOEColumn('et_ophcocorrespondence_letter', 'fax', 'VARCHAR(64) NOT NULL', true);
        $this->alterOEColumn('document_instance_data', 'start_datetime', 'DATETIME NOT NULL', true);
        $this->alterOEColumn('document_instance_data', 'date', 'DATETIME NOT NULL', true);

        // CONSENT FORM
        $this->alterOEColumn('et_ophtrconsent_other', 'information', 'TINYINT(1) UNSIGNED NOT NULL', true);

        // DOCUMENT
        $this->alterOEColumn('protected_file', 'description', 'VARCHAR(64) NOT NULL', true);

        // LASER
        $this->alterOEColumn('et_ophtrlaser_anteriorseg', 'right_eyedraw', 'TEXT NOT NULL', true);
        $this->alterOEColumn('et_ophtrlaser_anteriorseg', 'left_eyedraw', 'TEXT NOT NULL', true);

        // OPERATION BOOKING
        $this->alterOEColumn('et_ophtroperationbooking_operation', 'cancellation_comment', 'VARCHAR(200) NOT NULL', true);
        $this->alterOEColumn('ophtroperationbooking_operation_booking', 'cancellation_comment', 'VARCHAR(200) NOT NULL', true);

        // TRIALS
        $this->alterOEColumn('trial', 'is_open', 'INT(1) NOT NULL', true);

        // THERAPY APPLICATION
        $this->alterOEColumn('ophcotherapya_email', 'last_modified_date', 'DATETIME NOT NULL', true);
        $this->alterOEColumn('ophcotherapya_email', 'created_date', 'DATETIME NOT NULL', true);

        //CAT PROM5
        $this->alterOEColumn('cat_prom5_question', 'mandatory', 'TINYINT(1) NOT NULL', false);

        // FIX DELETED DEFAULTS
        $tables_without_default_deleted = Yii::app()->db->createCommand("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = 'deleted' AND IS_NULLABLE = 'NO' AND COLUMN_DEFAULT IS NULL")->queryAll();
        foreach ($tables_without_default_deleted as $table) {
            $this->alterOEColumn($table['TABLE_NAME'], 'deleted', 'TINYINT(1) UNSIGNED NOT NULL', true);
        }

        // FIX DISPLAY_ORDER DEFAULTS
        $tables_without_default_display_order = Yii::app()->db->createCommand("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = 'display_order' AND IS_NULLABLE = 'NO' AND COLUMN_DEFAULT IS NULL")->queryAll();
        foreach ($tables_without_default_display_order as $table) {
            $this->alterOEColumn($table['TABLE_NAME'], 'display_order', 'INT(8) NOT NULL', true);
        }
    }
}
