<?php

class m190308_144937_rename_van_herick_thirty_percent extends CDbMigration
{
    public function up()
    {
        $this->update('ophciexamination_van_herick', ['name' => 'Grade 2 (26-40%)'], 'name = "Grade 2 (26-30%)"');
        $this->update('ophciexamination_van_herick', ['name' => 'Grade 3 (41-75%)'], 'name = "Grade 3 (31-75%)"');
    }

    public function down()
    {
        $this->update('ophciexamination_van_herick', ['name' => 'Grade 2 (26-30%)'], 'name = "Grade 2 (26-40%)"');
        $this->update('ophciexamination_van_herick', ['name' => 'Grade 3 (31-75%)'], 'name = "Grade 3 (41-75%)"');
    }
}
