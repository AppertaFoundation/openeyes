<?php

class m170204_085917_add_iot_webstar_other extends CDbMigration
{
    public function up()
    {
        $this->insert('ophciexamination_instrument', array('name'=>'Webstar-Other', 'visible'=>0));

    }

    public function down()
    {
        $this->delete('ophciexamination_instrument', array('name'=>'Webstar-Other'));
    }
}