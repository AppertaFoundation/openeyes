<?php

class m111222_161347_patient_gp_id_field extends CDbMigration
{
	public function up()
	{
		$this->addColumn('patient','gp_id','int(10) unsigned NULL DEFAULT NULL');
		$this->addForeignKey('patient_gp_id_fk','patient','gp_id','gp','id');
	}

	public function down()
	{
		$this->dropForeignKey('patient_gp_id_fk','patient');
		$this->dropColumn('patient','gp_id');
	}
}
