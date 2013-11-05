<?php

class m130301_094914_ozurdex_proc extends CDbMigration
{
	public function up()
	{
		$proc = $this->getDbConnection()->createCommand("select * from proc where snomed_code = :snomed_code and short_format = :short_format")
			->bindValues(array(':snomed_code' => '419222003', ':short_format' => 'Ozurdex'))->queryRow();
		if (!$proc ) {
			$this->insert('proc',array('term'=>'Dexamethasone 700microgram intravitreal implant','snomed_code'=>'419222003','short_format'=>'Ozurdex','default_duration'=>30,'snomed_term'=>'Implantation of intravitreal device'));
		}
	}

	public function down()
	{
	}
}
