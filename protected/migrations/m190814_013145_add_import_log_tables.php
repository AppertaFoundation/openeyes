<?php

class m190814_013145_add_import_log_tables extends OEMigration
{
	public function up()
	{
		$this->createOETable(
			'import_log',
			array(
				'id' => 'pk',
				'startdatetime' => 'datetime NOT NULL',
				'enddatetime' => 'datetime',
				'status' => 'string NOT NULL',
				'import_user_id' => 'int(10) NOT NULL'
				));

		$this->createOETable(
			'import',
			array(
				'id' => 'pk',
				'parent_log_id' => 'int NOT NULL',
				'message' => 'string NOT NULL',
				'import_status_id' => 'int NOT NULL'
			));
		$builder = Yii::app()->db->schema->commandBuilder;
		$builder->createMultipleInsertCommand(
			'import_status',
			array(
				array('id' => 6, 'status_value' => 'Duplicate Patient'),
				array('id' => 7, 'status_value' => 'Duplicate Contact'),
				array('id' => 8, 'status_value' => 'Import Patient Success'),
				array('id' => 9, 'status_value' => 'Invalid Patient Data'),
				array('id' => 10, 'status_value' => 'Invalid Contact Data'),
				array('id' => 11, 'status_value' => 'Invalid Diagnosis')
			))->execute();
	}

	public function down()
	{
		$this->dropTable('import_log');
		$this->dropTable('import');

		$this->delete('import_status', 'id=6 AND status_value="Duplicate Patient"');
		$this->delete('import_status', 'id=7 AND status_value="Duplicate Contact"');
		$this->delete('import_status', 'id=8 AND status_value="Import Patient Success"');
		$this->delete('import_status', 'id=9 AND status_value="Invalid Patient Data"');
		$this->delete('import_status', 'id=10 AND status_value="Invalid Contact Data"');
		$this->delete('import_status', 'id=11 AND status_value="Invalid Diagnosis"');
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