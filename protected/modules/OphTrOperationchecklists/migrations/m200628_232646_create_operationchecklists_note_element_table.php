<?php

class m200628_232646_create_operationchecklists_note_element_table extends OEMigration
{
    public function up()
    {
        // Creating Table
        $this->createOETable('et_ophtroperationchecklists_note', array(
            'id' => 'pk',
            'event_id' => 'int unsigned NOT NULL',
            ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'et_ophtroperationchecklists_note_ev_fk',
            'et_ophtroperationchecklists_note',
            'event_id',
            'event',
            'id'
        );

        $this->createOETable('ophtroperationchecklists_notes', array(
            'id' => 'pk',
            'element_id' => 'int(11)',
            'notes' => 'text',
        ), true);

        $this->addForeignKey(
            'ophtroperationchecklists_notes_eid_fk',
            'ophtroperationchecklists_notes',
            'element_id',
            'et_ophtroperationchecklists_note',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable('ophtroperationchecklists_notes', true);
        $this->dropOETable('et_ophtroperationchecklists_note', true);
    }
}
