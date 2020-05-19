<?php

class m160615_104222_OE5989_create_whiteboard extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'ophtroperationbooking_whiteboard',
            array(
                'id' => 'pk',
                'event_id' => 'int(10) unsigned not null',
                'eye_id' => 'int(10) unsigned not null',
                'patient_name' => 'varchar(255)',
                'date_of_birth' => 'date not null',
                'hos_num' => 'varchar(40)',
                'procedure' => 'varchar(255)',
                'allergies' => 'varchar(255)',
                'iol_model' => 'varchar(255)',
                'iol_power' => 'varchar(15)',
                'predicted_additional_equipment' => 'varchar(255)',
                'predicted_refractive_outcome' => 'decimal(5,2)',
                'comments' => 'text',
                'alpha_blockers' => 'tinyint',
                'anticoagulants' => 'tinyint',
                'inr' => 'varchar(255)',
            ),
            true
        );

        $this->addForeignKey('whiteboard_booking_event', 'ophtroperationbooking_whiteboard', 'event_id', 'event', 'id');
        $this->addForeignKey('whiteboard_booking_eye', 'ophtroperationbooking_whiteboard', 'eye_id', 'eye', 'id');
    }

    public function down()
    {
        $this->dropTable('ophtroperationbooking_whiteboard_version');
        $this->dropTable('ophtroperationbooking_whiteboard');
    }
}
