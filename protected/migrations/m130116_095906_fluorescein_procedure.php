<?php

class m130116_095906_fluorescein_procedure extends CDbMigration
{
	public function up()
	{
		$this->insert('proc',array('term'=>'Fluorescein angiography','short_format'=>'FFA','default_duration'=>'15','snomed_code'=>'172581008','snomed_term'=>'Fluorescein angiography of eye'));
	}

	public function down()
	{
		$this->delete('proc',"term='Fluorescein angiography' and snomed_code='172581008'");
	}
}
