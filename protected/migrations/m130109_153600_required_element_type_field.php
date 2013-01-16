<?php

class m130109_153600_required_element_type_field extends CDbMigration
{
	public function up()
	{
		$this->addColumn('element_type', 'required', 'boolean default NULL');
	}

	public function down()
	{
		$this->dropColumn('element_type', 'required');
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