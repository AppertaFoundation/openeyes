<?php

class m140618_112350_extra_lens_data extends CDbMigration
{
    public function up()
    {
        $this->insert('ophinbiometry_lenstype_lens', array('name' => 'SA60AT', 'display_order' => 3));
        $this->insert('ophinbiometry_lenstype_lens', array('name' => 'MTA3UO', 'display_order' => 4));
    }

    public function down()
    {
        $this->dbConnection->createCommand('delete from ophinbiometry_lenstype_lens where name="SA60AT"')->query();
        $this->dbConnection->createCommand('delete from ophinbiometry_lenstype_lens where name="MTA3UO"')->query();
    }
}
