<?php

class m170814_140450_user_voice extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 21,
            'field_type_id' => 3,
            'key' => 'uservoice_enabled',
            'name' => 'UserVoice Enabled',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'on'
        ));
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 21,
            'field_type_id' => 3,
            'key' => 'uservoice_use_logged_in_user',
            'name' => 'UserVoice Use Logged In User',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'off'
        ));
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 22,
            'field_type_id' => 4,
            'key' => 'uservoice_override_account_id',
            'name' => 'UserVoice Override Account ID',
            'data' => '',
            'default_value' => ''
        ));
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 22,
            'field_type_id' => 4,
            'key' => 'uservoice_override_account_name',
            'name' => 'UserVoice Override Account Name',
            'data' => '',
            'default_value' => ''
        ));
        $this->insert('setting_installation', array(
            'key' => 'uservoice_use_logged_in_user',
            'value' => 'off'
        ));
        $this->insert('setting_installation', array(
            'key' => 'uservoice_enabled',
            'value' => 'on'
        ));
        $this->insert('setting_installation', array(
            'key' => 'uservoice_override_account_id',
            'value' => ''
        ));
        $this->insert('setting_installation', array(
            'key' => 'uservoice_override_account_name',
            'value' => ''
        ));
    }

    public function down()
    {
        $this->delete('setting_installation', '`key` = \'uservoice_use_logged_in_user\'');
        $this->delete('setting_installation', '`key` = \'uservoice_override_account_id\'');
        $this->delete('setting_installation', '`key` = \'uservoice_override_account_name\'');
        $this->delete('setting_installation', '`key` = \'uservoice_enabled\'');

        $this->delete('setting_metadata', '`key` = \'uservoice_use_logged_in_user\'');
        $this->delete('setting_metadata', '`key` = \'uservoice_override_account_id\'');
        $this->delete('setting_metadata', '`key` = \'uservoice_override_account_name\'');
        $this->delete('setting_metadata', '`key` = \'uservoice_enabled\'');
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
