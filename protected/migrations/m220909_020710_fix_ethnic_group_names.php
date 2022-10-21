<?php

class m220909_020710_fix_ethnic_group_names extends OEMigration
{
	public function up()
	{
		$this->execute('UPDATE ethnic_group SET name = REPLACE(name, "?", "–")');
	}

	public function down()
	{
		echo "m220909_020710_fix_ethnic_group_names does not support migration down.\n";
		return false;
	}
}
