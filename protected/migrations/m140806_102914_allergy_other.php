<?php

class m140806_102914_allergy_other extends OEMigration
{
	public function safeUp()
	{
		$this->addColumn('patient_allergy_assignment', 'other', 'string');
		$this->addColumn('patient_allergy_assignment_version', 'other', 'string');
		$this->initialiseData(__DIR__);
	}

	public function safeDown()
	{
		$this->dropColumn('patient_allergy_assignment', 'other');
		$this->dropColumn('patient_allergy_assignment_version', 'other');
	}
}
