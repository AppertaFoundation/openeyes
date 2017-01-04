<?php

class m161219_153512_genetic_test_version extends OEMigration
{
	public function up()
	{
        $this->versionExistingTable('ophingenetictest_test_method');
        $this->versionExistingTable('ophingenetictest_test_effect');
	}

	public function down()
	{
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