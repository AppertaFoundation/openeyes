<?php

class m220903_062525_update_pgspsd_assignment_active_field extends OEMigration
{
	public function safeUp()
	{
        $this->update('ophdrpgdpsd_assignment', ['active' => 0], "active IS NULL");
	}

	public function safeDown()
	{
		echo "m220903_062525_update_pgspsd_assignment_active_field does not support migration down.\n";
		return false;
	}
}