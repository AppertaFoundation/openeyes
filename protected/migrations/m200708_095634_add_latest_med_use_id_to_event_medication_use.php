<?php

class m200708_095634_add_latest_med_use_id_to_event_medication_use extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('event_medication_use', 'latest_med_use_id', 'INT unsigned NULL', true);
    }

    public function safeDown()
    {
        $this->dropOEColumn('event_medication_use', 'latest_med_use_id', true);
    }
}
