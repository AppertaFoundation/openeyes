<?php

class m161219_152500_new_dna_fields extends CDbMigration
{
	public function up()
	{
        $this->addColumn('et_ophingenetictest_test', 'dna_quality', 'FLOAT NULL AFTER exon');
        $this->addColumn('et_ophingenetictest_test', 'dna_quantity', 'FLOAT NULL AFTER dna_quality');	    
	}

	public function down()
	{
		echo "m161219_152500_new_dna_fields does not support migration down.\n";
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