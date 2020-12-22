<?php

class m180910_044802_uservoice_remove extends CDbMigration
{
    public function up()
    {
//      Removing uservoice settings from the portal
        $this->delete('setting_installation', '`key` = \'uservoice_use_logged_in_user\'');
        $this->delete('setting_installation', '`key` = \'uservoice_override_account_id\'');
        $this->delete('setting_installation', '`key` = \'uservoice_override_account_name\'');
        $this->delete('setting_installation', '`key` = \'uservoice_enabled\'');

        $this->delete('setting_metadata', '`key` = \'uservoice_use_logged_in_user\'');
        $this->delete('setting_metadata', '`key` = \'uservoice_override_account_id\'');
        $this->delete('setting_metadata', '`key` = \'uservoice_override_account_name\'');
        $this->delete('setting_metadata', '`key` = \'uservoice_enabled\'');

        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 22,
            'field_type_id' => 4,
            'key' => 'feedback_link',
            'name' => 'Feedback Link',
            'data' => '',
            'default_value' => ''
        ));
        $this->insert('setting_installation', array(
            'key' => 'feedback_link',
            'value' => 'https://forums.apperta.org/c/openeyes'
        ));
    }

    public function down()
    {
        $this->delete('setting_installation', '`key` = \'feedback_link\'');
        $this->delete('setting_metadata', '`key` = \'feedback_link\'');
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
