<?php

class m160523_150352_cataract_surgical_management extends OEMigration
{
	public function up()
	{
            $this->createOETable('ophciexamination_cataractsurgicalmanagement',
            array('id' => 'pk', 'name' => 'text', 'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1', ), true);
	}

	public function down()
	{
		echo "m160523_150352_cataract_management_operation does not support migration down.\n";
		return false;
	}

}