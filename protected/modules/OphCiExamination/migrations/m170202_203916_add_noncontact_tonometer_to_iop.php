<?php

class m170202_203916_add_noncontact_tonometer_to_iop extends CDbMigration
{
    public function up()
    {
        $this->insert('ophciexamination_instrument', array('name' => 'Non-contact Tonometer', 'display_order'=>10));
    }

    public function down()
    {
        $this->delete('ophciexamination_instrument', 'name = :name', array(':name'=>'Non-contact Tonometer'));
    }
}
