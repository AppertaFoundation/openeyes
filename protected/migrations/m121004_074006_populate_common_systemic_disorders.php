<?php

class m121004_074006_populate_common_systemic_disorders extends CDbMigration
{
	public function up()
	{
		foreach (array() as $snomed) {
			$this->insert('common_systemic_disorder',array('disorder_id'=>$snomed));
		}
	}

	public function down()
	{
		$this->delete('common_systemic_disorder');
	}
}
