<?php

class m160520_095447_reorder_none_complication extends CDbMigration
{
	public function up()
	{
		$this->update('ophtroperationnote_anaesthetic_anaesthetic_complications', array('display_order' => 0), 'name = "None"');
	}

	public function down()
	{
		echo "m160520_095447_reorder_none_complication does not support migration down.\n";
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
