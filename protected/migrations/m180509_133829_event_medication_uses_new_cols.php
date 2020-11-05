<?php

class m180509_133829_event_medication_uses_new_cols extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('event_medication_use', 'stop_reason_id', 'INT NULL', true);

        $this->addOEColumn('event_medication_use', 'prescription_item_id', 'INT NULL', true);
        
        $this->addForeignKey('fk_emu_stop_reason', 'event_medication_use', 'stop_reason_id', 'ophciexamination_medication_stop_reason', 'id');
        $this->addForeignKey('fk_emu_prescription_item', 'event_medication_use', 'prescription_item_id', 'event_medication_use', 'id');
        
        $this->addOEColumn('event_medication_use', 'temp_prescription_item_id', 'INT NULL', true);
        // Add an index to speedup some later migrations - this column and index will de dropped at the end of the import
        $this->createIndex('idx_temp_prescription_item_id', 'event_medication_use', 'temp_prescription_item_id', false);
    }

    public function down()
    {
        $this->dropForeignKey('fk_emu_stop_reason', 'event_medication_use');
        $this->dropForeignKey('fk_emu_prescription_item', 'event_medication_use');
        $this->dropColumn('event_medication_use', 'temp_prescription_item_id');
        $this->dropColumn('event_medication_use', 'stop_reason_id');
        $this->dropColumn('event_medication_use_version', 'stop_reason_id');
        $this->dropColumn('event_medication_use', 'prescription_item_id');
        $this->dropColumn('event_medication_use_version', 'prescription_item_id');
    }
}
