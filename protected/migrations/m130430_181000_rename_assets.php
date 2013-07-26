<?php

class m130430_181000_rename_assets extends CDbMigration
{
	public function up()
	{
		$this->renameTable('asset', 'protected_file');
	}

	public function down()
	{
		$this->renameTable('protected_file', 'asset');
	}

}
