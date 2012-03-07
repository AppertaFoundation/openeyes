<?php

class m120307_143141_fix_class_names_on_event_type_table extends CDbMigration
{
	public function up()
	{
		$this->update('event_type',array('class_name'=>'OphTrOperationnote'),'id=4');
		$this->update('event_type',array('class_name'=>'OphImFfa'),'id=5');
		$this->update('event_type',array('class_name'=>'OphImIcg'),'id=6');
		$this->update('event_type',array('class_name'=>'OphImOct'),'id=7');
		$this->update('event_type',array('class_name'=>'OphInHfa'),'id=8');
		$this->update('event_type',array('class_name'=>'OphImUltrasound'),'id=9');
		$this->update('event_type',array('class_name'=>'OphImXray'),'id=10');
		$this->update('event_type',array('class_name'=>'OphImCtscan'),'id=11');
		$this->update('event_type',array('class_name'=>'OphImMriscan'),'id=12');
		$this->update('event_type',array('class_name'=>'OphInBloodtest'),'id=13');
		$this->update('event_type',array('class_name'=>'OphDrPrescription'),'id=14');
		$this->update('event_type',array('class_name'=>'OphCoAmdapplication'),'id=17');
		$this->update('event_type',array('class_name'=>'OphDrInjection'),'id=19');
		$this->update('event_type',array('class_name'=>'OphTrLaser'),'id=20');
		$this->update('event_type',array('class_name'=>'OphCoLetterin'),'id=21');
		$this->update('event_type',array('class_name'=>'OphCoLetterout'),'id=22');
		$this->update('event_type',array('class_name'=>'OphCoCvi'),'id=23');
		$this->update('event_type',array('class_name'=>'OphTrOperation'),'id=25');
	}

	public function down()
	{
		$this->update('event_type',array('class_name'=>'OphCiOperationnote'),'id=4');
		$this->update('event_type',array('class_name'=>'OphCiFfa'),'id=5');
		$this->update('event_type',array('class_name'=>'OphCiIcg'),'id=6');
		$this->update('event_type',array('class_name'=>'OphCiOct'),'id=7');
		$this->update('event_type',array('class_name'=>'OphCiHfa'),'id=8');
		$this->update('event_type',array('class_name'=>'OphCiUltrasound'),'id=9');
		$this->update('event_type',array('class_name'=>'OphCiXray'),'id=10');
		$this->update('event_type',array('class_name'=>'OphCiCtscan'),'id=11');
		$this->update('event_type',array('class_name'=>'OphCiMriscan'),'id=12');
		$this->update('event_type',array('class_name'=>'OphCiBloodtest'),'id=13');
		$this->update('event_type',array('class_name'=>'OphCiPrescription'),'id=14');
		$this->update('event_type',array('class_name'=>'OphCiAmdapplication'),'id=17');
		$this->update('event_type',array('class_name'=>'OphCiInjection'),'id=19');
		$this->update('event_type',array('class_name'=>'OphCiLaser'),'id=20');
		$this->update('event_type',array('class_name'=>'OphCiLetterin'),'id=21');
		$this->update('event_type',array('class_name'=>'OphCiLetterout'),'id=22');
		$this->update('event_type',array('class_name'=>'OphCiCvi'),'id=23');
		$this->update('event_type',array('class_name'=>'OphCiOperation'),'id=25');
	}
}
