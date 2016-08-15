<?php

class m151123_164655_change_dicom_import_log_table extends OEMigration
{
    public function up()
    {
        $this->renameTable('audit_dicom_import', 'dicom_import_log');
        $this->addColumn('dicom_import_log', 'import_type', 'varchar(10)');
        $this->addColumn('dicom_import_log', 'file_name', 'varchar(255)');
        $this->addColumn('dicom_import_log', 'file_path', 'varchar(400)');
        $this->addColumn('dicom_import_log', 'raw_importer_output', 'text');
    }

    public function down()
    {
        $this->dropColumn('dicom_import_log', 'import_type');
        $this->dropColumn('dicom_import_log', 'file_name');
        $this->dropColumn('dicom_import_log', 'file_path');
        $this->dropColumn('dicom_import_log', 'raw_importer_output');
        $this->renameTable('dicom_import_log', 'audit_dicom_import');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
