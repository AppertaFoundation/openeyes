<?php

class m180405_095159_add_contact_details_element extends OEMigration
{
    public function up()
    {
        $this->createElementType('OphTrOperationbooking', 'Contact Details', array(
            'class_name' => 'Element_OphTrOperationbooking_ContactDetails',
            'display_order' => 40,
            'default' => 1,
            'required' => 1
        ));

        $this->createOETable('et_ophtroperationbooking_contact_details', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned',
            'collector_name' => 'varchar(255)',
            'collector_contact_number' => 'varchar(15)',
            'patient_booking_contact_number' => 'varchar(15)'
        ), true);

        $this->addForeignKey('et_ophtroperationbooking_contact_details_event_fk', 'et_ophtroperationbooking_contact_details', 'event_id', 'event', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('et_ophtroperationbooking_contact_details_event_fk', 'et_ophtroperationbooking_contact_details');
        $this->dropTable('et_ophtroperationbooking_contact_details');
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