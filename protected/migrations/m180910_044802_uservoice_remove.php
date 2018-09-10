<?php

class m180910_044802_uservoice_remove extends CDbMigration
{
	public function up()
	{
//	    Removing uservoice settings from the portal
        $this->delete('setting_installation', '`key` = \'uservoice_use_logged_in_user\'');
        $this->delete('setting_installation', '`key` = \'uservoice_override_account_id\'');
        $this->delete('setting_installation', '`key` = \'uservoice_override_account_name\'');
        $this->delete('setting_installation', '`key` = \'uservoice_enabled\'');

        $this->delete('setting_metadata', '`key` = \'uservoice_use_logged_in_user\'');
        $this->delete('setting_metadata', '`key` = \'uservoice_override_account_id\'');
        $this->delete('setting_metadata', '`key` = \'uservoice_override_account_name\'');
        $this->delete('setting_metadata', '`key` = \'uservoice_enabled\'');
	}

	public function down()
	{
		echo "m180910_044802_uservoice_remove does not support migration down.\n";
		return false;
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