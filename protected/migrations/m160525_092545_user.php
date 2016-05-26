<?php

class m160525_092545_user extends CDbMigration
{
	public function up()
	{
            
            $insert_portal_user = $this->execute("insert into user values ('0','portal_user', 'Portal', 'User', 'portal_user@openeyes.com', '1', '0', 'Mr', 'DR', 'OpTom Portal', NULL, 'a6be81a2523654545cae5707fa47bd3c', '98bAzbwQJ3', '1', '2016-05-16 14:17:56', '1', '2016-05-16 14:17:56', NULL, '0', '576750', NULL, '0', '0', '0', '0', NULL, 'admin')");
	}

	public function down()
	{
		echo "m160525_092545_user does not support migration down.\n";
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