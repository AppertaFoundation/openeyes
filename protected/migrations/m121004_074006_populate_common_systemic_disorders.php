<?php

class m121004_074006_populate_common_systemic_disorders extends CDbMigration
{
	public function up()
	{
		foreach (array(46635009,44054006,59621000,414545008,22298006,230690007,195967001,90688005,13645005,69896004,19346006,78675000,40930008,34486009) as $snomed) {
			$this->insert('common_systemic_disorder',array('disorder_id'=>$snomed));
		}
	}

	public function down()
	{
		$this->delete('common_systemic_disorder');
	}
}
