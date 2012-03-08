<?php

class m120302_132027_episode_status extends CDbMigration
{
	public function up()
	{
		$this->createTable('episode_status',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => "varchar(64) NOT NULL DEFAULT ''",
			'PRIMARY KEY (`id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->insert('episode_status',array('name' => 'New'));
		$this->insert('episode_status',array('name' => 'Under investigation'));
		$this->insert('episode_status',array('name' => 'Listed/booked'));
		$this->insert('episode_status',array('name' => 'Post-op'));
		$this->insert('episode_status',array('name' => 'Follow-up'));
		$this->insert('episode_status',array('name' => 'Discharged'));
	}

	public function down()
	{
		$this->dropTable('episode_status');
	}
}
