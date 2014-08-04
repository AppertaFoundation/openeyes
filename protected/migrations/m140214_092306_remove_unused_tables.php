<?php

class m140214_092306_remove_unused_tables extends CDbMigration
{
	public function up()
	{
		foreach (array(
				'element_type_anaesthetic_agent',
				'element_type_anaesthetic_complication',
				'element_type_anaesthetic_delivery',
				'element_type_anaesthetic_type',
				'element_type_anaesthetist',
				'element_type_eye',
				'element_type_priority',
			) as $table) {

			$this->dropTable($table);
		}
	}

	public function down()
	{
		$this->createTable('element_type_anaesthetic_agent', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'anaesthetic_agent_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'tinyint(3) unsigned not null',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `element_type_anaesthetic_agent_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_type_anaesthetic_agent_created_user_id_fk` (`created_user_id`)',
				'KEY `element_type_anaesthetic_agent_element_type_id_fk` (`element_type_id`)',
				'KEY `element_type_anaesthetic_agent_anaesthetic_agent_id_fk` (`anaesthetic_agent_id`)',
				'CONSTRAINT `element_type_anaesthetic_agent_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_anaesthetic_agent_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_anaesthetic_agent_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `element_type_anaesthetic_agent_anaesthetic_agent_id_fk` FOREIGN KEY (`anaesthetic_agent_id`) REFERENCES `anaesthetic_agent` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('element_type_anaesthetic_complication', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'anaesthetic_complication_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'tinyint(3) unsigned not null',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `element_type_ac_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_type_ac_created_user_id_fk` (`created_user_id`)',
				'KEY `element_type_ac_element_type_id_fk` (`element_type_id`)',
				'KEY `element_type_ac_anaesthetic_complication_id_fk` (`anaesthetic_complication_id`)',
				'CONSTRAINT `element_type_ac_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_ac_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_ac_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `element_type_ac_anaesthetic_complication_id_fk` FOREIGN KEY (`anaesthetic_complication_id`) REFERENCES `anaesthetic_complication` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('element_type_anaesthetic_delivery', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'anaesthetic_delivery_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'tinyint(3) unsigned not null',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `element_type_anaesthetic_delivery_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_type_anaesthetic_delivery_created_user_id_fk` (`created_user_id`)',
				'KEY `element_type_anaesthetic_delivery_element_type_id_fk` (`element_type_id`)',
				'KEY `element_type_anaesthetic_delivery_anaesthetic_delivery_id_fk` (`anaesthetic_delivery_id`)',
				'CONSTRAINT `element_type_anaesthetic_delivery_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_anaesthetic_delivery_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_anaesthetic_delivery_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `element_type_anaesthetic_delivery_anaesthetic_delivery_id_fk` FOREIGN KEY (`anaesthetic_delivery_id`) REFERENCES `anaesthetic_delivery` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('element_type_anaesthetic_type', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'anaesthetic_type_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'tinyint(3) unsigned not null',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `element_type_anaesthetic_type_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_type_anaesthetic_type_created_user_id_fk` (`created_user_id`)',
				'KEY `element_type_anaesthetic_type_fk1` (`element_type_id`)',
				'KEY `element_type_anaesthetic_type_fk2` (`anaesthetic_type_id`)',
				'CONSTRAINT `element_type_anaesthetic_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_anaesthetic_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_anaesthetic_type_fk1` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `element_type_anaesthetic_type_fk2` FOREIGN KEY (`anaesthetic_type_id`) REFERENCES `anaesthetic_type` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('element_type_anaesthetist', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'anaesthetist_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'tinyint(3) unsigned not null',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `element_type_anaesthetist_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_type_anaesthetist_created_user_id_fk` (`created_user_id`)',
				'KEY `element_type_anaesthetist_element_type_id_fk` (`element_type_id`)',
				'KEY `element_type_anaesthetist_anaesthetist_id_fk` (`anaesthetist_id`)',
				'CONSTRAINT `element_type_anaesthetist_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_anaesthetist_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_anaesthetist_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `element_type_anaesthetist_anaesthetist_id_fk` FOREIGN KEY (`anaesthetist_id`) REFERENCES `anaesthetist` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('element_type_eye', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'eye_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'tinyint(3) unsigned not null',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `element_type_eye_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_type_eye_created_user_id_fk` (`created_user_id`)',
				'KEY `element_type_eye_fk1` (`element_type_id`)',
				'KEY `element_type_eye_fk2` (`eye_id`)',
				'CONSTRAINT `element_type_eye_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_eye_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_eye_fk1` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `element_type_eye_fk2` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('element_type_priority', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'priority_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'tinyint(3) unsigned not null',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `element_type_priority_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_type_priority_created_user_id_fk` (`created_user_id`)',
				'KEY `element_type_priority_fk1` (`element_type_id`)',
				'KEY `element_type_priority_fk2` (`priority_id`)',
				'CONSTRAINT `element_type_priority_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_priority_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_priority_fk1` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `element_type_priority_fk2` FOREIGN KEY (`priority_id`) REFERENCES `priority` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
	}
}
