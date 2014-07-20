<?php

class m140220_164630_drop_dead_contact_tables extends CDbMigration
{
	public function up()
	{
		$this->dropTable('institution_consultant_assignment');
		$this->dropTable('site_consultant_assignment');
		$this->dropTable('consultant');
		$this->dropTable('manual_contact');
		$this->dropTable('contact_type');
	}

	public function down()
	{
		$this->execute("CREATE TABLE `contact_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `letter_template_only` tinyint(4) NOT NULL DEFAULT '0',
  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `contact_type_last_modified_user_id_fk` (`last_modified_user_id`),
  KEY `contact_type_created_user_id_fk` (`created_user_id`),
  CONSTRAINT `contact_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `contact_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

		$this->execute("CREATE TABLE `manual_contact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contact_type_id` int(10) unsigned NOT NULL,
  `contact_id` int(10) unsigned NOT NULL,
  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  PRIMARY KEY (`id`),
  KEY `manual_contact_contact_id_fk_1` (`contact_id`),
  KEY `manual_contact_contact_type_id_fk_2` (`contact_type_id`),
  KEY `manual_contact_last_modified_user_id_fk` (`last_modified_user_id`),
  KEY `manual_contact_created_user_id_fk` (`created_user_id`),
  CONSTRAINT `manual_contact_contact_id_fk_1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
  CONSTRAINT `manual_contact_contact_type_id_fk_2` FOREIGN KEY (`contact_type_id`) REFERENCES `contact_type` (`id`),
  CONSTRAINT `manual_contact_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `manual_contact_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

		$this->execute("CREATE TABLE `consultant` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  `gmc_number` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `practitioner_code` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consultant_last_modified_user_id_fk` (`last_modified_user_id`),
  KEY `consultant_created_user_id_fk` (`created_user_id`),
  CONSTRAINT `consultant_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `consultant_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

		$this->execute("CREATE TABLE `site_consultant_assignment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(10) unsigned NOT NULL,
  `consultant_id` int(10) unsigned NOT NULL,
  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  PRIMARY KEY (`id`),
  KEY `site_consultant_assignment_site_id_fk` (`site_id`),
  KEY `site_consultant_assignment_consultant_id_fk` (`consultant_id`),
  KEY `site_consultant_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
  KEY `site_consultant_assignment_created_user_id_fk` (`created_user_id`),
  CONSTRAINT `site_consultant_assignment_consultant_id_fk` FOREIGN KEY (`consultant_id`) REFERENCES `consultant` (`id`),
  CONSTRAINT `site_consultant_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `site_consultant_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `site_consultant_assignment_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

		$this->execute("CREATE TABLE `institution_consultant_assignment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `consultant_id` int(10) unsigned NOT NULL,
  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  PRIMARY KEY (`id`),
  KEY `institution_consultant_assignment_institution_id_fk` (`institution_id`),
  KEY `institution_consultant_assignment_consultant_id_fk` (`consultant_id`),
  KEY `institution_consultant_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
  KEY `institution_consultant_assignment_created_user_id_fk` (`created_user_id`),
  CONSTRAINT `institution_consultant_assignment_consultant_id_fk` FOREIGN KEY (`consultant_id`) REFERENCES `consultant` (`id`),
  CONSTRAINT `institution_consultant_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `institution_consultant_assignment_institution_id_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`),
  CONSTRAINT `institution_consultant_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
	}
}
