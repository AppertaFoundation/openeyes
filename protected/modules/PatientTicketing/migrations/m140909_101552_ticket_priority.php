<?php

class m140909_101552_ticket_priority extends OEMigration
{
    public function up()
    {
        $this->addColumn('patientticketing_queueset', 'allow_null_priority', 'boolean DEFAULT false NOT NULL');
        $this->addColumn('patientticketing_queueset_version', 'allow_null_priority', 'boolean DEFAULT false NOT NULL');
    }

    public function down()
    {
        $this->dropColumn('patientticketing_queueset_version', 'allow_null_priority');
        $this->dropColumn('patientticketing_queueset', 'allow_null_priority');
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
