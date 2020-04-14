<?php

class m180514_111030_fix_evt_med_uses_temp_presc_item extends CDbMigration
{
    public function up()
    {
        $this->addColumn('event_medication_use_version', 'temp_prescription_item_id', 'INT NULL');
    }

    public function down()
    {
        $this->dropColumn('event_medication_use_version', 'temp_prescription_item_id');
    }
}
