<?php

class m120302_135905_last_modified_and_created_fields_for_episode_status_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('episode_status','last_modified_user_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->addColumn('episode_status','last_modified_date','datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'');
		$this->addForeignKey('episode_status_last_modified_user_id_fk','episode_status','last_modified_user_id','user','id');
		$this->addColumn('episode_status','created_user_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->addForeignKey('episode_status_created_user_id_fk','episode_status','created_user_id','user','id');
		$this->addColumn('episode_status','created_date','datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'');
	}

	public function down()
	{
		$this->dropForeignKey('episode_status_created_user_id_fk','episode_status');
		$this->dropForeignKey('episode_status_last_modified_user_id_fk','episode_status');
		$this->dropColumn('episode_status','created_date');
		$this->dropColumn('episode_status','created_user_id');
		$this->dropColumn('episode_status','last_modified_date');
		$this->dropColumn('episode_status','last_modified_user_id');
	}
}
