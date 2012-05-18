<?php

class m120518_142641_site_id_and_insititution_id_fields_in_patient_contact_assignment_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('patient_contact_assignment','site_id','int(10) unsigned NULL');
		$this->createIndex('patient_contact_assignment_site_id_fk','patient_contact_assignment','site_id');
		$this->addForeignKey('patient_contact_assignment_site_id_fk','patient_contact_assignment','site_id','site','id');

		$this->addColumn('patient_contact_assignment','institution_id','int(10) unsigned NULL');
		$this->createIndex('patient_contact_assignment_institution_id_fk','patient_contact_assignment','institution_id');
		$this->addForeignKey('patient_contact_assignment_institution_id_fk','patient_contact_assignment','institution_id','institution','id');
	}

	public function down()
	{
		$this->dropForeignKey('patient_contact_assignment_institution_id_fk','patient_contact_assignment');
		$this->dropIndex('patient_contact_assignment_institution_id_fk','patient_contact_assignment');
		$this->dropColumn('patient_contact_assignment','institution_id');

		$this->dropForeignKey('patient_contact_assignment_site_id_fk','patient_contact_assignment');
		$this->dropIndex('patient_contact_assignment_site_id_fk','patient_contact_assignment');
		$this->dropColumn('patient_contact_assignment','site_id');
	}
}
