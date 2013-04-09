<?php

class m130409_103405_remove_unnecessary_contact_id_field extends CDbMigration
{
	public function up()
	{
		$this->dropForeignKey('patient_consultant_assignment_contact_id_fk','patient_contact_assignment');
		$this->dropIndex('patient_consultant_assignment_contact_id_fk','patient_contact_assignment');
		$this->dropColumn('patient_contact_assignment','contact_id');
	}

	public function down()
	{
		$this->addColumn('patient_contact_assignment','contact_id','int(10) unsigned NOT NULL');
		$this->createIndex('patient_consultant_assignment_contact_id_fk','patient_contact_assignment','contact_id');
		$this->addForeignKey('patient_consultant_assignment_contact_id_fk','patient_contact_assignment','contact_id','contact','id');
	}
}
