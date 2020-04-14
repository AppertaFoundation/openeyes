<?php

class m190129_135906_add_not_checke_to_opticdisc extends CDbMigration
{
    public function up()
    {
        $this->insert('ophciexamination_opticdisc_cd_ratio', [
            'name' => 'Not checked',
            'display_order' => 0
        ]);
    }

    public function down()
    {
        $this->delete('ophciexamination_opticdisc_cd_ratio', "name = 'Not checked'");
    }
}
