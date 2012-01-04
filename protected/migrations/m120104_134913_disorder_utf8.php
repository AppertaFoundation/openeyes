<?php

class m120104_134913_disorder_utf8 extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('disorder', 'fully_specified_name', 'char(255) CHARACTER SET utf8 NOT NULL');
		$this->alterColumn('disorder', 'term', 'char(255) CHARACTER SET utf8 NOT NULL');
	}

	public function down()
	{
		$this->alterColumn('disorder', 'fully_specified_name', 'char(255) CHARACTER SET latin1 NOT NULL');
		$this->alterColumn('disorder', 'term', 'char(255) CHARACTER SET latin1 NOT NULL');
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
