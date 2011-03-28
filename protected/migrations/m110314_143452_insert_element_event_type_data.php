<?php

class m110314_143452_insert_element_event_type_data extends CDbMigration
{
    public function up()
    {
		$command = Yii::app()->db->createCommand("INSERT INTO `event_type` (`id`, `name`, `first_in_episode_possible`) VALUES (24, 'example', 0)");
		$command->execute();
    }

    public function down()
    {
		$command = Yii::app()->db->createCommand("DELETE FROM `event_type` WHERE `id` = 24");
		$command->execute();
    }
}