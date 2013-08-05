<?php

class m120515_115300_drug_sets extends CDbMigration
{
	public function up()
	{
		// Create drug set tables
		$this->createTable('drug_set',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(40) DEFAULT NULL',
				'subspecialty_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'CONSTRAINT `drug_set_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)',
				'CONSTRAINT `drug_set_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `drug_set_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		$this->createTable('drug_set_item',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'drug_id' => 'int(10) unsigned NOT NULL',
				'drug_set_id' => 'int(10) unsigned NOT NULL',
				'default_frequency_id' => 'int(10) unsigned NOT NULL',
				'default_duration_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'CONSTRAINT `drug_set_item_drug_id_fk` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`id`)',
				'CONSTRAINT `drug_set_item_drug_set_id_fk` FOREIGN KEY (`drug_set_id`) REFERENCES `drug_set` (`id`)',
				'CONSTRAINT `drug_set_item_default_frequency_id_fk` FOREIGN KEY (`default_frequency_id`) REFERENCES `drug_frequency` (`id`)',
				'CONSTRAINT `drug_set_item_default_duration_id_fk` FOREIGN KEY (`default_duration_id`) REFERENCES `drug_duration` (`id`)',
				'CONSTRAINT `drug_set_item_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `drug_set_item_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

	}

	public function down()
	{
		$this->dropForeignKey('drug_set_item_drug_id_fk', 'drug_set_item');
		$this->dropForeignKey('drug_set_item_drug_set_id_fk', 'drug_set_item');
		$this->dropForeignKey('drug_set_item_default_frequency_id_fk', 'drug_set_item');
		$this->dropForeignKey('drug_set_item_default_duration_id_fk', 'drug_set_item');
		$this->dropForeignKey('drug_set_item_last_modified_user_id_fk', 'drug_set_item');
		$this->dropForeignKey('drug_set_item_created_user_id_fk', 'drug_set_item');
		$this->dropTable('drug_set_item');
		$this->dropForeignKey('drug_set_subspecialty_id_fk', 'drug_set');
		$this->dropForeignKey('drug_set_last_modified_user_id_fk', 'drug_set');
		$this->dropForeignKey('drug_set_created_user_id_fk', 'drug_set');
		$this->dropTable('drug_set');
	}

	public function safeUp()
	{
		$this->up();
	}

	public function safeDown()
	{
		$this->down();
	}

}
