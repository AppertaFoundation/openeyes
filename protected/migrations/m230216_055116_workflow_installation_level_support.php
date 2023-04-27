<?php

class m230216_055116_workflow_installation_level_support extends OEMigration
{
	public function up()
	{
		$this->alterOEColumn('ophciexamination_workflow', 'institution_id', 'int(10) unsigned');
	}

	public function down()
	{
		echo "m230216_055116_workflow_installation_level_support does not support migration down.\n";
		return false;
	}
}
