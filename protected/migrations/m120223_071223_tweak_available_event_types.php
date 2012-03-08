<?php

class m120223_071223_tweak_available_event_types extends CDbMigration
{
	public function up()
	{
		// change id=4 to be 'operationnote'
		// it is currently 'diagnosis' which is incorrect as this shouldn't be an event type: it's represented at patient and episode level
		// operationnote is the next event type we will be working on and it isn't currently represented
		// operationnote seems to be the only event type from the upcoming 'cataract' clump that doesn't already exist

		$this->update('event_type', array('name' => 'operationnote'), 'id = :id', array(':id' => 4));

		// remove the 'first_in_episode_possible' column from 'event_type' as we're in the process of removing the concept of
		// 'first in episode' in any case, to be replaced with a more flexible concept of statuses
		$this->dropColumn('event_type', 'first_in_episode_possible');
	}

	public function down()
	{
		echo "m120223_071223_tweak_available_event_types does not support migration down.\n";
		return false;
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
