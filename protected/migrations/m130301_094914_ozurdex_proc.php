<?php

class m130301_094914_ozurdex_proc extends CDbMigration
{
	public function up()
	{
		if (!$proc = Procedure::model()->find('snomed_code=? and short_format=?',array('419222003','Ozurdex'))) {
			$this->insert('proc',array('term'=>'Dexamethasone 700microgram intravitreal implant','snomed_code'=>'419222003','short_format'=>'Ozurdex','default_duration'=>30,'snomed_term'=>'Implantation of intravitreal device'));
		}
	}

	public function down()
	{
	}
}
