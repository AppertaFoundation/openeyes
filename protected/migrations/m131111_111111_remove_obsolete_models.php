<?php

class m131111_111111_remove_obsolete_models extends CDbMigration
{
	public function up()
	{
		$this->dropTable('letter_template');
		$this->dropTable('phrase');
		$this->dropTable('phrase_by_firm');
		$this->dropTable('phrase_by_subspecialty');
		$this->dropTable('phrase_name');
		$this->dropTable('section_type');
		$this->dropTable('section');
	}

	public function down()
	{
		$this->execute(
			"CREATE TABLE `section` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(255) DEFAULT NULL,
							 `section_type_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `section_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `section_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `section_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `section_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `section_type` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(255) DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `section_type_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `section_type_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `section_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `section_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `phrase_name` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(255) DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `phrase_name_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `phrase_name_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `phrase_name_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `phrase_name_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `phrase` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `phrase` text ,
							 `section_id` int(10) unsigned NOT NULL,
							 `display_order` int(10) unsigned DEFAULT NULL,
							 `phrase_name_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `phrase_phrase_name_id_fk` (`phrase_name_id`),
							 KEY `phrase_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `phrase_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `phrase_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `phrase_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `phrase_phrase_name_id_fk` FOREIGN KEY (`phrase_name_id`) REFERENCES `phrase_name` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `phrase_by_firm` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `phrase` text ,
							 `section_id` int(10) unsigned NOT NULL,
							 `display_order` int(10) unsigned DEFAULT NULL,
							 `firm_id` int(10) unsigned NOT NULL,
							 `phrase_name_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `phrase_by_firm_section_fk` (`section_id`),
							 KEY `phrase_by_firm_firm_fk` (`firm_id`),
							 KEY `phrase_by_firm_phrase_name_id_fk` (`phrase_name_id`),
							 KEY `phrase_by_firm_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `phrase_by_firm_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `phrase_by_firm_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `phrase_by_firm_firm_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
							 CONSTRAINT `phrase_by_firm_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `phrase_by_firm_phrase_name_id_fk` FOREIGN KEY (`phrase_name_id`) REFERENCES `phrase_name` (`id`),
							 CONSTRAINT `phrase_by_firm_section_fk` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `phrase_by_subspecialty` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `phrase` text ,
							 `section_id` int(10) unsigned NOT NULL,
							 `display_order` int(10) unsigned DEFAULT NULL,
							 `subspecialty_id` int(10) unsigned NOT NULL,
							 `phrase_name_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `phrase_by_subspecialty_subspecialty_fk` (`subspecialty_id`),
							 KEY `phrase_by_subspecialty_section_fk` (`section_id`),
							 KEY `phrase_by_subspecialty_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `phrase_by_subspecialty_created_user_id_fk` (`created_user_id`),
							 KEY `phrase_by_subspecialty_phrase_name_id_fk` (`phrase_name_id`),
							 CONSTRAINT `phrase_by_subspecialty_section_fk` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`),
							 CONSTRAINT `phrase_by_subspecialty_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `phrase_by_subspecialty_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `phrase_by_subspecialty_phrase_name_id_fk` FOREIGN KEY (`phrase_name_id`) REFERENCES `phrase_name` (`id`),
							 CONSTRAINT `phrase_by_subspecialty_subspecialty_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `letter_template` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `subspecialty_id` int(10) unsigned NOT NULL,
							 `name` varchar(64) DEFAULT NULL,
							 `cc` int(10) unsigned NOT NULL,
							 `phrase` text NOT NULL,
							 `send_to` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `letter_template_ibfk_3` (`cc`),
							 KEY `letter_template_ibfk_2` (`send_to`),
							 KEY `letter_template_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `letter_template_created_user_id_fk` (`created_user_id`),
							 KEY `subspecialty_id` (`subspecialty_id`),
							 CONSTRAINT `letter_template_ibfk_1` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
							 CONSTRAINT `letter_template_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `letter_template_ibfk_2` FOREIGN KEY (`send_to`) REFERENCES `contact_type` (`id`),
							 CONSTRAINT `letter_template_ibfk_3` FOREIGN KEY (`cc`) REFERENCES `contact_type` (`id`),
							 CONSTRAINT `letter_template_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

	}
}
