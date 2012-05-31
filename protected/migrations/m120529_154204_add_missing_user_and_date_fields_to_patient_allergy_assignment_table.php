<?php

class m120529_154204_add_missing_user_and_date_fields_to_patient_allergy_assignment_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('patient_allergy_assignment','last_modified_user_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->addColumn('patient_allergy_assignment','last_modified_date','datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'');
		$this->addForeignKey('patient_allergy_assignment_last_modified_user_id_fk','patient_allergy_assignment','last_modified_user_id','user','id');
		$this->addColumn('patient_allergy_assignment','created_user_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->addForeignKey('patient_allergy_assignment_created_user_id_fk','patient_allergy_assignment','created_user_id','user','id');
		$this->addColumn('patient_allergy_assignment','created_date','datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'');
	}

	public function down()
	{
		$this->dropForeignKey('patient_allergy_assignment_created_user_id_fk','patient_allergy_assignment');
		$this->dropForeignKey('patient_allergy_assignment_last_modified_user_id_fk','patient_allergy_assignment');
		$this->dropColumn('patient_allergy_assignment','created_date');
		$this->dropColumn('patient_allergy_assignment','created_user_id');
		$this->dropColumn('patient_allergy_assignment','last_modified_date');
		$this->dropColumn('patient_allergy_assignment','last_modified_user_id');
	}
}
