<?php

class m200706_024925_create_operationchecklists_event_draft_table extends OEMigration
{
    public function up()
    {
        $this->createTable('ophtroperationchecklists_event', array(
            'id' => 'pk',
            'event_id' => 'INT(10) unsigned NOT NULL',
            'draft' => 'tinyint',
        ));

        $this->addForeignKey(
            'ophtroperationchecklists_event_eid_fk',
            'ophtroperationchecklists_event',
            'event_id',
            'event',
            'id'
        );
    }

    public function down()
    {
        $this->dropTable('ophtroperationchecklists_event');
    }
}
