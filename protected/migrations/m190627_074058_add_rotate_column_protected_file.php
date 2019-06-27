<?php

class m190627_074058_add_rotate_column_protected_file extends CDbMigration
{
	public function up()
	{
        $this->addColumn('protected_file', 'rotate', 'int(11) NULL');
        $this->addColumn('protected_file_version', 'rotate', 'int(11) NULL');
	}

	public function down()
	{
        $this->dropColumn('protected_file', 'rotate');
        $this->dropColumn('protected_file_version', 'rotate');
	}

}