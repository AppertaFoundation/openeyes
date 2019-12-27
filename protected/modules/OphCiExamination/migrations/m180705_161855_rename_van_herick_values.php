<?php

class m180705_161855_rename_van_herick_values extends CDbMigration
{
    public function up()
    {
        $this->update('ophciexamination_van_herick', ['name' => 'Grade 0 (0-5%)'], 'name = "5%"');
        $this->update('ophciexamination_van_herick', ['name' => 'Grade 1 (6-15%)'], 'name = "15%"');
        $this->update('ophciexamination_van_herick', ['name' => 'Grade 1 (16-25%)'], 'name = "25%"');
        $this->update('ophciexamination_van_herick', ['name' => 'Grade 2 (26-30%)'], 'name = "30%"');
        $this->update('ophciexamination_van_herick', ['name' => 'Grade 3 (31-75%)'], 'name = "75%"');
        $this->update('ophciexamination_van_herick', ['name' => 'Grade 4 (76-100%)'], 'name = "100%"');
    }

    public function down()
    {
        $this->update('ophciexamination_van_herick', ['name' => '5%'], 'name = "Grade 0 (0-5%)"');
        $this->update('ophciexamination_van_herick', ['name' => '15%'], 'name = "Grade 1 (6-15%)"');
        $this->update('ophciexamination_van_herick', ['name' => '25%'], 'name = "Grade 1 (16-25%)"');
        $this->update('ophciexamination_van_herick', ['name' => '30%'], 'name = "Grade 2 (26-30%)"');
        $this->update('ophciexamination_van_herick', ['name' => '75%'], 'name = "Grade 3 (31-75%)"');
        $this->update('ophciexamination_van_herick', ['name' => '100%'], 'name = "Grade 4 (76-100%)"');
    }
}