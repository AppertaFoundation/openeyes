<?php

class m121026_083852_erod_rule_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('erod_rule',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'subspecialty_id' => 'int(10) unsigned NOT NULL',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `erod_rule_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `erod_rule_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `erod_rule_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `erod_rule_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('erod_rule_item',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'erod_rule_id' => 'int(10) unsigned NOT NULL',
				'item_type' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'item_id' => 'int(10) unsigned NOT NULL',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `erod_rule_item_erod_rule_id_fk` (`erod_rule_id`)',
				'KEY `erod_rule_item_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `erod_rule_item_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `erod_rule_item_erod_rule_id_fk` FOREIGN KEY (`erod_rule_id`) REFERENCES `erod_rule` (`id`)',
				'CONSTRAINT `erod_rule_item_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `erod_rule_item_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
	}

	public function down()
	{
		$this->dropTable('erod_rule_item');
		$this->dropTable('erod_rule');
	}
}
