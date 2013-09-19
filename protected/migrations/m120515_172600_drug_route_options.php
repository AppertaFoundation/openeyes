<?php

class m120515_172600_drug_route_options extends CDbMigration
{
	public function up()
	{
		// Create drug set tables
		$this->createTable('drug_route_option',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(40) DEFAULT NULL',
				'drug_route_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'CONSTRAINT `drug_route_option_drug_route_id_fk` FOREIGN KEY (`drug_route_id`) REFERENCES `drug_route` (`id`)',
				'CONSTRAINT `drug_route_option_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `drug_route_option_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

	}

	public function down()
	{
		$this->dropForeignKey('drug_route_option_drug_route_id_fk', 'drug_route_option');
		$this->dropForeignKey('drug_route_option_last_modified_user_id_fk', 'drug_route_option');
		$this->dropForeignKey('drug_route_option_created_user_id_fk', 'drug_route_option');
		$this->dropTable('drug_route_option');
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
