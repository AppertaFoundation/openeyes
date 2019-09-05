<?php

class m190904_110852_add_med_stop_reason extends CDbMigration
{
    public function up()
    {
        $this->insert('ophciexamination_medication_stop_reason', [
            'active' => 1,
            'name' => 'Medication parameters changed',
            'display_order' => 22,
        ]);
        $this->update('ophciexamination_medication_stop_reason', ['display_order' => 23], 'name=:name', [':name' => 'Other']);
    }

    public function down()
    {
        $this->delete('ophciexamination_medication_stop_reason', 'name=:name', [':name' => 'Other']);
        $this->update('ophciexamination_medication_stop_reason', ['display_order' => 22], 'name=:name', [':name' => 'Other']);
    }
}