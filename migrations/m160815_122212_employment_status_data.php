<?php

class m160815_122212_employment_status_data extends CDbMigration
{
	public function up()
	{
		$this->insert('ophcocvi_clericinfo_employment_status',array('name'=>'Retired','display_order'=>1));
		$this->insert('ophcocvi_clericinfo_employment_status',array('name'=>'Employed','display_order'=>1));
		$this->insert('ophcocvi_clericinfo_employment_status',array('name'=>'Unemployed','display_order'=>1));
		$this->insert('ophcocvi_clericinfo_employment_status',array('name'=>'Child','display_order'=>1));
		$this->insert('ophcocvi_clericinfo_employment_status',array('name'=>'Student','display_order'=>1));
	}

	public function down()
	{
		echo "m160815_122212_employment_status_data does not support migration down.\n";
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