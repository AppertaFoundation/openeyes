<?php

class m120112_120315_default_nonadmin_user extends CDbMigration
{
	public function up()
	{
		$this->insert('user', array(
			'username' => 'username',
			'first_name' => 'default',
			'last_name' => 'user',
			'email' => 'defaultuser@opeenyes.org.uk',
			'active' => 1,
			'global_firm_rights' => 1,
			'title' => 'mr',
			'qualifications' => '',
			'role' => '',
			'code' => '',
			'password' => '49ce5e1189de532d9e157ed07e749c87',
			'salt' => 'FbYJis0YG3'
		));	
	}

	public function down()
	{
		echo "m120112_120315_default_nonadmin_user does not support migration down.\n";
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
