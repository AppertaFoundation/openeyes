<?php

class m141209_160405_rename_table extends CDbMigration
{
    public function up()
    {
        $this->dropForeignKey('patientticketing_appointment_type_last_modified_user_id_fk', 'patientticketing_appointment_type');
        $this->dropForeignKey('patientticketing_appointment_type_created_user_id_fk', 'patientticketing_appointment_type');

        $this->dropIndex('patientticketing_appointment_type_last_modified_user_id_fk', 'patientticketing_appointment_type');
        $this->dropIndex('patientticketing_appointment_type_created_user_id_fk', 'patientticketing_appointment_type');

        $this->renameTable('patientticketing_appointment_type', 'patientticketing_clinic_location');

        $this->createIndex('patientticketing_clinic_location_last_modified_user_id_fk', 'patientticketing_clinic_location', 'last_modified_user_id');
        $this->createIndex('patientticketing_clinic_location_created_user_id_fk', 'patientticketing_clinic_location', 'created_user_id');

        $this->addForeignKey('patientticketing_clinic_location_last_modified_user_id_fk', 'patientticketing_clinic_location', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('patientticketing_clinic_location_created_user_id_fk', 'patientticketing_clinic_location', 'created_user_id', 'user', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('patientticketing_clinic_location_last_modified_user_id_fk', 'patientticketing_clinic_location');
        $this->dropForeignKey('patientticketing_clinic_location_created_user_id_fk', 'patientticketing_clinic_location');

        $this->dropIndex('patientticketing_clinic_location_last_modified_user_id_fk', 'patientticketing_clinic_location');
        $this->dropIndex('patientticketing_clinic_location_created_user_id_fk', 'patientticketing_clinic_location');

        $this->renameTable('patientticketing_clinic_location', 'patientticketing_appointment_type');

        $this->createIndex('patientticketing_appointment_type_last_modified_user_id_fk', 'patientticketing_appointment_type', 'last_modified_user_id');
        $this->createIndex('patientticketing_appointment_type_created_user_id_fk', 'patientticketing_appointment_type', 'created_user_id');

        $this->addForeignKey('patientticketing_appointment_type_last_modified_user_id_fk', 'patientticketing_appointment_type', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('patientticketing_appointment_type_created_user_id_fk', 'patientticketing_appointment_type', 'created_user_id', 'user', 'id');
    }
}
