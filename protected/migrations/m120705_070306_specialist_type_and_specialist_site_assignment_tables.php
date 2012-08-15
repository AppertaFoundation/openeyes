<?php

class m120705_070306_specialist_type_and_specialist_site_assignment_tables extends CDbMigration
{
	public function up()
	{
		$this->createTable('site_specialist_assignment',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'site_id' => 'int(10) unsigned NOT NULL',
				'specialist_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `site_specialist_assignment_site_id_fk` (`site_id`)',
				'KEY `site_specialist_assignment_specialist_id_fk` (`specialist_id`)',
				'KEY `site_specialist_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `site_specialist_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `site_specialist_assignment_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
				'CONSTRAINT `site_specialist_assignment_specialist_id_fk` FOREIGN KEY (`specialist_id`) REFERENCES `specialist` (`id`)',
				'CONSTRAINT `site_specialist_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `site_specialist_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('specialist_type',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
			'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
			'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
			'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
			'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
			'PRIMARY KEY (`id`)',
			'CONSTRAINT `specialist_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `specialist_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		echo "-----------------------------------------------------------------------------------\n";
		echo "| Warning: this migration (necessarily) removes all rows in the specialist table. |\n";
		echo "-----------------------------------------------------------------------------------\n";

		$this->delete('specialist');
		$this->addColumn('specialist','surgeon','tinyint(1) NOT NULL DEFAULT 0');
		$this->addColumn('specialist','specialist_type_id','int(10) unsigned NOT NULL');
		$this->createIndex('specialist_specialist_type_id_fk','specialist','specialist_type_id');
		$this->addForeignKey('specialist_specialist_type_id_fk','specialist','specialist_type_id','specialist_type','id');
	}

	public function down()
	{
		$this->dropForeignKey('specialist_specialist_type_id_fk','specialist');
		$this->dropIndex('specialist_specialist_type_id_fk','specialist');
		$this->dropColumn('specialist','specialist_type_id');
		$this->dropColumn('specialist','surgeon');
		$this->dropTable('specialist_type');
		$this->dropTable('site_specialist_assignment');
	}
}
