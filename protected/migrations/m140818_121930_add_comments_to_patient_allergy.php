<?php

class m140818_121930_add_comments_to_patient_allergy extends OEMigration
{
	public function safeUp()
	{
		$this->addColumn('patient_allergy_assignment', 'comments', 'string');
		$this->addColumn('patient_allergy_assignment_version', 'comments', 'string');
	}

	public function safeDown()
	{
		$this->dropColumn('patient_allergy_assignment', 'comments');
		$this->dropColumn('patient_allergy_assignment_version', 'comments');
	}
}
