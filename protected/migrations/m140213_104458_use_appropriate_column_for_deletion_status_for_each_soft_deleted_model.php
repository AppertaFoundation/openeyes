<?php

class m140213_104458_use_appropriate_column_for_deletion_status_for_each_soft_deleted_model extends CDbMigration
{
	public function up()
	{
		$this->renameColumn('anaesthetic_agent','deleted','discontinued');
		$this->renameColumn('anaesthetic_agent_version','deleted','discontinued');

		$this->renameColumn('anaesthetic_type','deleted','discontinued');
		$this->renameColumn('anaesthetic_type_version','deleted','discontinued');

		$this->dropColumn('drug','deleted');
		$this->dropColumn('drug_version','deleted');

		$this->renameColumn('medication','deleted','discontinued');
		$this->renameColumn('medication_version','deleted','discontinued');

		$this->dropColumn('user','deleted');
		$this->dropColumn('user_version','deleted');
	}

	public function down()
	{
		$this->renameColumn('anaesthetic_agent_version','discontinued','deleted');
		$this->renameColumn('anaesthetic_agent','discontinued','deleted');

		$this->renameColumn('anaesthetic_type','discontinued','deleted');
		$this->renameColumn('anaesthetic_type_version','discontinued','deleted');

		$this->addColumn('drug','deleted','tinyint(1) unsigned not null');
		$this->addColumn('drug_version','deleted','tinyint(1) unsigned not null');

		$this->renameColumn('medication','discontinued','deleted');
		$this->renameColumn('medication_version','discontinued','deleted');

		$this->addColumn('user','deleted','tinyint(1) unsigned not null');
		$this->addColumn('user_version','deleted','tinyint(1) unsigned not null');
	}
}
