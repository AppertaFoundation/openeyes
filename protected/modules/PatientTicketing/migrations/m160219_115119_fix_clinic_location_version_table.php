<?php

class m160219_115119_fix_clinic_location_version_table extends CDbMigration
{
    public function up()
    {
        $this->renameTable('patientticketing_appointment_type_version', 'patientticketing_clinic_location_version');
    }

    public function down()
    {
        $this->renameTable('patientticketing_clinic_location_version', 'patientticketing_appointment_type_version');
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
