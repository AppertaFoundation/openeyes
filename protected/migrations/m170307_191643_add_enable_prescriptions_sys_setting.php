<?php

class m170307_191643_add_enable_prescriptions_sys_setting extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 20,
            'field_type_id' => 3,
            'key' => 'enable_prescriptions_edit',
            'name' => 'Enable prescription editing',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'on'
        ));
    }

    public function down()
    {
        $this->delete('setting_metadata', '`key` = \'enable_prescriptions_edit\'');
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