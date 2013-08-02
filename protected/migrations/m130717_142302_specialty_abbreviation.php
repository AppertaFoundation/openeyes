<?php

class m130717_142302_specialty_abbreviation extends OEMigration
{
	public function up()
	{
		$this->addColumn('specialty','abbreviation','char(3) UNIQUE');
		
		$migrations_path = dirname(__FILE__);
		$this->initialiseData($migrations_path, 'code');
		
		$this->alterColumn('specialty', 'abbreviation', 'char(3) UNIQUE NOT NULL');
	}

	public function down()
	{
		$this->dropColumn('specialty', 'abbreviation');
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