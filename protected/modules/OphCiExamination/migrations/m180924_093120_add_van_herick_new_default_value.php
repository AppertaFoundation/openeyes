<?php

class m180924_093120_add_van_herick_new_default_value extends CDbMigration
{
    public function up()
    {
        $this->insert('ophciexamination_van_herick', ['display_order' => 0 , 'name' => 'Ungraded']);
    }

    public function down()
    {
        $this->delete('ophciexamination_van_herick', 'name = :name', [':name' => 'Ungraded']);
    }
}
