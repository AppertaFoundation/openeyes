<?php

class m170310_114213_add_new_system_setting extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array('display_order' => 0, 'field_type_id' => 3, 'key' => 'mandatory_post_op_instructions', 'name' => 'Mandatory Post-Op Instructions', 'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}', 'default_value' => 'off'));
    }

    public function down()
    {
        $this->delete('setting_metadata', '`key`="mandatory_post_op_instructions"');
    }

}
