<?php

class m201112_115747_add_stopped_in_event_id_field_to_event_medication_use extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('event_medication_use', 'stopped_in_event_id', 'int unsigned null default null', true);
    }

    public function safeDown()
    {
        $this->dropOEColumn('event_medication_use', 'stopped_in_event_id', true);
    }
}
