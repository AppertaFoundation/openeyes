<?php

class m200812_085129_remove_drug_duration_foreign_key_in_event_medication_use extends OEMigration
{

    public function safeUp()
    {
        $this->dropForeignKey('fk_emu_duration', 'event_medication_use');
        $this->alterOEColumn('event_medication_use', 'duration_id', 'int(11)', true);
        $this->addForeignKey('fk_emu_medication_duration', 'event_medication_use', 'duration_id', 'medication_duration', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_emu_medication_duration', 'event_medication_use');
        echo "Unable to  add foreign key to non-existent table";
    }
}
