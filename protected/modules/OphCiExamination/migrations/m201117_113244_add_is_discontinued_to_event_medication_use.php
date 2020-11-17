<?php

class m201117_113244_add_is_discontinued_to_event_medication_use extends OEMigration
{

    public function safeUp()
    {
        $this->addOEColumn('event_medication_use', 'is_discontinued', 'tinyint(1) unsigned default 0', true);
    }

    public function safeDown()
    {
        $this->dropOEColumn('event_medication_use', 'is_discontinued', true);
    }
}
