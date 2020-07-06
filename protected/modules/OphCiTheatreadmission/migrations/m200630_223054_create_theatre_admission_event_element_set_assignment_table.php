<?php

class m200630_223054_create_theatre_admission_event_element_set_assignment_table extends OEMigration
{
    public function up()
    {
        // Creating Table
        $this->createOETable('ophcitheatreadmission_event_elementset_assignment', array(
            'id' => 'pk',
            'step_id' => 'int(11)',
            'event_id' => 'int unsigned NOT NULL',
        ), true);

        $this->addForeignKey(
            'ophcitheatreadmission_eea_sid_fk',
            'ophcitheatreadmission_event_elementset_assignment',
            'step_id',
            'ophcitheatreadmission_element_set',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_eea_eid_fk',
            'ophcitheatreadmission_event_elementset_assignment',
            'event_id',
            'event',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable('ophcitheatreadmission_event_elementset_assignment', true);
    }
}
