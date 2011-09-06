<?php

class m110906_100325_add_non_admin_user extends CDbMigration
{
	public function up()
	{
		$this->insert('user', array(
			'username' => 'user',
			'first_name' => 'Joe',
			'last_name' => 'Bloggs',
			'email' => 'user@example.com',
			'active' => true,
			'password' => '3653a08a907906f6fc462b81aba2aa87', // user
			'salt' => 'NM7449bLqX',
			'global_firm_rights' => false,
			'title' => 'Mr',
			'qualifications' => '',
			'role' => 'user role',
		));
	}

	public function down()
	{
		$this->delete('user', 'username = :name', array(':name' => 'user'));
	}
}