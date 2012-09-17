<?php

class m120917_122307_soft_deleted_flat_for_episode_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('episode','deleted','int(10) unsigned NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('episode','deleted');
	}
}
