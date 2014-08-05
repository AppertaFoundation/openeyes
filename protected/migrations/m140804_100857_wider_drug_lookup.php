<?php

class m140804_100857_wider_drug_lookup extends OEMigration
{
	public function up()
	{
		$this->createOETable('medication_drug', array(
						'id' => 'pk',
						'name' => 'string NOT NULL',
						'external_code' => 'string',
						'external_source' => 'string',
						'aliases' => 'text',
				), true);
		$this->createIndex('medication_drug_search_name_idx', 'medication_drug', 'name');
		$this->createIndex('medication_drug_search_aliases_idx', 'medication_drug', 'aliases(500)');
		$this->createIndex('medication_drug_external_id_idx', 'medication_drug', 'external_code, external_source', true);

		$this->alterColumn('medication', 'drug_id', 'int(10) unsigned');
		$this->alterColumn('medication_version', 'drug_id', 'int(10) unsigned');
		$this->addColumn('medication', 'medication_drug_id', 'int(11)');
		$this->addColumn('medication_version', 'medication_drug_id', 'int(11)');
		$this->addForeignKey('medication_mdid_fk',
				'medication', 'medication_drug_id', 'medication_drug', 'id');


	}

	public function down()
	{

		$this->dropForeignKey('medication_mdid_fk','medication');
		$this->dropColumn('medication', 'medication_drug_id');
		$this->dropColumn('medication_version', 'medication_drug_id');

		$this->dropOETable('medication_drug', true);
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