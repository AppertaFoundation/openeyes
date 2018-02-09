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
	    $this->addPrimaryKey(
	        'user_feature_tour_state_pk',
            'user_feature_tour_state',
            'user_id, tour_id');
	    $this->addForeignKey(
	        'user_feature_tour_state_user_fk',
            'user_feature_tour_state',
            'user_id',
            'user',
            'id',
            'CASCADE');
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