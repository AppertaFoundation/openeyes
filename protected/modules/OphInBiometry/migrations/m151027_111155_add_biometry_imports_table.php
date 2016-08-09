<?php

class m151027_111155_add_biometry_imports_table extends CDbMigration
{
    public function up()
    {
        $this->createTable('ophinbiometry_imported_events', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'event_id' => 'int(10) unsigned NOT NULL',
            'patient_id' => 'int(10) unsigned NOT NULL',
            'study_id' => 'varchar(255)',
            'device_id' => 'varchar(255)',
            'device_name' => 'varchar(255)',
            'device_model' => 'varchar(255)',
            'device_manufacturer' => 'varchar(255)',
            'device_software_version' => 'varchar(20)',
            'is_linked' => 'boolean default false',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'PRIMARY KEY (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey('ophinbiometry_imported_events_event_id_fk', 'ophinbiometry_imported_events', 'event_id', 'event', 'id');
        $this->addForeignKey('ophinbiometry_imported_events_patient_id_fk', 'ophinbiometry_imported_events', 'patient_id', 'patient', 'id');
        $this->addForeignKey('ophinbiometry_imported_events_last_modified_user_id_fk', 'ophinbiometry_imported_events', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('ophinbiometry_imported_events_created_user_id_fk', 'ophinbiometry_imported_events', 'created_user_id', 'user', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('ophinbiometry_imported_events_event_id_fk', 'ophinbiometry_imported_events');
        $this->dropForeignKey('ophinbiometry_imported_events_patient_id_fk', 'ophinbiometry_imported_events');
        $this->dropForeignKey('ophinbiometry_imported_events_last_modified_user_id_fk', 'ophinbiometry_imported_events');
        $this->dropForeignKey('ophinbiometry_imported_events_created_user_id_fk', 'ophinbiometry_imported_events');
        $this->addColumn('et_ophinbiometry_measurement', 'study_id', 'varchar(255)');
        $this->addColumn('et_ophinbiometry_measurement', 'device_id', 'varchar(255)');
        $this->addColumn('et_ophinbiometry_measurement', 'study_id_version', 'varchar(255)');
        $this->addColumn('et_ophinbiometry_measurement', 'device_id_version', 'varchar(255)');

        $this->dropTable('ophinbiometry_imported_events');
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
