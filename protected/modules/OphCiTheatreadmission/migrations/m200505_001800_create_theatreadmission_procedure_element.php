<?php

class m200505_001800_create_theatreadmission_procedure_element extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Creating Table
        $this->createOETable(
            'et_ophcitheatreadmission_procedurelist',
            array(
                'id' => 'pk',
                'event_id' => 'int unsigned NOT NULL',
                'eye_id' => 'int(10) unsigned NOT NULL',
                'booking_event_id' => 'int(10) unsigned',
                'disorder_id' => 'BIGINT(20) UNSIGNED',
                'priority_id' => 'int unsigned',
            ),
            true
        );

        // Add Foreign Key
        $this->addForeignKey(
            'et_ophcitheatreadmission_procedurelist_eid_fk',
            'et_ophcitheatreadmission_procedurelist',
            'event_id',
            'event',
            'id'
        );

        $this->addForeignKey(
            'et_ophcitheatreadmission_procedurelist_eye_id_fk',
            'et_ophcitheatreadmission_procedurelist',
            'eye_id',
            'eye',
            'id'
        );

        $this->addForeignKey(
            'et_ophcitheatreadmission_procedurelist_bei_fk',
            'et_ophcitheatreadmission_procedurelist',
            'booking_event_id',
            'event',
            'id'
        );

        $this->addForeignKey(
            'et_ophcitheatreadmission_procedurelist_diagnosis_disorder',
            'et_ophcitheatreadmission_procedurelist',
            'disorder_id',
            'disorder',
            'id'
        );

        $this->addForeignKey(
            'et_ophcitheatreadmission_procedurelist_priority_fk',
            'et_ophcitheatreadmission_procedurelist',
            'priority_id',
            'ophtroperationbooking_operation_priority',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropOETable('et_ophcitheatreadmission_procedurelist', true);
    }
}
