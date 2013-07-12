<?php

class m120510_135100_allergy_table_and_drug_changes extends CDbMigration
{
	public function up()
	{
		// Create allergy table and drug associations
		$this->createTable('allergy',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(40) DEFAULT NULL',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
			'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
			'PRIMARY KEY (`id`)',
			'CONSTRAINT `allergy_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `allergy_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		$this->createTable('drug_allergy_assignment',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'drug_id' => 'int(10) unsigned NOT NULL',
			'allergy_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'CONSTRAINT `drug_allergy_assignment_drug_id_fk` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`id`)',
			'CONSTRAINT `drug_allergy_assignment_allergy_id_fk` FOREIGN KEY (`allergy_id`) REFERENCES `allergy` (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		// Add metadata to drug table
		$this->createTable('drug_type',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(40) DEFAULT NULL',
			'PRIMARY KEY (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		$this->insert('drug_type', array('id' => 1, 'name' => 'Undefined'));
		$this->createTable('drug_form',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(40) DEFAULT NULL',
			'PRIMARY KEY (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		$this->insert('drug_form', array('id' => 1, 'name' => 'Undefined'));
		$this->createTable('drug_route',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(40) DEFAULT NULL',
			'PRIMARY KEY (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		$this->insert('drug_route', array('id' => 1, 'name' => 'Undefined'));
		$this->createTable('drug_frequency',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(40) DEFAULT NULL',
			'PRIMARY KEY (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		$this->insert('drug_frequency', array('id' => 1, 'name' => 'Undefined'));
		$this->createTable('drug_duration',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(40) DEFAULT NULL',
			'PRIMARY KEY (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		$this->insert('drug_duration', array('id' => 1, 'name' => 'Undefined'));
		$this->addColumn('drug', 'description', 'varchar(255)');
		$this->addColumn('drug', 'code', 'varchar(40)');
		$this->addColumn('drug', 'term', 'varchar(255)');
		$this->addColumn('drug', 'type_id', 'int(10) unsigned NOT NULL DEFAULT 1');
		$this->addForeignKey('drug_type_id_fk', 'drug', 'type_id', 'drug_type', 'id');
		$this->addColumn('drug', 'form_id', 'int(10) unsigned NOT NULL DEFAULT 1');
		$this->addForeignKey('drug_form_id_fk', 'drug', 'form_id', 'drug_form', 'id');
		$this->addColumn('drug', 'dose_unit', 'varchar(40)');
		$this->addColumn('drug', 'default_dose', 'varchar(40)');
		$this->addColumn('drug', 'default_route_id', 'int(10) unsigned NOT NULL DEFAULT 1');
		$this->addForeignKey('drug_default_route_id_fk', 'drug', 'default_route_id', 'drug_route', 'id');
		$this->addColumn('drug', 'default_frequency_id', 'int(10) unsigned NOT NULL DEFAULT 1');
		$this->addForeignKey('drug_default_frequency_id_fk', 'drug', 'default_frequency_id', 'drug_frequency', 'id');
		$this->addColumn('drug', 'default_duration_id', 'int(10) unsigned NOT NULL DEFAULT 1');
		$this->addForeignKey('drug_default_duration_id_fk', 'drug', 'default_duration_id', 'drug_duration', 'id');
		$this->addColumn('drug', 'preservative_free', 'tinyint(1) unsigned NOT NULL DEFAULT \'0\'');

	}

	public function down()
	{
		$this->dropForeignKey('drug_type_id_fk', 'drug');
		$this->dropForeignKey('drug_form_id_fk', 'drug');
		$this->dropForeignKey('drug_default_route_id_fk', 'drug');
		$this->dropForeignKey('drug_default_frequency_id_fk', 'drug');
		$this->dropForeignKey('drug_default_duration_id_fk', 'drug');
		$this->dropColumn('drug', 'description');
		$this->dropColumn('drug', 'code');
		$this->dropColumn('drug', 'term');
		$this->dropColumn('drug', 'type_id');
		$this->dropColumn('drug', 'form_id');
		$this->dropColumn('drug', 'dose_unit');
		$this->dropColumn('drug', 'default_dose');
		$this->dropColumn('drug', 'default_route_id');
		$this->dropColumn('drug', 'default_frequency_id');
		$this->dropColumn('drug', 'default_duration_id');
		$this->dropColumn('drug', 'preservative_free');
		$this->dropTable('drug_type');
		$this->dropTable('drug_form');
		$this->dropTable('drug_route');
		$this->dropTable('drug_frequency');
		$this->dropTable('drug_duration');
		$this->dropTable('drug_allergy_assignment');
		$this->dropTable('allergy');
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
