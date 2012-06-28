<?php

class m120628_091253_add_missing_procedures extends CDbMigration
{
	public function up()
	{
		$this->insert('proc',array('term'=>'Three snip procedure','short_format'=>'Three snip','default_duration'=>30,'snomed_code'=>'172311004','snomed_term'=>'Three snip procedure to eyelid'));
		$this->insert('proc',array('term'=>'Lateral canthal sling','short_format'=>'Lat canth sling','default_duration'=>40,'snomed_code'=>'231590007','snomed_term'=>'Lateral canthal sling'));
		$this->insert('proc',array('term'=>'Tarsoconjunctival diamond excision','short_format'=>'Tarsoconj diamond','default_duration'=>40,'snomed_code'=>'231603008','snomed_term'=>'Tarsoconjunctival diamond excision'));
		$this->insert('proc',array('term'=>'Drainage of suprachoroidal fluid','short_format'=>'Drain Supra','default_duration'=>60,'snomed_code'=>'231788008','snomed_term'=>'Drainage of suprachoroidal fluid'));
		$this->insert('proc',array('term'=>'Repair orbital floor','short_format'=>'Repair Orbit','default_duration'=>60,'snomed_code'=>'239385005','snomed_term'=>'Repair orbital floor'));
		$this->insert('proc',array('term'=>'Redo external DCR','short_format'=>'RedoDCR','default_duration'=>90,'snomed_code'=>'265281004','snomed_term'=>'Canaliculodacryocystorhinostomy'));
		$this->insert('proc',array('term'=>'Other procedure on eyelid','short_format'=>'Lid - other','default_duration'=>60,'snomed_code'=>'118912008','snomed_term'=>'Procedure on eyelid'));
		$this->insert('proc',array('term'=>'Other procedure on orbit','short_format'=>'Orbit - other','default_duration'=>60,'snomed_code'=>'172189002','snomed_term'=>'Orbit operations NOS'));
		$this->insert('proc',array('term'=>'Removal of aqueous shunt','short_format'=>'RemAqueousShunt','default_duration'=>90,'snomed_code'=>'440587008','snomed_term'=>'Revision of aqueous shunt to extraocular reservoir'));
		$this->insert('proc',array('term'=>'Dexamethasone 700microgram intravitreal implant','short_format'=>'Ozurdex','default_duration'=>30,'snomed_code'=>'419222003','snomed_term'=>'Implantation of intravitreal device'));
	}

	public function down()
	{
		$this->delete('proc',"snomed_code in ('".implode("','",array('172311004','231590007','231603008','231788008','239385005','265281004','118912008','172189002','440587008','419222003'))."')");
	}
}
