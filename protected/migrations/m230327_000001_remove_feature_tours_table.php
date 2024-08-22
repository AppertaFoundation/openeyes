<?php

class m230327_000001_remove_feature_tours_table extends OEMigration
{
    public function safeUp()
    {
        if ($this->verifyTableExists('user_feature_tour_state')) {
            $this->dropTable('user_feature_tour_state');
        }
            $this->deleteSetting('disable_auto_feature_tours');
    }

    public function safeDown()
    {
        $this->createTable('user_feature_tour_state', array(
            'user_id' => 'int(10) unsigned NOT NULL',
            'tour_id' => 'varchar(180) NOT NULL', ## This has been reduced from 511 due to it failing when migrating up from clean db. Longest entry in db at time of typing this was 18
            'completed' => 'boolean default false',
            'sleep_until' => 'datetime'
        ));
        $this->addPrimaryKey(
            'user_feature_tour_state_pk',
            'user_feature_tour_state',
            'user_id, tour_id'
        );
        $this->addForeignKey(
            'user_feature_tour_state_user_fk',
            'user_feature_tour_state',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        $this->addSetting(
            'disable_auto_feature_tours',
            'Disable Automatic Feature Tours',
            'Can be removed - feature tours are no longer an OpenEyes feature',
            'Core',
            'Radio buttons',
            'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'on'
        );
    }
}
