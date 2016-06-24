<?php

class m160623_144438_event_creation extends OEMigration
{
    public function up()
    {
        $this->insertOEEventType('Lab Results', 'OphInLabResults', 'In');
    }

    public function down()
    {
        $this->delete('event_type', 'name = "Lab Results"');
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