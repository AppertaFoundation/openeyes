<?php

class m190426_105513_add_max_num_per_patient_to_contact_label extends CDbMigration
{
	public function up()
	{
	    $this->addColumn('contact_label', 'max_number_per_patient' , 'tinyint(2) NULL');
	    $this->addColumn('contact_label_version', 'max_number_per_patient' , 'tinyint(2) NULL');
	}

	public function down()
	{
		$this->dropColumn('contact_label', 'max_number_per_patient');
		$this->dropColumn('contact_label_version', 'max_number_per_patient');
	}
}