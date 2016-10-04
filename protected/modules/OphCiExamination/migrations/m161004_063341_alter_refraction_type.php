<?php

class m161004_063341_alter_refraction_type extends CDbMigration
{
	public function up()
	{
        $this->update('ophciexamination_refraction_type', array('name' => 'Focimetry'), 'name = "Own Glasses"');
	}

	public function down()
	{
	    return true;
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