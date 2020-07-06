<?php

class m200628_232645_create_theatre_admission_case_notes_element extends OEMigration
{
    public function up()
    {
        // Creating Table
        $this->createOETable('et_ophcitheatreadmission_case_note', array(
            'id' => 'pk',
            'event_id' => 'int unsigned NOT NULL',
            ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'et_ophcitheatreadmission_case_note_ev_fk',
            'et_ophcitheatreadmission_case_note',
            'event_id',
            'event',
            'id'
        );

        $this->createOETable('ophcitheatreadmission_case_notes', array(
            'id' => 'pk',
            'element_id' => 'int(11)',
            'case_notes' => 'text',
        ), true);

        $this->addForeignKey(
            'ophcitheatreadmission_cn_eid_fk',
            'ophcitheatreadmission_case_notes',
            'element_id',
            'et_ophcitheatreadmission_case_note',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable('ophcitheatreadmission_case_notes', true);
        $this->dropOETable('et_ophcitheatreadmission_case_note', true);
    }
}
