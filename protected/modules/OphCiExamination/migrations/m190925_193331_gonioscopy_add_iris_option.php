<?php

class m190925_193331_gonioscopy_add_iris_option extends CDbMigration
{
    public function up()
    {
        $this->insert('ophciexamination_gonioscopy_iris', ['name' => 'Steep', 'display_order' => '5']);
    }

    public function down()
    {
        $this->delete('ophciexamination_gonioscopy_iris', "name = :name", [':name' => 'Steep']);
    }
}