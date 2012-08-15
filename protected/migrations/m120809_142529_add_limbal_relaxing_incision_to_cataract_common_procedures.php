<?php

class m120809_142529_add_limbal_relaxing_incision_to_cataract_common_procedures extends CDbMigration
{
	public function up()
	{
		$this->insert('proc_subspecialty_assignment',array('proc_id'=>340,'subspecialty_id'=>4));
	}

	public function down()
	{
		$this->delete('proc_subspecialty_assignment','proc_id=340 and subspecialty_id=4');
	}
}
