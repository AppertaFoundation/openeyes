<?php

class m120511_101458_patient_consultant_assignment_should_be_patient_contact_assignment extends CDbMigration
{
	public function up()
	{
		$this->renameTable('patient_consultant_assignment','patient_contact_assignment');
		$this->dropForeignKey('patient_consultant_assignment_consultant_id_fk','patient_contact_assignment');
		$this->dropIndex('patient_consultant_assignment_consultant_id_fk','patient_contact_assignment');
		$this->renameColumn('patient_contact_assignment','consultant_id','contact_id');
		$this->createIndex('patient_consultant_assignment_contact_id_fk','patient_contact_assignment','contact_id');
		$this->addForeignKey('patient_consultant_assignment_contact_id_fk','patient_contact_assignment','contact_id','contact','id');
	}

	public function down()
	{
		$this->dropForeignKey('patient_consultant_assignment_contact_id_fk','patient_contact_assignment');
		$this->dropIndex('patient_consultant_assignment_contact_id_fk','patient_contact_assignment');
		$this->renameColumn('patient_contact_assignment','contact_id','consultant_id');
		$this->createIndex('patient_consultant_assignment_consultant_id_fk','patient_contact_assignment','consultant_id');
		$this->addForeignKey('patient_consultant_assignment_consultant_id_fk','patient_contact_assignment','consultant_id','consultant','id');
		$this->renameTable('patient_contact_assignment','patient_consultant_assignment');
	}
}
