<?php

class m140523_080847_social_history_table_versioning extends OEMigration
{
	public function up()
	{
		$this->versionExistingTable('socialhistory');
		$this->versionExistingTable('socialhistory_carer');
		$this->versionExistingTable('socialhistory_driving_status');
		$this->versionExistingTable('socialhistory_occupation');
		$this->versionExistingTable('socialhistory_smoking_status');
		$this->versionExistingTable('socialhistory_substance_misuse');
	}

	public function down()
	{
		$this->dropTable('socialhistory_version');
		$this->dropTable('socialhistory_carer_version');
		$this->dropTable('socialhistory_driving_status_version');
		$this->dropTable('socialhistory_occupation_version');
		$this->dropTable('socialhistory_smoking_status_version');
		$this->dropTable('socialhistory_substance_misuse_version');
	}
}