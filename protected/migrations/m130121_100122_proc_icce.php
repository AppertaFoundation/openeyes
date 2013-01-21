<?php

class m130121_100122_proc_icce extends CDbMigration
{
	public function up()
	{
		$this->insert('proc',array('term'=>'Intracapsular cataract extraction','short_format'=>'ICCE','default_duration'=>'30','snomed_code'=>'260216002','snomed_term'=>'Intra-capsular cataract extraction'));
	}

	public function down()
	{
		$this->delete('proc',"term='Intracapsular cataract extraction' and snomed_code='260216002'");
	}
}
