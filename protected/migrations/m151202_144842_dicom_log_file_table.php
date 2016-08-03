<?php

class m151202_144842_dicom_log_file_table extends CDbMigration
{
    public function up()
    {
        $this->createTable('dicom_files', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'entry_date_time' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'filename' => 'varchar(500) NOT NULL',
            'filesize' => 'int(16)',
            'filedate' => 'TIMESTAMP',
            'processor_id' => 'varchar(20)',
        ));

        $this->dropColumn('dicom_file_log', 'filename');
        $this->addColumn('dicom_file_log', 'dicom_file_id', 'int(10) unsigned');
        $this->addForeignKey('dicom_file_id_ref', 'dicom_file_log', 'dicom_file_id', 'dicom_files', 'id');
    }

    public function down()
    {
        $this->dropTable('dicom_files');
        $this->dropForeignKey('dicom_file_id_ref', 'dicom_file_log');
        $this->dropColumn('dicom_file_log', 'dicom_file_id');
        $this->addColumn('dicom_file_log', 'filename', 'varchar(500)');
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
