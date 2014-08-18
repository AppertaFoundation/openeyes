<?php

class m140818_135549_global_data_fixes extends OEMigration
{
	public function safeUp()
	{
		$this->update('socialhistory_occupation', array('name' => 'Disability Benefits'), 'name = "Sickness"');

	}

	public function safeDown()
	{
		echo "m140818_135549_global_data_fixes does not support migration down.\n";
		return false;
	}
}
