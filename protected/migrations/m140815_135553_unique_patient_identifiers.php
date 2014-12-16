<?php

class m140815_135553_unique_patient_identifiers extends OEMigration
{
	public function safeUp()
	{
		$this->createIndex('patient_hos_num_unique', 'patient', 'hos_num', true);

		$this->update('patient',array('nhs_num' => null),"nhs_num = ''");
		$this->createIndex('patient_nhs_num_unique', 'patient', 'nhs_num', true);
	}

	public function safeDown()
	{
		$this->dropIndex('patient_hos_num_unique', 'patient');
		$this->dropIndex('patient_nhs_num_unique', 'patient');
	}
}
