<?php

class m121204_102309_parent_elements extends CDbMigration
{
	public function up()
	{
		$this->addColumn('element_type', 'parent_element_type_id', 'int(10) unsigned default NULL');
		$this->addForeignKey('element_type_parent_et_fk', 'element_type', 'parent_element_type_id', 'element_type', 'id');
	}

	public function down()
	{
		$this->dropForeignKey('element_type_parent_et_fk', 'element_type');
		$this->dropColumn('element_type', 'parent_element_type_id');
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