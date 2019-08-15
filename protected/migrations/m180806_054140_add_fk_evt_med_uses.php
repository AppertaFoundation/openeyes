<?php

class m180806_054140_add_fk_evt_med_uses extends CDbMigration
{
	public function up()
	{
	    $this->addForeignKey('fk_emu_duration', 'event_medication_use', 'duration_id', 'drug_duration', 'id');
	}

	public function down()
	{
		$this->dropForeignKey('fk_emu_duration', 'event_medication_use');
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