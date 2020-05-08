<?php

class m141104_181302_ticketassignoutcomes extends OEMigration
{
    public function up()
    {
        $this->createOETable('patientticketing_ticketassignoutcomeoption', array(
                'id' => 'pk',
                'name' => 'string NOT NULL',
                'display_order' => 'integer NOT NULL',
                'episode_status_id' => 'int(10) unsigned',
                'followup' => 'boolean DEFAULT false',
            ), true);

        $this->addForeignKey(
            'patientticketing_ticketassignoutcomeoption_epstid',
            'patientticketing_ticketassignoutcomeoption',
            'episode_status_id',
            'episode_status',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable('patientticketing_ticketassignoutcomeoption', true);
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
