<?php

class m181228_044926_add_ethics_number_to_trial extends CDbMigration
{
	public function safeUp()
	{
        $this->addColumn('trial', 'ethics_number', 'integer');
	}

	public function safeDown()
	{
        $this->dropColumn('trial','ethics_number');
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