<?php

class m190411_094429_insert_row_none_into_ophinbiometry_lenstype_lens extends CDbMigration
{
    public function up()
    {
        $this->insert('ophinbiometry_lenstype_lens', array('name' => 'None', 'display_name' => '-', 'active' => '1'));
    }

    public function down()
    {
        $this->delete('ophinbiometry_lenstype_lens', '`name`="None"');
    }
}