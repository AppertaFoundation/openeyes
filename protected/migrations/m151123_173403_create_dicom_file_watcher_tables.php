<?php

class m151123_173403_create_dicom_file_log_table extends OEMigration
{
	public function up()
	{
		$this->createTable('dicom_file_log', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_date_time' => 'datetime '
		));

	}

	public function down()
	{
		echo "m151123_173403_create_dicom_file_log_table does not support migration down.\n";
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