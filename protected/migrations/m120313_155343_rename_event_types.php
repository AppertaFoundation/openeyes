<?php

class m120313_155343_rename_event_types extends CDbMigration
{
	public function up()
	{
		$this->update('event_type',array('name'=>'Examination'),"class_name='OphCiExamination'");
		$this->update('event_type',array('name'=>'Refraction'),"class_name='OphCiRefraction'");
		$this->update('event_type',array('name'=>'Orthoptics'),"class_name='OphCiOrthoptics'");
		$this->update('event_type',array('name'=>'Operation note'),"class_name='OphTrOperationnote'");
		$this->update('event_type',array('name'=>'FFA'),"class_name='OphImFfa'");
		$this->update('event_type',array('name'=>'ICG'),"class_name='OphImIcg'");
		$this->update('event_type',array('name'=>'OCT'),"class_name='OphImOct'");
		$this->update('event_type',array('name'=>'HFA'),"class_name='OphInHfa'");
		$this->update('event_type',array('name'=>'Ultrasound'),"class_name='OphImUltrasound'");
		$this->update('event_type',array('name'=>'X-ray'),"class_name='OphImXray'");
		$this->update('event_type',array('name'=>'CT scan'),"class_name='OphImCtscan'");
		$this->update('event_type',array('name'=>'MRI scan'),"class_name='OphImMriscan'");
		$this->update('event_type',array('name'=>'Blood test'),"class_name='OphInBloodtest'");
		$this->update('event_type',array('name'=>'Prescription'),"class_name='OphDrPrescription'");
		$this->update('event_type',array('name'=>'Pre-assessment'),"class_name='OphCiPreassess'");
		$this->update('event_type',array('name'=>'Anaesthetics'),"class_name='OphCiAnaesth'");
		$this->update('event_type',array('name'=>'AMD Application'),"class_name='OphCoAmdapplication'");
		$this->update('event_type',array('name'=>'AMD Injection'),"class_name='OphCiAmdinjection'");
		$this->update('event_type',array('name'=>'Injection'),"class_name='OphDrInjection'");
		$this->update('event_type',array('name'=>'Laser'),"class_name='OphTrLaser'");
		$this->update('event_type',array('name'=>'Letter in'),"class_name='OphCoLetterin'");
		$this->update('event_type',array('name'=>'Letter out'),"class_name='OphCoLetterout'");
		$this->update('event_type',array('name'=>'CVI'),"class_name='OphCoCvi'");
		$this->update('event_type',array('name'=>'Operation'),"class_name='OphTrOperation'");
	}

	public function down()
	{
		echo "m120313_155343_rename_event_types does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}
