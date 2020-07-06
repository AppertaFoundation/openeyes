<?php

class m200706_024924_create_theatre_admission_event_draft_table extends OEMigration
{
    public function up()
    {
        $this->createTable('ophcitheatreadmission_event', array(
            'id' => 'pk',
            'event_id' => 'INT(10) unsigned NOT NULL',
            'draft' => 'tinyint',
        ));

        $this->addForeignKey(
            'ophcitheatreadmission_eventid_fk',
            'ophcitheatreadmission_event',
            'event_id',
            'event',
            'id'
        );
    }

    public function down()
    {
        $this->dropTable('ophcitheatreadmission_event');
    }
}
