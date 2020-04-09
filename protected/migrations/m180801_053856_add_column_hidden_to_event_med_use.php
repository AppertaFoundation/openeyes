<?php

class m180801_053856_add_column_hidden_to_event_med_use extends CDbMigration
{
	public function up()
	{
	    $this->addColumn('event_medication_use', 'hidden', 'BOOLEAN NOT NULL DEFAULT 0');
	    $this->addColumn('event_medication_use_version', 'hidden', 'BOOLEAN NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('event_medication_use', 'hidden');
		$this->dropColumn('event_medication_use_version', 'hidden');
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
