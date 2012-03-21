<?php

class m120321_172135_is_doctor_field_on_user_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('user','is_doctor','tinyint(1) unsigned NOT NULL DEFAULT 0');

		$this->getDbConnection()->createCommand("update user set is_doctor=1 where qualifications != '' and qualifications != '.'")->execute();
	}

	public function down()
	{
		$this->dropColumn('user','is_doctor');
	}
}
