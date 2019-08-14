<?php

class m180712_090533_add_element_close_warning_enabled_setting extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 22,
            'field_type_id' => 3,
            'key' => 'element_close_warning_enabled',
            'name' => 'Require Confirmation to close elements',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'off'
        ));
        $this->insert('setting_installation', array(
            'key' => 'element_close_warning_enabled',
            'value' => 'off'
        ));
    }


    public function down()
    {
        $this->delete('setting_installation', '`key`="element_close_warning_enabled"');
        $this->delete('setting_metadata', '`key`="element_close_warning_enabled"');

    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
