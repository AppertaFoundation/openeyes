<?php

class m160517_083543_automated_event extends OEMigration
{
    public function up()
    {
        // was originally in the wrong place, so check the change hasn't already been applied
        $res = $this->dbConnection->createCommand("SHOW COLUMNS FROM `event` LIKE 'is_automated'")->queryAll();
        if (!count($res)) {
            $this->addColumn('event', 'is_automated', 'boolean');
            $this->addColumn('event', 'automated_source', 'text');
            $this->addColumn('event_version', 'is_automated', 'boolean');
            $this->addColumn('event_version', 'automated_source', 'text');

            $this->createIndex('event_is_automated', 'event', 'is_automated');
        }
    }

    public function down()
    {
        $this->dropColumn('event', 'is_automated');
        $this->dropColumn('event', 'automated_source');
        $this->dropColumn('event_version', 'is_automated');
        $this->dropColumn('event_version', 'automated_source');
    }
}
