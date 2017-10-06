<?php

class m171005_170953_track_tourstates extends CDbMigration
{
	public function up()
	{
	    $this->createTable('user_feature_tour_state', array(
            'user_id' => 'int(10) unsigned NOT NULL',
            'tour_id' => 'varchar(511) NOT NULL',
            'completed' => 'boolean default false',
            'sleep_until' => 'datetime'
        ));
	}

	public function down()
	{
		$this->dropTable('user_feature_tour_state');
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