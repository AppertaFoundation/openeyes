<?php

class m140718_135220_patient_extra_fields extends CDbMigration
{
	public function up()
	{
		$this->addColumn('patient','yob','int(2) unsigned NULL');
	}

	public function down()
	{
		$this->dropColumn('patient','yob');
	}
}