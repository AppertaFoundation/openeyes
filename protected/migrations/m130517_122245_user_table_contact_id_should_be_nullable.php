<?php

class m130517_122245_user_table_contact_id_should_be_nullable extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('user','contact_id','int(10) unsigned NULL');
	}

	public function down()
	{
		$this->alterColumn('user','contact_id','int(10) unsigned NOT NULL');
	}
}
