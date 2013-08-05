<?php

class m130530_073132_patient_contacts_without_locations extends CDbMigration
{
	public function up()
	{
		$this->addColumn('patient_contact_assignment','contact_id','int(10) unsigned NULL');
		$this->createIndex('patient_contact_assignment_contact_id_fk','patient_contact_assignment','contact_id');
		$this->addForeignKey('patient_contact_assignment_contact_id_fk','patient_contact_assignment','contact_id','contact','id');

		$this->alterColumn('patient_contact_assignment','location_id','int(10) unsigned NULL');
	}

	public function down()
	{
		$this->dropForeignKey('patient_contact_assignment_contact_id_fk','patient_contact_assignment');
		$this->dropIndex('patient_contact_assignment_contact_id_fk','patient_contact_assignment');
		$this->dropColumn('patient_contact_assignment','contact_id');

		$this->alterColumn('patient_contact_assignment','location_id','int(10) unsigned NOT NULL');
	}
}
