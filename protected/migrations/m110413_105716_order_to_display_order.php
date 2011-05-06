<?php

class m110413_105716_order_to_display_order extends CDbMigration
{
	public function up()
	{
		$this->renameColumn('possible_element_type', 'order', 'display_order');
		$this->renameColumn('letter_phrase', 'order', 'display_order');
		$this->renameColumn('exam_phrase', 'order', 'display_order');
	}

	public function down()
	{
		$this->renameColumn('possible_element_type', 'display_order', 'order');
		$this->renameColumn('letter_phrase', 'display_order', 'order');
		$this->renameColumn('exam_phrase', 'display_order', 'order');
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
