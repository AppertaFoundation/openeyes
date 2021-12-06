<?php

class m211113_091100_create_indexes_on_event_medication_use extends OEMigration
{

    private $table = 'event_medication_use';

    public function up()
    {
        $this->createIndex('idx_event_medication_use_latest_med_use_id',$this->table, 'latest_med_use_id');
        $this->createIndex('idx_event_medication_use_prescribe',$this->table, 'prescribe');
    }

    public function down()
    {
        $this->dropIndex('idx_event_medication_use_latest_med_use_id',$this->table);
        $this->dropIndex('idx_event_medication_use_prescribe',$this->table);
    }
}
