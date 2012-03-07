<?php

class m120306_170530_class_names_for_event_type_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('event_type','class_name','varchar(200) COLLATE utf8_bin NOT NULL');

		$this->update('event_type',array('class_name'=>'OphCiExamination'),'id = 1');
		$this->update('event_type',array('class_name'=>'OphCiRefraction'),'id = 2');
		$this->update('event_type',array('class_name'=>'OphCiOrthoptics'),'id = 3');
		$this->update('event_type',array('class_name'=>'OphCiOperationnote'),'id = 4');
		$this->update('event_type',array('class_name'=>'OphCiFfa'),'id = 5');
		$this->update('event_type',array('class_name'=>'OphCiIcg'),'id = 6');
		$this->update('event_type',array('class_name'=>'OphCiOct'),'id = 7');
		$this->update('event_type',array('class_name'=>'OphCiHfa'),'id = 8');
		$this->update('event_type',array('class_name'=>'OphCiUltrasound'),'id = 9');
		$this->update('event_type',array('class_name'=>'OphCiXray'),'id = 10');
		$this->update('event_type',array('class_name'=>'OphCiCtscan'),'id = 11');
		$this->update('event_type',array('class_name'=>'OphCiMriscan'),'id = 12');
		$this->update('event_type',array('class_name'=>'OphCiBloodtest'),'id = 13');
		$this->update('event_type',array('class_name'=>'OphCiPrescription'),'id = 14');
		$this->update('event_type',array('class_name'=>'OphCiPreassess'),'id = 15');
		$this->update('event_type',array('class_name'=>'OphCiAnaesth'),'id = 16');
		$this->update('event_type',array('class_name'=>'OphCiAmdapplication'),'id = 17');
		$this->update('event_type',array('class_name'=>'OphCiAmdinjection'),'id = 18');
		$this->update('event_type',array('class_name'=>'OphCiInjection'),'id = 19');
		$this->update('event_type',array('class_name'=>'OphCiLaser'),'id = 20');
		$this->update('event_type',array('class_name'=>'OphCiLetterin'),'id = 21');
		$this->update('event_type',array('class_name'=>'OphCiLetterout'),'id = 22');
		$this->update('event_type',array('class_name'=>'OphCiCvi'),'id = 23');
		$this->update('event_type',array('class_name'=>'OphCiOperation'),'id = 25');
	}

	public function down()
	{
		$this->dropColumn('event_type','class_name');
	}
}
