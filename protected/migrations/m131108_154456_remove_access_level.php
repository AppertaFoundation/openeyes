<?php

class m131108_154456_remove_access_level extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('user', 'access_level');
	}

	public function down()
	{
		$this->addColumn('user', 'access_level', 'tinyint(1) unsigned not null default 0');

		$this->execute('update user u set u.access_level = 1 where exists (select * from authassignment where userid = u.id and itemname = "User")');
		$this->execute('update user u set u.access_level = 2 where exists (select * from authassignment where userid = u.id and itemname = "View clinical")');
		$this->execute('update user u set u.access_level = 3 where exists (select * from authassignment where userid = u.id and itemname = "Print")');
		$this->execute('update user u set u.access_level = 4 where exists (select * from authassignment where userid = u.id and itemname = "Edit")');
		$this->execute('update user u set u.access_level = 5 where exists (select * from authassignment where userid = u.id and itemname = "Prescribe")');
	}
}
