<?php

class m151123_173403_create_dicom_file_watcher_tables extends CDbMigration
{
    public function up()
    {
        $this->createTable('dicom_file_log', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'event_date_time' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'filename' => 'varchar(500) NOT NULL',
            'status' => 'varchar(20)',
            'process_name' => 'varchar(500)',
        ));

        $this->createTable('dicom_file_queue', array(
            'id' => 'int(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'filename' => 'varchar(500) NOT NULL',
            'detected_date' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'last_modified_date' => 'TIMESTAMP NOT NULL',
            'status_id' => 'int(10) unsigned NOT NULL',
        ));

        $this->createTable('dicom_process_status', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'name' => 'varchar(255)',
        ));

        $this->insert('dicom_process_status', array('name' => 'new'));
        $this->insert('dicom_process_status', array('name' => 'in_progress'));
        $this->insert('dicom_process_status', array('name' => 'failed'));
        $this->insert('dicom_process_status', array('name' => 'success'));

        $this->addForeignKey(
            'dicom_process_status_id_fk',
            'dicom_file_queue',
            'status_id',
            'dicom_process_status',
            'id'
        );
    }

    public function down()
    {
        $this->dropTable('dicom_file_log');
        $this->dropTable('dicom_file_queue');
        $this->dropTable('dicom_process_status');
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
