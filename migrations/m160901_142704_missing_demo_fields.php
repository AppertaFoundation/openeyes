<?php

class m160901_142704_missing_demo_fields extends CDbMigration
{
	public function up()
	{
		$this->addColumn('et_ophcocvi_demographics', 'postcode', 'varchar(15)');
		$this->addColumn('et_ophcocvi_demographics_version', 'postcode', 'varchar(15)');
	}

	public function down()
	{
		$this->dropColumn('et_ophcocvi_demographics', 'postcode');
		$this->dropColumn('et_ophcocvi_demographics_version', 'postcode');
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