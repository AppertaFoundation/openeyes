<?php

class m160902_093913_add_la_details_to_demographics extends CDbMigration
{
	public function up()
	{
		$this->addColumn('et_ophcocvi_demographics', 'la_name', 'varchar(255)');
		$this->addColumn('et_ophcocvi_demographics_version', 'la_name', 'varchar(255)');
		$this->addColumn('et_ophcocvi_demographics', 'la_address', 'text');
		$this->addColumn('et_ophcocvi_demographics_version', 'la_address', 'text');
		$this->addColumn('et_ophcocvi_demographics', 'la_telephone', 'varchar(20)');
		$this->addColumn('et_ophcocvi_demographics_version', 'la_telephone', 'varchar(20)');

	}

	public function down()
	{
		$this->dropColumn('et_ophcocvi_demographics', 'la_name');
		$this->dropColumn('et_ophcocvi_demographics_version', 'la_name');
		$this->dropColumn('et_ophcocvi_demographics', 'la_address');
		$this->dropColumn('et_ophcocvi_demographics_version', 'la_address');
		$this->dropColumn('et_ophcocvi_demographics', 'la_telephone');
		$this->dropColumn('et_ophcocvi_demographics_version', 'la_telephone');
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