<?php

class m110614_135222_create_referral_table extends CDbMigration
{
	public function up()
	{
                $this->createTable('referral', array(
                        'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                        'refno' => 'int(10) unsigned NOT NULL',
			'patient_id' => 'int(10) unsigned NOT NULL',
			'service_id' => 'int(10) unsigned NOT NULL',
                        'PRIMARY KEY (`id`)'
                        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
                );

                $this->createTable('referral_episode_assignment', array(
                        'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                        'referral_id' => 'int(10) unsigned NOT NULL',
			'episode_id' => 'int(10) unsigned NOT NULL',
                        'PRIMARY KEY (`id`)'
                        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
                );
	}

	public function down()
	{
		$this->dropTable('referral');
		$this->dropTable('referral_episode_assignment');
	}
}
