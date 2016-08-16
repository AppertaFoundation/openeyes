<?php

class m151202_144929_dicom_log_file_table_reference extends CDbMigration
{
    public function up()
    {
        $this->dropColumn('dicom_import_log', 'file_name');
        $this->dropColumn('dicom_import_log', 'file_path');
        $this->addColumn('dicom_import_log', 'dicom_file_id', 'int(10) unsigned');
        $this->addForeignKey('dicom_import_file_id_ref', 'dicom_import_log', 'dicom_file_id', 'dicom_files', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('dicom_import_file_id_ref', 'dicom_import_log');
        $this->dropColumn('dicom_import_log', 'dicom_file_id');
        $this->addColumn('dicom_import_log', 'file_name', 'varchar(255)');
        $this->addColumn('dicom_import_log', 'file_path', 'varchar(400)');
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
