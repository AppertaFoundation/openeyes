<?php

class m140728_101512_version_tables extends OEMigration
{
	public function up()
	{
		$this->versionExistingTable('et_ophindnaextraction_dnaextraction');
		$this->versionExistingTable('et_ophindnaextraction_dnatests');
	}

	public function down()
	{
		$this->dropTable('et_ophindnaextraction_dnaextraction');
		$this->dropTable('et_ophindnaextraction_dnatests');
	}
}