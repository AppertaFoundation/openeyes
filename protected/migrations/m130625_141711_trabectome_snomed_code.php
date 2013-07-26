<?php

class m130625_141711_trabectome_snomed_code extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('proc','snomed_code','varchar(20) COLLATE utf8_bin NOT NULL');
		$this->update('proc',array('snomed_code'=>'11000163100'),'snomed_code = 31337');
	}

	public function down()
	{
		$this->update('proc',array('snomed_code'=>'31337'),"snomed_code = '11000163100'");
		$this->alterColumn('proc','snomed_code',"int(10) unsigned NOT NULL DEFAULT '0'");
	}
}
