<?php

class m230214_065937_add_event_draft_table extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('event_draft',
            [
                'id' => 'pk',
                'institution_id' => 'int(10) unsigned NOT NULL',
                'site_id' => 'int(10) unsigned',
                'episode_id' => 'int(10) unsigned NOT NULL',
                'event_type_id' => 'int(10) unsigned NOT NULL',
                'event_id' => 'int unsigned',
                'originating_url' => 'tinytext',
                'data' => 'json',
            ]
        );

        $this->addForeignKey('fk_event_draft_iid', 'event_draft', 'institution_id', 'institution', 'id');
        $this->addForeignKey('fk_event_draft_sid', 'event_draft', 'site_id', 'site', 'id');
        $this->addForeignKey('fk_event_draft_epid', 'event_draft', 'episode_id', 'episode', 'id');
        $this->addForeignKey('fk_event_draft_evtid', 'event_draft', 'event_type_id', 'event_type', 'id');
        $this->addForeignKey('fk_event_draft_evid', 'event_draft', 'event_id', 'event', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_event_draft_evid', 'event_draft');
        $this->dropForeignKey('fk_event_draft_evtid', 'event_draft');
        $this->dropForeignKey('fk_event_draft_epid', 'event_draft');
        $this->dropForeignKey('fk_event_draft_sid', 'event_draft');
        $this->dropForeignKey('fk_event_draft_iid', 'event_draft');

        $this->dropTable('event_draft');
    }
}
