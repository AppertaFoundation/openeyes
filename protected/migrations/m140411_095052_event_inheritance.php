<?php

class m140411_095052_event_inheritance extends CDbMigration
{
	public function up()
	{
		$this->addColumn('event_type', 'parent_event_type_id', 'int(10) unsigned');
		$this->addForeignKey('event_type_pevid_fk', 'event_type', 'parent_event_type_id', 'event_type', 'id');
		$this->addColumn('event_type_version', 'parent_event_type_id', 'int(10) unsigned');
	}

	public function down()
	{
		$this->dropColumn('event_type_version', 'parent_event_type_id');
		$this->dropForeignKey('event_type_pevid_fk', 'event_type');
		$this->dropColumn('event_type', 'parent_event_type_id');
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