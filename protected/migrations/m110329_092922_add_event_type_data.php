<?php

class m110329_092922_add_event_type_data extends CDbMigration
{
    public function up()
    {
		$this->insert('event_type', array(
			'name' => 'example',
			'first_in_episode_possible' => 0
		));
    }

    public function down()
    {
		$this->delete('event_type', 'name = :name AND first_in_episode_possible = :fie',
			array(':name' => 'example', ':fie' => 0)
		);
    }
}