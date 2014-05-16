<?php

class m140516_145619_social_history extends CDbMigration
{
	public function up()
	{
		$this->createTable('socialhistory_occupation', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(128) COLLATE utf8_bin NOT NULL',
				'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `socialhistory_occupation_lmui_fk` (`last_modified_user_id`)',
				'KEY `socialhistory_occupation_cui_fk` (`created_user_id`)',
				'CONSTRAINT `socialhistory_occupation_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `socialhistory_occupation_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->insert('socialhistory_occupation',array('name'=>'Employed','display_order'=>1));
		$this->insert('socialhistory_occupation',array('name'=>'Self-employed','display_order'=>2));
		$this->insert('socialhistory_occupation',array('name'=>'Unemployed','display_order'=>3));
		$this->insert('socialhistory_occupation',array('name'=>'Sickness','display_order'=>4));
		$this->insert('socialhistory_occupation',array('name'=>'Student','display_order'=>5));
		$this->insert('socialhistory_occupation',array('name'=>'Retired','display_order'=>6));
		$this->insert('socialhistory_occupation',array('name'=>'Other (specify)','display_order'=>7));

		$this->createTable('socialhistory_driving_status', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(128) COLLATE utf8_bin NOT NULL',
				'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `socialhistory_driving_status_lmui_fk` (`last_modified_user_id`)',
				'KEY `socialhistory_driving_status_cui_fk` (`created_user_id`)',
				'CONSTRAINT `socialhistory_driving_status_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `socialhistory_driving_status_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->insert('socialhistory_driving_status',array('name'=>'No license','display_order'=>1));
		$this->insert('socialhistory_driving_status',array('name'=>'No longer drives','display_order'=>2));
		$this->insert('socialhistory_driving_status',array('name'=>'Motor vehicle','display_order'=>3));
		$this->insert('socialhistory_driving_status',array('name'=>'HGV, Taxi, Train','display_order'=>4));
		$this->insert('socialhistory_driving_status',array('name'=>'Taxi','display_order'=>5));
		$this->insert('socialhistory_driving_status',array('name'=>'Train','display_order'=>6));

		$this->createTable('socialhistory_smoking_status', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(128) COLLATE utf8_bin NOT NULL',
				'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `socialhistory_smoking_status_lmui_fk` (`last_modified_user_id`)',
				'KEY `socialhistory_smoking_status_cui_fk` (`created_user_id`)',
				'CONSTRAINT `socialhistory_smoking_status_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `socialhistory_smoking_status_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->insert('socialhistory_smoking_status',array('name'=>'Non-smoker','display_order'=>1));
		$this->insert('socialhistory_smoking_status',array('name'=>'Smoker','display_order'=>2));
		$this->insert('socialhistory_smoking_status',array('name'=>'Ex smoker','display_order'=>3));
		$this->insert('socialhistory_smoking_status',array('name'=>'Never smoked tobacco','display_order'=>4));
		$this->insert('socialhistory_smoking_status',array('name'=>'Tobacco smoking consumption unknown','display_order'=>5));

		$this->createTable('socialhistory_accommodation', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(128) COLLATE utf8_bin NOT NULL',
				'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `socialhistory_accommodation_lmui_fk` (`last_modified_user_id`)',
				'KEY `socialhistory_accommodation_cui_fk` (`created_user_id`)',
				'CONSTRAINT `socialhistory_accommodation_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `socialhistory_accommodation_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->insert('socialhistory_accommodation',array('name'=>'Lives with partner','display_order'=>1));
		$this->insert('socialhistory_accommodation',array('name'=>'Lives with family','display_order'=>2));
		$this->insert('socialhistory_accommodation',array('name'=>'Lives alone - coping','display_order'=>3));
		$this->insert('socialhistory_accommodation',array('name'=>'Lives alone - not coping','display_order'=>4));
		$this->insert('socialhistory_accommodation',array('name'=>'Lives with friends','display_order'=>5));
		$this->insert('socialhistory_accommodation',array('name'=>'Lives in sheltered housing','display_order'=>6));
		$this->insert('socialhistory_accommodation',array('name'=>'Nursing home','display_order'=>7));



		$this->createTable('socialhistory', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'occupation_id' => 'int(10) unsigned NOT NULL',

				'driving_status_id' => 'int(10) unsigned NOT NULL',

				'smoking_status_id' => 'int(10) unsigned NOT NULL',

				'accommodation_id' => 'int(10) unsigned NOT NULL',

				'comments' => 'text COLLATE utf8_bin DEFAULT \'\'',

				'type_of_job' => 'varchar(255) COLLATE utf8_bin DEFAULT \'\'',

				'carer' => 'tinyint(1) unsigned NOT NULL',

				'alcohol_intake' => 'varchar(255) COLLATE utf8_bin DEFAULT \'\'',

				'substance_misuse' => 'tinyint(1) unsigned NOT NULL',

				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `socialhistory_lmui_fk` (`last_modified_user_id`)',
				'KEY `socialhistory_cui_fk` (`created_user_id`)',
				'KEY `socialhistory_occupation_fk` (`occupation_id`)',
				'KEY `socialhistory_driving_status_fk` (`driving_status_id`)',
				'KEY `socialhistory_smoking_status_fk` (`smoking_status_id`)',
				'KEY `socialhistory_accommodation_fk` (`accommodation_id`)',
				'CONSTRAINT `socialhistory_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `socialhistory_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `socialhistory_occupation_fk` FOREIGN KEY (`occupation_id`) REFERENCES `socialhistory_occupation` (`id`)',
				'CONSTRAINT `socialhistory_driving_status_fk` FOREIGN KEY (`driving_status_id`) REFERENCES `socialhistory_driving_status` (`id`)',
				'CONSTRAINT `socialhistory_smoking_status_fk` FOREIGN KEY (`smoking_status_id`) REFERENCES `socialhistory_smoking_status` (`id`)',
				'CONSTRAINT `socialhistory_accommodation_fk` FOREIGN KEY (`accommodation_id`) REFERENCES `socialhistory_accommodation` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
	}

	public function down()
	{
		$this->dropTable('socialhistory');


		$this->dropTable('socialhistory_occupation');
		$this->dropTable('socialhistory_driving_status');
		$this->dropTable('socialhistory_smoking_status');
		$this->dropTable('socialhistory_accommodation');
	}

}