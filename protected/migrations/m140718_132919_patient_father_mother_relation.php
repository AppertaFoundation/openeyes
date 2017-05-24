<?php

class m140718_132919_patient_father_mother_relation extends CDbMigration
{
	public function up()
	{
		$this->addColumn('patient','father_id','int(10) unsigned NULL');
		$this->createIndex('patient_father_id_fk','patient','father_id');
		$this->addForeignKey('patient_father_id_fk','patient','father_id','patient','id');

		$this->addColumn('patient','mother_id','int(10) unsigned NULL');
		$this->createIndex('patient_mother_id_fk','patient','mother_id');
		$this->addForeignKey('patient_mother_id_fk','patient','mother_id','patient','id');
	}

	public function down()
	{
		$this->dropForeignKey('patient_mother_id_fk','patient');
		$this->dropIndex('patient_mother_id_fk','patient');
		$this->dropColumn('patient','mother_id');

		$this->dropForeignKey('patient_father_id_fk','patient');
		$this->dropIndex('patient_father_id_fk','patient');
		$this->dropColumn('patient','father_id');
	}
}