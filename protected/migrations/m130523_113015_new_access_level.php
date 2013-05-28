<?php

class m130523_113015_new_access_level extends CDbMigration
{
	public function up()
	{
		Yii::app()->db->createCommand("update user set access_level = access_level + 1 where access_level >= 3")->query();
	}

	public function down()
	{
		Yii::app()->db->createCommand("update user set access_level = access_level - 1 where access_level >= 4")->query();
	}
}
