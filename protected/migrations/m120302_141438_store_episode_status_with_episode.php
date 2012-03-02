<?php

class m120302_141438_store_episode_status_with_episode extends CDbMigration
{
	public function up()
	{
		$this->addColumn('episode','episode_status_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->addForeignKey('episode_episode_status_id_fk','episode','episode_status_id','episode_status','id');
	}

	public function down()
	{
		$this->dropForeignKey('episode_episode_status_id_fk','episode');
		$this->dropColumn('episode','episode_status_id');
	}
}
