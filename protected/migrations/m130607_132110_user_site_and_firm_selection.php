<?php

class m130607_132110_user_site_and_firm_selection extends CDbMigration
{
	public function up()
	{
		$this->addColumn('user','has_selected_firms','tinyint(1) unsigned NOT NULL');

		$this->createTable('user_firm', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'user_id' => 'int(10) unsigned NOT NULL',
				'firm_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `user_firm_user_id_fk` (`user_id`)',
				'KEY `user_firm_firm_id_fk` (`firm_id`)',
				'KEY `user_firm_lmui_fk` (`last_modified_user_id`)',
				'KEY `user_firm_cui_fk` (`created_user_id`)',
				'CONSTRAINT `user_firm_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `user_firm_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
				'CONSTRAINT `user_firm_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `user_firm_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('user_site', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'user_id' => 'int(10) unsigned NOT NULL',
				'site_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `user_site_user_id_fk` (`user_id`)',
				'KEY `user_site_site_id_fk` (`site_id`)',
				'KEY `user_site_lmui_fk` (`last_modified_user_id`)',
				'KEY `user_site_cui_fk` (`created_user_id`)',
				'CONSTRAINT `user_site_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `user_site_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
				'CONSTRAINT `user_site_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `user_site_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
	}

	public function down()
	{
		$this->dropTable('user_site');
		$this->dropTable('user_firm');

		$this->dropColumn('user','has_selected_firms');
	}
}
