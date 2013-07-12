<?php

class m120630_195352_legacy_tweaks extends CDbMigration
{
	public function up()
	{
		$this->addColumn('episode','legacy',"tinyint(1) unsigned DEFAULT '0'");
		$this->alterColumn('event','episode_id','int(10) unsigned DEFAULT NULL');
		$this->createIndex('idx_event_episode_id', 'event', 'episode_id');
		$this->createIndex('idx_event_event_type_id', 'event', 'event_type_id');
		return true;
	}

	public function down()
	{
		$this->dropColumn('episode','legacy');
		$this->alterColumn('event','episode_id','int(10) unsigned NOT NULL');
		$this->dropIndex('idx_event_episode_id', 'event');
		$this->dropIndex('idx_event_event_type_id', 'event');
		return true;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}
