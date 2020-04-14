<?php

class m171109_022117_add_firm_id_to_event extends OEMigration
{
    public function safeUp()
    {
        $this->addColumn('event', 'firm_id', 'int(10) unsigned');
        $this->addColumn('event_version', 'firm_id', 'int(10) unsigned');

        $this->addForeignKey('event_firm_fk', 'event', 'firm_id', 'firm', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('event_firm_fk', 'event');
        $this->dropColumn('event', 'firm_id');
        $this->dropColumn('event_version', 'firm_id');
    }
}
