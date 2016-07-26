<?php

class m160726_123714_new_cvi_record extends OEMigration
{
	public function up()
	{
		$this->insert('measurement_type',array('class_name' => 'CviRecord', 'attachable' => false));

		$this->createOETable('cvi_record', array(
			'id' => 'pk',
			'patient_measurement_id' => 'int(11) NOT NULL',
			'status_date' => 'date',
			'status_text' => 'varchar(63)'
		), true);

		$this->addForeignKey('cvi_record_pm_id_fk','cvi_record','patient_measurement_id','patient_measurement','id');
	}

	public function down()
	{
		$this->dropForeignKey('cvi_record_pm_id_fk', 'cvi_record');
		$this->dropOETable('cvi_record', true);

		$this->delete('measurement_type','class_name = ?', array('CviRecord'));
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