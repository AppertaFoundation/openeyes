<?php

class m200630_223053_create_operationchecklists_event_element_set_assignment_table extends OEMigration
{
    public function up()
    {
        // Creating Table
        $this->createOETable('ophtroperationchecklists_event_elementset_assignment', array(
            'id' => 'pk',
            'step_id' => 'int(11)',
            'event_id' => 'int unsigned NOT NULL',
        ), true);

        $this->addForeignKey(
            'ophtroperationchecklists_event_elementset_assignment_sid_fk',
            'ophtroperationchecklists_event_elementset_assignment',
            'step_id',
            'ophtroperationchecklists_element_set',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_event_elementset_assignment_eid_fk',
            'ophtroperationchecklists_event_elementset_assignment',
            'event_id',
            'event',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable('ophtroperationchecklists_event_elementset_assignment', true);
    }
}
