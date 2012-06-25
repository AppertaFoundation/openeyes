<?php

class m120618_113619_element_settings extends CDbMigration
{
	public function up()
	{
		$this->createTable('setting_field_type',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `setting_field_type_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `setting_field_type_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `setting_field_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `setting_field_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('setting_field_type',array('id'=>1,'name'=>'Checkbox'));
		$this->insert('setting_field_type',array('id'=>2,'name'=>'Dropdown list'));

		$this->createTable('setting_metadata',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'tinyint(3) unsigned DEFAULT 0',
				'field_type_id' => 'int(10) unsigned NOT NULL',
				'key' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'data' => 'varchar(4096) COLLATE utf8_bin NOT NULL',
				'default_value' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `setting_metadata_element_type_id_fk` (`element_type_id`)',
				'KEY `setting_metadata_field_type_id_fk` (`field_type_id`)',
				'KEY `setting_metadata_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `setting_metadata_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `setting_metadata_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `setting_metadata_field_type_id_fk` FOREIGN KEY (`field_type_id`) REFERENCES `setting_field_type` (`id`)',
				'CONSTRAINT `setting_metadata_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `setting_metadata_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('setting_installation',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'key' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'value' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `setting_installation_element_type_id_fk` (`element_type_id`)',
				'KEY `setting_installation_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `setting_installation_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `setting_installation_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `setting_installation_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `setting_installation_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('setting_institution',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'institution_id' => 'int(10) unsigned NOT NULL',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'key' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'value' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `setting_institution_institution_id_fk` (`institution_id`)',
				'KEY `setting_institution_element_type_id_fk` (`element_type_id`)',
				'KEY `setting_institution_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `setting_institution_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `setting_institution_institution_id_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`)',
				'CONSTRAINT `setting_institution_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `setting_institution_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `setting_institution_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('setting_site',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'site_id' => 'int(10) unsigned NOT NULL',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'key' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'value' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `setting_site_site_id_fk` (`site_id`)',
				'KEY `setting_site_element_type_id_fk` (`element_type_id`)',
				'KEY `setting_site_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `setting_site_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `setting_site_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
				'CONSTRAINT `setting_site_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `setting_site_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `setting_site_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('setting_specialty',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'specialty_id' => 'int(10) unsigned NOT NULL',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'key' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'value' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `setting_specialty_specialty_id_fk` (`specialty_id`)',
				'KEY `setting_specialty_element_type_id_fk` (`element_type_id`)',
				'KEY `setting_specialty_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `setting_specialty_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `setting_specialty_specialty_id_fk` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`)',
				'CONSTRAINT `setting_specialty_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `setting_specialty_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `setting_specialty_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('setting_subspecialty',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'subspecialty_id' => 'int(10) unsigned NOT NULL',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'key' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'value' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `setting_subspecialty_subspecialty_id_fk` (`subspecialty_id`)',
				'KEY `setting_subspecialty_element_type_id_fk` (`element_type_id`)',
				'KEY `setting_subspecialty_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `setting_subspecialty_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `setting_subspecialty_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)',
				'CONSTRAINT `setting_subspecialty_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `setting_subspecialty_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `setting_subspecialty_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('setting_firm',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'firm_id' => 'int(10) unsigned NOT NULL',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'key' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'value' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `setting_firm_firm_id_fk` (`firm_id`)',
				'KEY `setting_firm_element_type_id_fk` (`element_type_id`)',
				'KEY `setting_firm_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `setting_firm_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `setting_firm_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
				'CONSTRAINT `setting_firm_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `setting_firm_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `setting_firm_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('setting_user',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'user_id' => 'int(10) unsigned NOT NULL',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'key' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'value' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `setting_user_user_id_fk` (`user_id`)',
				'KEY `setting_user_element_type_id_fk` (`element_type_id`)',
				'KEY `setting_user_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `setting_user_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `setting_user_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `setting_user_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `setting_user_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `setting_user_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
	}

	public function down()
	{
		$this->dropTable('setting_user');
		$this->dropTable('setting_firm');
		$this->dropTable('setting_subspecialty');
		$this->dropTable('setting_specialty');
		$this->dropTable('setting_site');
		$this->dropTable('setting_institution');
		$this->dropTable('setting_installation');
		$this->dropTable('setting_metadata');
		$this->dropTable('setting_field_type');
	}
}
