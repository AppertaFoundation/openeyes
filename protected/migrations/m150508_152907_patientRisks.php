<?php

class m150508_152907_patientRisks extends CDbMigration
{
	public function up()
	{
		$this->createOETable(
			'risk',
			array(
				'id' => 'pk',
				'name' => 'varchar(255) not null'
			),
			true
		);
		$this->createOETable(
			'patient_risk_assignment',
			array(
				'id' => 'pk',
				'patient_id' => 'int(11) not null',
				'risk_id' => 'int(11) not null',
				'comment' => 'text'
			),
			true
		);
	}

	public function down()
	{
		$this->dropTable('patient_risk_assignment');
		$this->dropTable('risk');
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