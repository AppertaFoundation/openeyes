<?php

class m161220_111436_investigator_version extends OEMigration
{
	public function up()
	{
        $this->versionExistingTable('ophindnaextraction_dnatests_investigator');
	}

	public function down()
	{
		echo "m161220_111436_investigator_version does not support migration down.\n";
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