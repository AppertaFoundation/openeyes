<?php

class m131128_113809_table_versioning extends OEMigration
{
	public function up()
	{
		$this->update('drug',array('default_frequency_id' => null),"default_frequency_id = 0");
		$this->update('drug',array('default_duration_id' => null),"default_duration_id = 0");
		$this->update('drug',array('default_route_id' => null),"default_route_id = 0");

		$proc_ids = array();
		foreach ($this->dbConnection->createCommand()->select("id")->from("proc")->queryAll() as $row) {
			$proc_ids[] = $row['id'];
		}

		if (!empty($proc_ids)) {
			$this->delete('proc_opcs_assignment',"proc_id not in (".implode(',',$proc_ids).")");
		}

		$this->renameColumn('disorder_tree','id','disorder_id');

		$this->addColumn('disorder_tree','id','int(10) unsigned NOT NULL');

		foreach ($this->dbConnection->createCommand()->select("*")->from("disorder_tree")->queryAll() as $i => $row) {
			$this->update('disorder_tree',array('id' => $i+1),"disorder_id = {$row['disorder_id']} and lft = {$row['lft']} and rght = {$row['rght']}");
		}

		$this->addPrimaryKey("id","disorder_tree","id");
		$this->alterColumn('disorder_tree','id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `address_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`address1` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
	`address2` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
	`city` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
	`postcode` varchar(10) DEFAULT NULL,
	`county` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
	`country_id` int(10) unsigned NOT NULL,
	`email` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`contact_id` int(10) unsigned NOT NULL,
	`date_start` datetime DEFAULT NULL,
	`date_end` datetime DEFAULT NULL,
	`address_type_id` int(10) unsigned DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_address_country_id_fk` (`country_id`),
	KEY `acv_address_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_address_created_user_id_fk` (`created_user_id`),
	KEY `acv_address_address_type_id_fk` (`address_type_id`),
	CONSTRAINT `acv_address_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
	CONSTRAINT `acv_address_address_type_id_fk` FOREIGN KEY (`address_type_id`) REFERENCES `address_type` (`id`),
	CONSTRAINT `acv_address_country_id_fk` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`),
	CONSTRAINT `acv_address_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_address_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('address_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','address_version');

		$this->createIndex('address_aid_fk','address_version','id');

		$this->addColumn('address_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('address_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','address_version','version_id');
		$this->alterColumn('address_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `address_type_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_address_type_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_address_type_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_address_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_address_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('address_type_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','address_type_version');

		$this->createIndex('address_type_aid_fk','address_type_version','id');
		$this->addForeignKey('address_type_aid_fk','address_type_version','id','address_type','id');

		$this->addColumn('address_type_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('address_type_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','address_type_version','version_id');
		$this->alterColumn('address_type_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `allergy_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(40) DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_allergy_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_allergy_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_allergy_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_allergy_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('allergy_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','allergy_version');

		$this->createIndex('allergy_aid_fk','allergy_version','id');
		$this->addForeignKey('allergy_aid_fk','allergy_version','id','allergy','id');

		$this->addColumn('allergy_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('allergy_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','allergy_version','version_id');
		$this->alterColumn('allergy_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `anaesthetic_agent_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`display_order` tinyint(3) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_et_ophtroperationnote_agent_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_et_ophtroperationnote_agent_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_et_ophtroperationnote_agent_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_et_ophtroperationnote_agent_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('anaesthetic_agent_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','anaesthetic_agent_version');

		$this->createIndex('anaesthetic_agent_aid_fk','anaesthetic_agent_version','id');
		$this->addForeignKey('anaesthetic_agent_aid_fk','anaesthetic_agent_version','id','anaesthetic_agent','id');

		$this->addColumn('anaesthetic_agent_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('anaesthetic_agent_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','anaesthetic_agent_version','version_id');
		$this->alterColumn('anaesthetic_agent_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `anaesthetic_complication_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`display_order` tinyint(3) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_et_ophtroperationnote_age_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_et_ophtroperationnote_age_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_et_ophtroperationnote_age_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_et_ophtroperationnote_age_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('anaesthetic_complication_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','anaesthetic_complication_version');

		$this->createIndex('anaesthetic_complication_aid_fk','anaesthetic_complication_version','id');
		$this->addForeignKey('anaesthetic_complication_aid_fk','anaesthetic_complication_version','id','anaesthetic_complication','id');

		$this->addColumn('anaesthetic_complication_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('anaesthetic_complication_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','anaesthetic_complication_version','version_id');
		$this->alterColumn('anaesthetic_complication_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `anaesthetic_delivery_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`display_order` tinyint(3) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_et_ophtroperationnote_del_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_et_ophtroperationnote_del_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_et_ophtroperationnote_del_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_et_ophtroperationnote_del_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('anaesthetic_delivery_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','anaesthetic_delivery_version');

		$this->createIndex('anaesthetic_delivery_aid_fk','anaesthetic_delivery_version','id');
		$this->addForeignKey('anaesthetic_delivery_aid_fk','anaesthetic_delivery_version','id','anaesthetic_delivery','id');

		$this->addColumn('anaesthetic_delivery_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('anaesthetic_delivery_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','anaesthetic_delivery_version','version_id');
		$this->alterColumn('anaesthetic_delivery_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `anaesthetic_type_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`code` varchar(3) NOT NULL DEFAULT '',
	`created_user_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL,
	`created_date` date NOT NULL DEFAULT '1900-01-01',
	`last_modified_date` date NOT NULL DEFAULT '1900-01-01',
	PRIMARY KEY (`id`),
	KEY `acv_anaesthetic_type_created_user_id_fk` (`created_user_id`),
	KEY `acv_anaesthetic_type_last_modified_user_id_fk` (`last_modified_user_id`),
	CONSTRAINT `acv_anaesthetic_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_anaesthetic_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('anaesthetic_type_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','anaesthetic_type_version');

		$this->createIndex('anaesthetic_type_aid_fk','anaesthetic_type_version','id');
		$this->addForeignKey('anaesthetic_type_aid_fk','anaesthetic_type_version','id','anaesthetic_type','id');

		$this->addColumn('anaesthetic_type_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('anaesthetic_type_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','anaesthetic_type_version','version_id');
		$this->alterColumn('anaesthetic_type_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `anaesthetist_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`display_order` tinyint(3) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_anaesthetist_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_anaesthetist_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_anaesthetist_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_anaesthetist_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('anaesthetist_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','anaesthetist_version');

		$this->createIndex('anaesthetist_aid_fk','anaesthetist_version','id');
		$this->addForeignKey('anaesthetist_aid_fk','anaesthetist_version','id','anaesthetist','id');

		$this->addColumn('anaesthetist_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('anaesthetist_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','anaesthetist_version','version_id');
		$this->alterColumn('anaesthetist_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `benefit_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) DEFAULT NULL,
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_benefit_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_benefit_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_benefit_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_benefit_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('benefit_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','benefit_version');

		$this->createIndex('benefit_aid_fk','benefit_version','id');
		$this->addForeignKey('benefit_aid_fk','benefit_version','id','benefit','id');

		$this->addColumn('benefit_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('benefit_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','benefit_version','version_id');
		$this->alterColumn('benefit_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `commissioning_body_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`code` varchar(16) DEFAULT NULL,
	`commissioning_body_type_id` int(10) unsigned NOT NULL,
	`contact_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_commissioning_body_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_commissioning_body_created_user_id_fk` (`created_user_id`),
	KEY `acv_commissioning_body_commissioning_body_type_id_fk` (`commissioning_body_type_id`),
	KEY `acv_commissioning_body_contact_id_fk` (`contact_id`),
	CONSTRAINT `acv_commissioning_body_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
	CONSTRAINT `acv_commissioning_body_commissioning_body_type_id_fk` FOREIGN KEY (`commissioning_body_type_id`) REFERENCES `commissioning_body_type` (`id`),
	CONSTRAINT `acv_commissioning_body_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_commissioning_body_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('commissioning_body_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','commissioning_body_version');

		$this->createIndex('commissioning_body_aid_fk','commissioning_body_version','id');
		$this->addForeignKey('commissioning_body_aid_fk','commissioning_body_version','id','commissioning_body','id');

		$this->addColumn('commissioning_body_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('commissioning_body_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','commissioning_body_version','version_id');
		$this->alterColumn('commissioning_body_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `commissioning_body_patient_assignment_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`commissioning_body_id` int(10) unsigned NOT NULL,
	`patient_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_commissioning_body_patient_assignment_cbid_fk` (`commissioning_body_id`),
	KEY `acv_commissioning_body_patient_assignment_created_user_id_fk` (`created_user_id`),
	KEY `acv_issioning_body_patient_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_commissioning_body_patient_assignment_pid_fk` (`patient_id`),
	CONSTRAINT `acv_commissioning_body_patient_assignment_pid_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
	CONSTRAINT `acv_commissioning_body_patient_assignment_cbid_fk` FOREIGN KEY (`commissioning_body_id`) REFERENCES `commissioning_body` (`id`),
	CONSTRAINT `acv_commissioning_body_patient_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_issioning_body_patient_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('commissioning_body_patient_assignment_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','commissioning_body_patient_assignment_version');

		$this->createIndex('commissioning_body_patient_assignment_aid_fk','commissioning_body_patient_assignment_version','id');

		$this->addColumn('commissioning_body_patient_assignment_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('commissioning_body_patient_assignment_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','commissioning_body_patient_assignment_version','version_id');
		$this->alterColumn('commissioning_body_patient_assignment_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `commissioning_body_practice_assignment_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`commissioning_body_id` int(10) unsigned NOT NULL,
	`practice_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_issioning_body_practice_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_commissioning_body_practice_assignment_created_user_id_fk` (`created_user_id`),
	KEY `acv_commissioning_body_practice_assignment_cbid_fk` (`commissioning_body_id`),
	KEY `acv_commissioning_body_practice_assignment_pid_fk` (`practice_id`),
	CONSTRAINT `acv_commissioning_body_practice_assignment_pid_fk` FOREIGN KEY (`practice_id`) REFERENCES `practice` (`id`),
	CONSTRAINT `acv_commissioning_body_practice_assignment_cbid_fk` FOREIGN KEY (`commissioning_body_id`) REFERENCES `commissioning_body` (`id`),
	CONSTRAINT `acv_commissioning_body_practice_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_issioning_body_practice_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('commissioning_body_practice_assignment_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','commissioning_body_practice_assignment_version');

		$this->createIndex('commissioning_body_practice_assignment_aid_fk','commissioning_body_practice_assignment_version','id');

		$this->addColumn('commissioning_body_practice_assignment_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('commissioning_body_practice_assignment_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','commissioning_body_practice_assignment_version','version_id');
		$this->alterColumn('commissioning_body_practice_assignment_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `commissioning_body_service_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`code` varchar(16) DEFAULT NULL,
	`commissioning_body_service_type_id` int(10) unsigned NOT NULL,
	`commissioning_body_id` int(10) unsigned DEFAULT NULL,
	`contact_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_commissioning_body_service_cbid_fk` (`commissioning_body_id`),
	KEY `acv_commissioning_body_service_cid_fk` (`contact_id`),
	KEY `acv_commissioning_body_service_created_user_id_fk` (`created_user_id`),
	KEY `acv_commissioning_body_service_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_commissioning_body_service_tid_fk` (`commissioning_body_service_type_id`),
	CONSTRAINT `acv_commissioning_body_service_tid_fk` FOREIGN KEY (`commissioning_body_service_type_id`) REFERENCES `commissioning_body_service_type` (`id`),
	CONSTRAINT `acv_commissioning_body_service_cbid_fk` FOREIGN KEY (`commissioning_body_id`) REFERENCES `commissioning_body` (`id`),
	CONSTRAINT `acv_commissioning_body_service_cid_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
	CONSTRAINT `acv_commissioning_body_service_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_commissioning_body_service_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('commissioning_body_service_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','commissioning_body_service_version');

		$this->createIndex('commissioning_body_service_aid_fk','commissioning_body_service_version','id');
		$this->addForeignKey('commissioning_body_service_aid_fk','commissioning_body_service_version','id','commissioning_body_service','id');

		$this->addColumn('commissioning_body_service_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('commissioning_body_service_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','commissioning_body_service_version','version_id');
		$this->alterColumn('commissioning_body_service_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `commissioning_body_service_type_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`shortname` varchar(16) DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_commissioning_body_service_type_created_user_id_fk` (`created_user_id`),
	KEY `acv_commissioning_body_service_type_last_modified_user_id_fk` (`last_modified_user_id`),
	CONSTRAINT `acv_commissioning_body_service_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_commissioning_body_service_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('commissioning_body_service_type_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','commissioning_body_service_type_version');

		$this->createIndex('commissioning_body_service_type_aid_fk','commissioning_body_service_type_version','id');
		$this->addForeignKey('commissioning_body_service_type_aid_fk','commissioning_body_service_type_version','id','commissioning_body_service_type','id');

		$this->addColumn('commissioning_body_service_type_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('commissioning_body_service_type_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','commissioning_body_service_type_version','version_id');
		$this->alterColumn('commissioning_body_service_type_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `commissioning_body_type_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`shortname` varchar(16) DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_commissioning_body_type_created_user_id_fk` (`created_user_id`),
	KEY `acv_commissioning_body_type_last_modified_user_id_fk` (`last_modified_user_id`),
	CONSTRAINT `acv_commissioning_body_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_commissioning_body_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('commissioning_body_type_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','commissioning_body_type_version');

		$this->createIndex('commissioning_body_type_aid_fk','commissioning_body_type_version','id');
		$this->addForeignKey('commissioning_body_type_aid_fk','commissioning_body_type_version','id','commissioning_body_type','id');

		$this->addColumn('commissioning_body_type_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('commissioning_body_type_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','commissioning_body_type_version','version_id');
		$this->alterColumn('commissioning_body_type_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `common_ophthalmic_disorder_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`disorder_id` int(10) unsigned NOT NULL,
	`subspecialty_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_disorder_id` (`disorder_id`),
	KEY `acv_common_ophthalmic_disorder_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_common_ophthalmic_disorder_created_user_id_fk` (`created_user_id`),
	KEY `acv_subspecialty_id` (`subspecialty_id`),
	CONSTRAINT `acv_common_ophthalmic_disorder_ibfk_2` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
	CONSTRAINT `acv_common_ophthalmic_disorder_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_common_ophthalmic_disorder_ibfk_1` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`),
	CONSTRAINT `acv_common_ophthalmic_disorder_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
		");

		$this->alterColumn('common_ophthalmic_disorder_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','common_ophthalmic_disorder_version');

		$this->createIndex('common_ophthalmic_disorder_aid_fk','common_ophthalmic_disorder_version','id');

		$this->addColumn('common_ophthalmic_disorder_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('common_ophthalmic_disorder_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','common_ophthalmic_disorder_version','version_id');
		$this->alterColumn('common_ophthalmic_disorder_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `common_previous_operation_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(1024) NOT NULL,
	`display_order` tinyint(1) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_common_previous_operation_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_common_previous_operation_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_common_previous_operation_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_common_previous_operation_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('common_previous_operation_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','common_previous_operation_version');

		$this->createIndex('common_previous_operation_aid_fk','common_previous_operation_version','id');

		$this->addColumn('common_previous_operation_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('common_previous_operation_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','common_previous_operation_version','version_id');
		$this->alterColumn('common_previous_operation_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `common_systemic_disorder_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`disorder_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_disorder_id` (`disorder_id`),
	KEY `acv_common_systemic_disorder_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_common_systemic_disorder_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_common_systemic_disorder_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_common_systemic_disorder_ibfk_1` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`),
	CONSTRAINT `acv_common_systemic_disorder_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
		");

		$this->alterColumn('common_systemic_disorder_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','common_systemic_disorder_version');

		$this->createIndex('common_systemic_disorder_aid_fk','common_systemic_disorder_version','id');

		$this->addColumn('common_systemic_disorder_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('common_systemic_disorder_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','common_systemic_disorder_version','version_id');
		$this->alterColumn('common_systemic_disorder_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `complication_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) DEFAULT NULL,
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_complication_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_complication_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_complication_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_complication_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('complication_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','complication_version');

		$this->createIndex('complication_aid_fk','complication_version','id');
		$this->addForeignKey('complication_aid_fk','complication_version','id','complication','id');

		$this->addColumn('complication_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('complication_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','complication_version','version_id');
		$this->alterColumn('complication_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `consultant_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`gmc_number` varchar(7) DEFAULT NULL,
	`practitioner_code` varchar(8) DEFAULT NULL,
	`gender` varchar(1) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_consultant_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_consultant_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_consultant_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_consultant_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('consultant_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','consultant_version');

		$this->createIndex('consultant_aid_fk','consultant_version','id');
		$this->addForeignKey('consultant_aid_fk','consultant_version','id','consultant','id');

		$this->addColumn('consultant_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('consultant_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','consultant_version','version_id');
		$this->alterColumn('consultant_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `contact_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`nick_name` varchar(80) DEFAULT NULL,
	`primary_phone` varchar(20) DEFAULT NULL,
	`title` varchar(20) DEFAULT NULL,
	`first_name` varchar(100) NOT NULL,
	`last_name` varchar(100) NOT NULL,
	`qualifications` varchar(200) DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`contact_label_id` int(10) unsigned DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_contact_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_contact_created_user_id_fk` (`created_user_id`),
	KEY `acv_contact_last_name_key` (`last_name`),
	KEY `acv_contact_contact_label_id_fk` (`contact_label_id`),
	CONSTRAINT `acv_contact_contact_label_id_fk` FOREIGN KEY (`contact_label_id`) REFERENCES `contact_label` (`id`),
	CONSTRAINT `acv_contact_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_contact_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('contact_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','contact_version');

		$this->createIndex('contact_aid_fk','contact_version','id');
		$this->addForeignKey('contact_aid_fk','contact_version','id','contact','id');

		$this->addColumn('contact_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('contact_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','contact_version','version_id');
		$this->alterColumn('contact_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `contact_label_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_contact_label_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_contact_label_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_contact_label_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_contact_label_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('contact_label_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','contact_label_version');

		$this->createIndex('contact_label_aid_fk','contact_label_version','id');
		$this->addForeignKey('contact_label_aid_fk','contact_label_version','id','contact_label','id');

		$this->addColumn('contact_label_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('contact_label_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','contact_label_version','version_id');
		$this->alterColumn('contact_label_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `contact_location_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`contact_id` int(10) unsigned NOT NULL,
	`site_id` int(10) unsigned DEFAULT NULL,
	`institution_id` int(10) unsigned DEFAULT NULL,
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_contact_location_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_contact_location_created_user_id_fk` (`created_user_id`),
	KEY `acv_contact_location_site_id_fk` (`site_id`),
	KEY `acv_contact_location_institution_id_fk` (`institution_id`),
	KEY `acv_contact_location_contact_id_fk` (`contact_id`),
	CONSTRAINT `acv_contact_location_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
	CONSTRAINT `acv_contact_location_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_contact_location_institution_id_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`),
	CONSTRAINT `acv_contact_location_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_contact_location_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('contact_location_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','contact_location_version');

		$this->createIndex('contact_location_aid_fk','contact_location_version','id');

		$this->addColumn('contact_location_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('contact_location_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','contact_location_version','version_id');
		$this->alterColumn('contact_location_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `contact_metadata_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`contact_id` int(10) unsigned NOT NULL,
	`key` varchar(64) NOT NULL,
	`value` varchar(64) NOT NULL,
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_contact_metadata_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_contact_metadata_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_contact_metadata_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_contact_metadata_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('contact_metadata_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','contact_metadata_version');

		$this->createIndex('contact_metadata_aid_fk','contact_metadata_version','id');

		$this->addColumn('contact_metadata_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('contact_metadata_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','contact_metadata_version','version_id');
		$this->alterColumn('contact_metadata_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `contact_type_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(40) NOT NULL,
	`letter_template_only` tinyint(4) NOT NULL DEFAULT '0',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_contact_type_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_contact_type_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_contact_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_contact_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('contact_type_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','contact_type_version');

		$this->createIndex('contact_type_aid_fk','contact_type_version','id');
		$this->addForeignKey('contact_type_aid_fk','contact_type_version','id','contact_type','id');

		$this->addColumn('contact_type_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('contact_type_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','contact_type_version','version_id');
		$this->alterColumn('contact_type_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `country_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`code` varchar(2) DEFAULT NULL,
	`name` varchar(50) DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_country_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_country_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_country_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_country_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('country_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','country_version');

		$this->createIndex('country_aid_fk','country_version','id');
		$this->addForeignKey('country_aid_fk','country_version','id','country','id');

		$this->addColumn('country_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('country_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','country_version','version_id');
		$this->alterColumn('country_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `disorder_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`fully_specified_name` varchar(255) NOT NULL,
	`term` varchar(255) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`specialty_id` int(10) unsigned DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_term` (`term`),
	KEY `acv_disorder_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_disorder_created_user_id_fk` (`created_user_id`),
	KEY `acv_disorder_specialty_fk` (`specialty_id`),
	CONSTRAINT `acv_disorder_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_disorder_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_disorder_specialty_fk` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('disorder_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','disorder_version');

		$this->createIndex('disorder_aid_fk','disorder_version','id');
		$this->addForeignKey('disorder_aid_fk','disorder_version','id','disorder','id');

		$this->addColumn('disorder_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('disorder_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','disorder_version','version_id');
		$this->alterColumn('disorder_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `disorder_tree_version` (
	`id` int(10) unsigned NOT NULL,
	`lft` int(10) unsigned NOT NULL,
	`rght` int(10) unsigned NOT NULL,
	`created_user_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL,
	`created_date` date NOT NULL DEFAULT '1900-01-01',
	`last_modified_date` date NOT NULL DEFAULT '1900-01-01',
	PRIMARY KEY (`id`),
	KEY `acv_id` (`id`),
	KEY `acv_lft` (`lft`),
	KEY `acv_rght` (`rght`),
	KEY `acv_disorder_tree_created_user_id_fk` (`created_user_id`),
	KEY `acv_disorder_tree_last_modified_user_id_fk` (`last_modified_user_id`),
	CONSTRAINT `acv_disorder_tree_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_disorder_tree_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('disorder_tree_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','disorder_tree_version');

		$this->createIndex('disorder_tree_aid_fk','disorder_tree_version','id');

		$this->addColumn('disorder_tree_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('disorder_tree_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','disorder_tree_version','version_id');
		$this->alterColumn('disorder_tree_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `drug_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`type_id` int(10) unsigned NOT NULL DEFAULT '1',
	`form_id` int(10) unsigned NOT NULL DEFAULT '1',
	`dose_unit` varchar(40) DEFAULT NULL,
	`default_dose` varchar(40) DEFAULT NULL,
	`default_route_id` int(10) unsigned DEFAULT NULL,
	`default_frequency_id` int(10) unsigned DEFAULT NULL,
	`default_duration_id` int(10) unsigned DEFAULT NULL,
	`preservative_free` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`aliases` text,
	`tallman` varchar(100) DEFAULT NULL,
	`discontinued` tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `acv_drug_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_drug_created_user_id_fk` (`created_user_id`),
	KEY `acv_drug_type_id_fk` (`type_id`),
	KEY `acv_drug_form_id_fk` (`form_id`),
	KEY `acv_drug_default_route_id_fk` (`default_route_id`),
	KEY `acv_drug_default_frequency_id_fk` (`default_frequency_id`),
	KEY `acv_drug_default_duration_id_fk` (`default_duration_id`),
	CONSTRAINT `acv_drug_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_drug_default_duration_id_fk` FOREIGN KEY (`default_duration_id`) REFERENCES `drug_duration` (`id`),
	CONSTRAINT `acv_drug_default_frequency_id_fk` FOREIGN KEY (`default_frequency_id`) REFERENCES `drug_frequency` (`id`),
	CONSTRAINT `acv_drug_default_route_id_fk` FOREIGN KEY (`default_route_id`) REFERENCES `drug_route` (`id`),
	CONSTRAINT `acv_drug_form_id_fk` FOREIGN KEY (`form_id`) REFERENCES `drug_form` (`id`),
	CONSTRAINT `acv_drug_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_drug_type_id_fk` FOREIGN KEY (`type_id`) REFERENCES `drug_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('drug_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','drug_version');

		$this->createIndex('drug_aid_fk','drug_version','id');
		$this->addForeignKey('drug_aid_fk','drug_version','id','drug','id');

		$this->addColumn('drug_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('drug_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','drug_version','version_id');
		$this->alterColumn('drug_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `drug_allergy_assignment_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`drug_id` int(10) unsigned NOT NULL,
	`allergy_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_drug_allergy_assignment_drug_id_fk` (`drug_id`),
	KEY `acv_drug_allergy_assignment_allergy_id_fk` (`allergy_id`),
	KEY `acv_drug_allergy_assignment_lmui_fk` (`last_modified_user_id`),
	KEY `acv_drug_allergy_assignment_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_drug_allergy_assignment_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_drug_allergy_assignment_allergy_id_fk` FOREIGN KEY (`allergy_id`) REFERENCES `allergy` (`id`),
	CONSTRAINT `acv_drug_allergy_assignment_drug_id_fk` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`id`),
	CONSTRAINT `acv_drug_allergy_assignment_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('drug_allergy_assignment_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','drug_allergy_assignment_version');

		$this->createIndex('drug_allergy_assignment_aid_fk','drug_allergy_assignment_version','id');

		$this->addColumn('drug_allergy_assignment_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('drug_allergy_assignment_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','drug_allergy_assignment_version','version_id');
		$this->alterColumn('drug_allergy_assignment_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `drug_duration_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(40) DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`display_order` int(10) unsigned NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`),
	KEY `acv_drug_duration_lmui_fk` (`last_modified_user_id`),
	KEY `acv_drug_duration_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_drug_duration_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_drug_duration_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('drug_duration_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','drug_duration_version');

		$this->createIndex('drug_duration_aid_fk','drug_duration_version','id');
		$this->addForeignKey('drug_duration_aid_fk','drug_duration_version','id','drug_duration','id');

		$this->addColumn('drug_duration_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('drug_duration_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','drug_duration_version','version_id');
		$this->alterColumn('drug_duration_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `drug_form_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(40) DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_drug_form_lmui_fk` (`last_modified_user_id`),
	KEY `acv_drug_form_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_drug_form_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_drug_form_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('drug_form_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','drug_form_version');

		$this->createIndex('drug_form_aid_fk','drug_form_version','id');
		$this->addForeignKey('drug_form_aid_fk','drug_form_version','id','drug_form','id');

		$this->addColumn('drug_form_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('drug_form_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','drug_form_version','version_id');
		$this->alterColumn('drug_form_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `drug_frequency_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(40) DEFAULT NULL,
	`long_name` varchar(40) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`display_order` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `acv_drug_frequency_lmui_fk` (`last_modified_user_id`),
	KEY `acv_drug_frequency_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_drug_frequency_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_drug_frequency_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('drug_frequency_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','drug_frequency_version');

		$this->createIndex('drug_frequency_aid_fk','drug_frequency_version','id');
		$this->addForeignKey('drug_frequency_aid_fk','drug_frequency_version','id','drug_frequency','id');

		$this->addColumn('drug_frequency_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('drug_frequency_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','drug_frequency_version','version_id');
		$this->alterColumn('drug_frequency_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `drug_route_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(40) DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`display_order` int(10) unsigned DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `acv_drug_route_lmui_fk` (`last_modified_user_id`),
	KEY `acv_drug_route_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_drug_route_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_drug_route_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('drug_route_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','drug_route_version');

		$this->createIndex('drug_route_aid_fk','drug_route_version','id');
		$this->addForeignKey('drug_route_aid_fk','drug_route_version','id','drug_route','id');

		$this->addColumn('drug_route_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('drug_route_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','drug_route_version','version_id');
		$this->alterColumn('drug_route_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `drug_route_option_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(40) DEFAULT NULL,
	`drug_route_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_drug_route_option_drug_route_id_fk` (`drug_route_id`),
	KEY `acv_drug_route_option_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_drug_route_option_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_drug_route_option_drug_route_id_fk` FOREIGN KEY (`drug_route_id`) REFERENCES `drug_route` (`id`),
	CONSTRAINT `acv_drug_route_option_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_drug_route_option_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('drug_route_option_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','drug_route_option_version');

		$this->createIndex('drug_route_option_aid_fk','drug_route_option_version','id');
		$this->addForeignKey('drug_route_option_aid_fk','drug_route_option_version','id','drug_route_option','id');

		$this->addColumn('drug_route_option_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('drug_route_option_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','drug_route_option_version','version_id');
		$this->alterColumn('drug_route_option_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `drug_set_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(40) DEFAULT NULL,
	`subspecialty_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_drug_set_subspecialty_id_fk` (`subspecialty_id`),
	KEY `acv_drug_set_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_drug_set_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_drug_set_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
	CONSTRAINT `acv_drug_set_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_drug_set_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('drug_set_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','drug_set_version');

		$this->createIndex('drug_set_aid_fk','drug_set_version','id');
		$this->addForeignKey('drug_set_aid_fk','drug_set_version','id','drug_set','id');

		$this->addColumn('drug_set_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('drug_set_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','drug_set_version','version_id');
		$this->alterColumn('drug_set_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `drug_set_item_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`drug_id` int(10) unsigned NOT NULL,
	`drug_set_id` int(10) unsigned NOT NULL,
	`frequency_id` int(10) unsigned DEFAULT NULL,
	`duration_id` int(10) unsigned DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`dose` varchar(40) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_drug_set_item_drug_id_fk` (`drug_id`),
	KEY `acv_drug_set_item_drug_set_id_fk` (`drug_set_id`),
	KEY `acv_drug_set_item_default_frequency_id_fk` (`frequency_id`),
	KEY `acv_drug_set_item_default_duration_id_fk` (`duration_id`),
	KEY `acv_drug_set_item_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_drug_set_item_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_drug_set_item_duration_id_fk` FOREIGN KEY (`duration_id`) REFERENCES `drug_duration` (`id`),
	CONSTRAINT `acv_drug_set_item_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_drug_set_item_drug_id_fk` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`id`),
	CONSTRAINT `acv_drug_set_item_drug_set_id_fk` FOREIGN KEY (`drug_set_id`) REFERENCES `drug_set` (`id`),
	CONSTRAINT `acv_drug_set_item_frequency_id_fk` FOREIGN KEY (`frequency_id`) REFERENCES `drug_frequency` (`id`),
	CONSTRAINT `acv_drug_set_item_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('drug_set_item_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','drug_set_item_version');

		$this->createIndex('drug_set_item_aid_fk','drug_set_item_version','id');

		$this->addColumn('drug_set_item_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('drug_set_item_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','drug_set_item_version','version_id');
		$this->alterColumn('drug_set_item_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `drug_set_item_taper_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`item_id` int(10) unsigned NOT NULL,
	`dose` varchar(40) DEFAULT NULL,
	`frequency_id` int(10) unsigned DEFAULT NULL,
	`duration_id` int(10) unsigned DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_drug_set_item_taper_f_fk` (`frequency_id`),
	KEY `acv_drug_set_item_taper_d_fk` (`duration_id`),
	KEY `acv_drug_set_item_taper_lmui_fk` (`last_modified_user_id`),
	KEY `acv_drug_set_item_taper_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_drug_set_item_taper_f_fk` FOREIGN KEY (`frequency_id`) REFERENCES `drug_frequency` (`id`),
	CONSTRAINT `acv_drug_set_item_taper_d_fk` FOREIGN KEY (`duration_id`) REFERENCES `drug_duration` (`id`),
	CONSTRAINT `acv_drug_set_item_taper_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_drug_set_item_taper_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('drug_set_item_taper_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','drug_set_item_taper_version');

		$this->createIndex('drug_set_item_taper_aid_fk','drug_set_item_taper_version','id');

		$this->addColumn('drug_set_item_taper_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('drug_set_item_taper_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','drug_set_item_taper_version','version_id');
		$this->alterColumn('drug_set_item_taper_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `drug_type_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(40) DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_drug_type_lmui_fk` (`last_modified_user_id`),
	KEY `acv_drug_type_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_drug_type_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_drug_type_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('drug_type_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','drug_type_version');

		$this->createIndex('drug_type_aid_fk','drug_type_version','id');
		$this->addForeignKey('drug_type_aid_fk','drug_type_version','id','drug_type','id');

		$this->addColumn('drug_type_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('drug_type_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','drug_type_version','version_id');
		$this->alterColumn('drug_type_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `element_type_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`class_name` varchar(255) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`event_type_id` int(10) unsigned NOT NULL DEFAULT '1',
	`display_order` int(10) unsigned NOT NULL DEFAULT '1',
	`default` tinyint(1) unsigned NOT NULL DEFAULT '1',
	`parent_element_type_id` int(10) unsigned DEFAULT NULL,
	`required` tinyint(1) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_element_type_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_element_type_created_user_id_fk` (`created_user_id`),
	KEY `acv_element_type_parent_et_fk` (`parent_element_type_id`),
	CONSTRAINT `acv_element_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_element_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_element_type_parent_et_fk` FOREIGN KEY (`parent_element_type_id`) REFERENCES `element_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('element_type_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','element_type_version');

		$this->createIndex('element_type_aid_fk','element_type_version','id');

		$this->addColumn('element_type_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('element_type_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','element_type_version','version_id');
		$this->alterColumn('element_type_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `element_type_anaesthetic_agent_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`element_type_id` int(10) unsigned NOT NULL,
	`anaesthetic_agent_id` int(10) unsigned NOT NULL,
	`display_order` tinyint(3) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_element_type_anaesthetic_agent_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_element_type_anaesthetic_agent_created_user_id_fk` (`created_user_id`),
	KEY `acv_element_type_anaesthetic_agent_element_type_id_fk` (`element_type_id`),
	KEY `acv_element_type_anaesthetic_agent_anaesthetic_agent_id_fk` (`anaesthetic_agent_id`),
	CONSTRAINT `acv_element_type_anaesthetic_agent_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_element_type_anaesthetic_agent_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_element_type_anaesthetic_agent_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
	CONSTRAINT `acv_element_type_anaesthetic_agent_anaesthetic_agent_id_fk` FOREIGN KEY (`anaesthetic_agent_id`) REFERENCES `anaesthetic_agent` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('element_type_anaesthetic_agent_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','element_type_anaesthetic_agent_version');

		$this->createIndex('element_type_anaesthetic_agent_aid_fk','element_type_anaesthetic_agent_version','id');

		$this->addColumn('element_type_anaesthetic_agent_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('element_type_anaesthetic_agent_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','element_type_anaesthetic_agent_version','version_id');
		$this->alterColumn('element_type_anaesthetic_agent_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `element_type_anaesthetic_complication_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`element_type_id` int(10) unsigned NOT NULL,
	`anaesthetic_complication_id` int(10) unsigned NOT NULL,
	`display_order` tinyint(3) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_element_type_ac_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_element_type_ac_created_user_id_fk` (`created_user_id`),
	KEY `acv_element_type_ac_element_type_id_fk` (`element_type_id`),
	KEY `acv_element_type_ac_anaesthetic_complication_id_fk` (`anaesthetic_complication_id`),
	CONSTRAINT `acv_element_type_ac_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_element_type_ac_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_element_type_ac_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
	CONSTRAINT `acv_element_type_ac_anaesthetic_complication_id_fk` FOREIGN KEY (`anaesthetic_complication_id`) REFERENCES `anaesthetic_complication` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('element_type_anaesthetic_complication_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','element_type_anaesthetic_complication_version');

		$this->createIndex('element_type_anaesthetic_complication_aid_fk','element_type_anaesthetic_complication_version','id');

		$this->addColumn('element_type_anaesthetic_complication_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('element_type_anaesthetic_complication_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','element_type_anaesthetic_complication_version','version_id');
		$this->alterColumn('element_type_anaesthetic_complication_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `element_type_anaesthetic_delivery_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`element_type_id` int(10) unsigned NOT NULL,
	`anaesthetic_delivery_id` int(10) unsigned NOT NULL,
	`display_order` tinyint(3) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_element_type_anaesthetic_delivery_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_element_type_anaesthetic_delivery_created_user_id_fk` (`created_user_id`),
	KEY `acv_element_type_anaesthetic_delivery_element_type_id_fk` (`element_type_id`),
	KEY `acv_element_type_anaesthetic_delivery_anaesthetic_delivery_id_fk` (`anaesthetic_delivery_id`),
	CONSTRAINT `acv_element_type_anaesthetic_delivery_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_element_type_anaesthetic_delivery_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_element_type_anaesthetic_delivery_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
	CONSTRAINT `acv_element_type_anaesthetic_delivery_anaesthetic_delivery_id_fk` FOREIGN KEY (`anaesthetic_delivery_id`) REFERENCES `anaesthetic_delivery` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('element_type_anaesthetic_delivery_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','element_type_anaesthetic_delivery_version');

		$this->createIndex('element_type_anaesthetic_delivery_aid_fk','element_type_anaesthetic_delivery_version','id');

		$this->addColumn('element_type_anaesthetic_delivery_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('element_type_anaesthetic_delivery_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','element_type_anaesthetic_delivery_version','version_id');
		$this->alterColumn('element_type_anaesthetic_delivery_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `element_type_anaesthetic_type_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`element_type_id` int(10) unsigned NOT NULL,
	`anaesthetic_type_id` int(10) unsigned NOT NULL,
	`display_order` int(10) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL,
	`created_date` date NOT NULL DEFAULT '1900-01-01',
	`last_modified_date` date NOT NULL DEFAULT '1900-01-01',
	PRIMARY KEY (`id`),
	KEY `acv_element_type_anaesthetic_type_fk1` (`element_type_id`),
	KEY `acv_element_type_anaesthetic_type_fk2` (`anaesthetic_type_id`),
	KEY `acv_element_type_anaesthetic_type_created_user_id_fk` (`created_user_id`),
	KEY `acv_element_type_anaesthetic_type_last_modified_user_id_fk` (`last_modified_user_id`),
	CONSTRAINT `acv_element_type_anaesthetic_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_element_type_anaesthetic_type_fk1` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
	CONSTRAINT `acv_element_type_anaesthetic_type_fk2` FOREIGN KEY (`anaesthetic_type_id`) REFERENCES `anaesthetic_type` (`id`),
	CONSTRAINT `acv_element_type_anaesthetic_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('element_type_anaesthetic_type_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','element_type_anaesthetic_type_version');

		$this->createIndex('element_type_anaesthetic_type_aid_fk','element_type_anaesthetic_type_version','id');

		$this->addColumn('element_type_anaesthetic_type_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('element_type_anaesthetic_type_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','element_type_anaesthetic_type_version','version_id');
		$this->alterColumn('element_type_anaesthetic_type_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `element_type_anaesthetist_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`element_type_id` int(10) unsigned NOT NULL,
	`anaesthetist_id` int(10) unsigned NOT NULL,
	`display_order` tinyint(3) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_element_type_anaesthetist_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_element_type_anaesthetist_created_user_id_fk` (`created_user_id`),
	KEY `acv_element_type_anaesthetist_element_type_id_fk` (`element_type_id`),
	KEY `acv_element_type_anaesthetist_anaesthetist_id_fk` (`anaesthetist_id`),
	CONSTRAINT `acv_element_type_anaesthetist_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_element_type_anaesthetist_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_element_type_anaesthetist_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
	CONSTRAINT `acv_element_type_anaesthetist_anaesthetist_id_fk` FOREIGN KEY (`anaesthetist_id`) REFERENCES `anaesthetist` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('element_type_anaesthetist_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','element_type_anaesthetist_version');

		$this->createIndex('element_type_anaesthetist_aid_fk','element_type_anaesthetist_version','id');

		$this->addColumn('element_type_anaesthetist_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('element_type_anaesthetist_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','element_type_anaesthetist_version','version_id');
		$this->alterColumn('element_type_anaesthetist_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `element_type_eye_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`element_type_id` int(10) unsigned NOT NULL,
	`eye_id` int(10) unsigned NOT NULL,
	`display_order` tinyint(1) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL,
	`created_date` date NOT NULL DEFAULT '1900-01-01',
	`last_modified_date` date NOT NULL DEFAULT '1900-01-01',
	PRIMARY KEY (`id`),
	KEY `acv_element_type_eye_fk1` (`element_type_id`),
	KEY `acv_element_type_eye_fk2` (`eye_id`),
	KEY `acv_element_type_eye_created_user_id_fk` (`created_user_id`),
	KEY `acv_element_type_eye_last_modified_user_id_fk` (`last_modified_user_id`),
	CONSTRAINT `acv_element_type_eye_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_element_type_eye_fk1` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
	CONSTRAINT `acv_element_type_eye_fk2` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
	CONSTRAINT `acv_element_type_eye_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('element_type_eye_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','element_type_eye_version');

		$this->createIndex('element_type_eye_aid_fk','element_type_eye_version','id');

		$this->addColumn('element_type_eye_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('element_type_eye_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','element_type_eye_version','version_id');
		$this->alterColumn('element_type_eye_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `element_type_priority_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`element_type_id` int(10) unsigned NOT NULL,
	`priority_id` int(10) unsigned NOT NULL,
	`display_order` tinyint(1) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL,
	`created_date` date NOT NULL DEFAULT '1900-01-01',
	`last_modified_date` date NOT NULL DEFAULT '1900-01-01',
	PRIMARY KEY (`id`),
	KEY `acv_element_type_priority_fk1` (`element_type_id`),
	KEY `acv_element_type_priority_fk2` (`priority_id`),
	KEY `acv_element_type_priority_created_user_id_fk` (`created_user_id`),
	KEY `acv_element_type_priority_last_modified_user_id_fk` (`last_modified_user_id`),
	CONSTRAINT `acv_element_type_priority_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_element_type_priority_fk1` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
	CONSTRAINT `acv_element_type_priority_fk2` FOREIGN KEY (`priority_id`) REFERENCES `priority` (`id`),
	CONSTRAINT `acv_element_type_priority_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('element_type_priority_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','element_type_priority_version');

		$this->createIndex('element_type_priority_aid_fk','element_type_priority_version','id');

		$this->addColumn('element_type_priority_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('element_type_priority_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','element_type_priority_version','version_id');
		$this->alterColumn('element_type_priority_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `episode_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`patient_id` int(10) unsigned NOT NULL,
	`firm_id` int(10) unsigned DEFAULT NULL,
	`start_date` datetime NOT NULL,
	`end_date` datetime DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`episode_status_id` int(10) unsigned NOT NULL DEFAULT '1',
	`legacy` tinyint(1) unsigned DEFAULT '0',
	`deleted` int(10) unsigned NOT NULL DEFAULT '0',
	`eye_id` int(10) unsigned DEFAULT NULL,
	`disorder_id` int(10) unsigned DEFAULT NULL,
	`support_services` tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `acv_episode_1` (`patient_id`),
	KEY `acv_episode_2` (`firm_id`),
	KEY `acv_episode_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_episode_created_user_id_fk` (`created_user_id`),
	KEY `acv_episode_episode_status_id_fk` (`episode_status_id`),
	KEY `acv_episode_eye_id_fk` (`eye_id`),
	KEY `acv_episode_disorder_id_fk` (`disorder_id`),
	CONSTRAINT `acv_episode_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
	CONSTRAINT `acv_episode_2` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
	CONSTRAINT `acv_episode_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_episode_disorder_id_fk` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`),
	CONSTRAINT `acv_episode_episode_status_id_fk` FOREIGN KEY (`episode_status_id`) REFERENCES `episode_status` (`id`),
	CONSTRAINT `acv_episode_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
	CONSTRAINT `acv_episode_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('episode_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','episode_version');

		$this->createIndex('episode_aid_fk','episode_version','id');
		$this->addForeignKey('episode_aid_fk','episode_version','id','episode','id');

		$this->addColumn('episode_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('episode_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','episode_version','version_id');
		$this->alterColumn('episode_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `episode_status_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL DEFAULT '',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`order` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `acv_episode_status_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_episode_status_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_episode_status_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_episode_status_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('episode_status_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','episode_status_version');

		$this->createIndex('episode_status_aid_fk','episode_status_version','id');

		$this->addColumn('episode_status_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('episode_status_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','episode_status_version','version_id');
		$this->alterColumn('episode_status_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ethnic_group_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`code` varchar(1) NOT NULL,
	`display_order` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_ethnic_group_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_ethnic_group_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_ethnic_group_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ethnic_group_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ethnic_group_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ethnic_group_version');

		$this->createIndex('ethnic_group_aid_fk','ethnic_group_version','id');

		$this->addColumn('ethnic_group_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ethnic_group_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ethnic_group_version','version_id');
		$this->alterColumn('ethnic_group_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `event_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`episode_id` int(10) unsigned DEFAULT NULL,
	`created_user_id` int(10) unsigned NOT NULL,
	`event_type_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`info` varchar(1024) CHARACTER SET utf8 DEFAULT NULL,
	`deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `acv_event_1` (`episode_id`),
	KEY `acv_event_2` (`created_user_id`),
	KEY `acv_event_3` (`event_type_id`),
	KEY `acv_event_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_idx_event_episode_id` (`episode_id`),
	KEY `acv_idx_event_event_type_id` (`event_type_id`),
	CONSTRAINT `acv_event_1` FOREIGN KEY (`episode_id`) REFERENCES `episode` (`id`),
	CONSTRAINT `acv_event_3` FOREIGN KEY (`event_type_id`) REFERENCES `event_type` (`id`),
	CONSTRAINT `acv_event_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_event_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
		");

		$this->alterColumn('event_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','event_version');

		$this->createIndex('event_aid_fk','event_version','id');
		$this->addForeignKey('event_aid_fk','event_version','id','event','id');

		$this->addColumn('event_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('event_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','event_version','version_id');
		$this->alterColumn('event_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `event_group_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`code` varchar(2) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('event_group_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','event_group_version');

		$this->createIndex('event_group_aid_fk','event_group_version','id');

		$this->addColumn('event_group_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('event_group_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','event_group_version','version_id');
		$this->alterColumn('event_group_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `event_issue_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` int(10) unsigned NOT NULL,
	`issue_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_event_issue_event_id` (`event_id`),
	KEY `acv_event_issue_issue_id` (`issue_id`),
	KEY `acv_event_issue_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_event_issue_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_event_issue_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_event_issue_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
	CONSTRAINT `acv_event_issue_issue_id` FOREIGN KEY (`issue_id`) REFERENCES `issue` (`id`),
	CONSTRAINT `acv_event_issue_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('event_issue_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','event_issue_version');

		$this->createIndex('event_issue_aid_fk','event_issue_version','id');

		$this->addColumn('event_issue_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('event_issue_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','event_issue_version','version_id');
		$this->alterColumn('event_issue_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `event_type_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(40) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`event_group_id` int(10) unsigned NOT NULL,
	`class_name` varchar(200) NOT NULL,
	`support_services` tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `acv_event_type_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_event_type_created_user_id_fk` (`created_user_id`),
	KEY `acv_event_type_event_group_id_fk` (`event_group_id`),
	CONSTRAINT `acv_event_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_event_type_event_group_id_fk` FOREIGN KEY (`event_group_id`) REFERENCES `event_group` (`id`),
	CONSTRAINT `acv_event_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('event_type_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','event_type_version');

		$this->createIndex('event_type_aid_fk','event_type_version','id');

		$this->addColumn('event_type_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('event_type_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','event_type_version','version_id');
		$this->alterColumn('event_type_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `family_history_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`patient_id` int(10) unsigned NOT NULL,
	`relative_id` int(10) unsigned NOT NULL,
	`side_id` int(10) unsigned NOT NULL,
	`condition_id` int(10) unsigned NOT NULL,
	`comments` varchar(1024) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_family_history_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_family_history_created_user_id_fk` (`created_user_id`),
	KEY `acv_family_history_patient_id_fk` (`patient_id`),
	KEY `acv_family_history_relative_id_fk` (`relative_id`),
	KEY `acv_family_history_side_id_fk` (`side_id`),
	KEY `acv_family_history_condition_id_fk` (`condition_id`),
	CONSTRAINT `acv_family_history_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_family_history_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_family_history_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
	CONSTRAINT `acv_family_history_relative_id_fk` FOREIGN KEY (`relative_id`) REFERENCES `family_history_relative` (`id`),
	CONSTRAINT `acv_family_history_side_id_fk` FOREIGN KEY (`side_id`) REFERENCES `family_history_side` (`id`),
	CONSTRAINT `acv_family_history_condition_id_fk` FOREIGN KEY (`condition_id`) REFERENCES `family_history_condition` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('family_history_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','family_history_version');

		$this->createIndex('family_history_aid_fk','family_history_version','id');

		$this->addColumn('family_history_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('family_history_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','family_history_version','version_id');
		$this->alterColumn('family_history_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `family_history_condition_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`display_order` tinyint(1) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_family_history_condition_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_family_history_condition_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_family_history_condition_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_family_history_condition_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('family_history_condition_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','family_history_condition_version');

		$this->createIndex('family_history_condition_aid_fk','family_history_condition_version','id');
		$this->addForeignKey('family_history_condition_aid_fk','family_history_condition_version','id','family_history_condition','id');

		$this->addColumn('family_history_condition_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('family_history_condition_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','family_history_condition_version','version_id');
		$this->alterColumn('family_history_condition_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `family_history_relative_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`display_order` tinyint(1) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_family_history_relative_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_family_history_relative_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_family_history_relative_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_family_history_relative_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('family_history_relative_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','family_history_relative_version');

		$this->createIndex('family_history_relative_aid_fk','family_history_relative_version','id');
		$this->addForeignKey('family_history_relative_aid_fk','family_history_relative_version','id','family_history_relative','id');

		$this->addColumn('family_history_relative_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('family_history_relative_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','family_history_relative_version','version_id');
		$this->alterColumn('family_history_relative_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `family_history_side_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`display_order` tinyint(1) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_family_history_side_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_family_history_side_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_family_history_side_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_family_history_side_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('family_history_side_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','family_history_side_version');

		$this->createIndex('family_history_side_aid_fk','family_history_side_version','id');
		$this->addForeignKey('family_history_side_aid_fk','family_history_side_version','id','family_history_side','id');

		$this->addColumn('family_history_side_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('family_history_side_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','family_history_side_version','version_id');
		$this->alterColumn('family_history_side_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `firm_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`service_subspecialty_assignment_id` int(10) unsigned DEFAULT NULL,
	`pas_code` varchar(4) DEFAULT NULL,
	`name` varchar(40) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`consultant_id` int(10) unsigned DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_firm_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_firm_created_user_id_fk` (`created_user_id`),
	KEY `acv_service_subspecialty_assignment_id` (`service_subspecialty_assignment_id`),
	KEY `acv_firm_consultant_id_fk` (`consultant_id`),
	CONSTRAINT `acv_firm_consultant_id_fk` FOREIGN KEY (`consultant_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_firm_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_firm_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_service_subspecialty_assignment_id` FOREIGN KEY (`service_subspecialty_assignment_id`) REFERENCES `service_subspecialty_assignment` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('firm_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','firm_version');

		$this->createIndex('firm_aid_fk','firm_version','id');
		$this->addForeignKey('firm_aid_fk','firm_version','id','firm','id');

		$this->addColumn('firm_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('firm_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','firm_version','version_id');
		$this->alterColumn('firm_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `firm_user_assignment_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`firm_id` int(10) unsigned NOT NULL,
	`user_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_firm_id` (`firm_id`),
	KEY `acv_user_id` (`user_id`),
	KEY `acv_firm_user_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_firm_user_assignment_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_firm_id` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
	CONSTRAINT `acv_firm_user_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_firm_user_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
		");

		$this->alterColumn('firm_user_assignment_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','firm_user_assignment_version');

		$this->createIndex('firm_user_assignment_aid_fk','firm_user_assignment_version','id');

		$this->addColumn('firm_user_assignment_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('firm_user_assignment_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','firm_user_assignment_version','version_id');
		$this->alterColumn('firm_user_assignment_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `gp_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`obj_prof` varchar(20) NOT NULL,
	`nat_id` varchar(20) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`contact_id` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_gp_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_gp_created_user_id_fk` (`created_user_id`),
	KEY `acv_gp_contact_id_fk` (`contact_id`),
	CONSTRAINT `acv_gp_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
	CONSTRAINT `acv_gp_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_gp_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('gp_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','gp_version');

		$this->createIndex('gp_aid_fk','gp_version','id');

		$this->addColumn('gp_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('gp_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','gp_version','version_id');
		$this->alterColumn('gp_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `institution_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`remote_id` varchar(10) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`short_name` varchar(64) NOT NULL,
	`contact_id` int(10) unsigned NOT NULL,
	`source_id` int(10) unsigned DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_institution_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_institution_created_user_id_fk` (`created_user_id`),
	KEY `acv_institution_contact_id_fk` (`contact_id`),
	KEY `acv_institution_source_id_fk` (`source_id`),
	CONSTRAINT `acv_institution_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
	CONSTRAINT `acv_institution_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_institution_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_institution_source_id_fk` FOREIGN KEY (`source_id`) REFERENCES `import_source` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('institution_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','institution_version');

		$this->createIndex('institution_aid_fk','institution_version','id');
		$this->addForeignKey('institution_aid_fk','institution_version','id','institution','id');

		$this->addColumn('institution_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('institution_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','institution_version','version_id');
		$this->alterColumn('institution_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `institution_consultant_assignment_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`institution_id` int(10) unsigned NOT NULL,
	`consultant_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_institution_consultant_assignment_institution_id_fk` (`institution_id`),
	KEY `acv_institution_consultant_assignment_consultant_id_fk` (`consultant_id`),
	KEY `acv_institution_consultant_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_institution_consultant_assignment_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_institution_consultant_assignment_institution_id_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`),
	CONSTRAINT `acv_institution_consultant_assignment_consultant_id_fk` FOREIGN KEY (`consultant_id`) REFERENCES `consultant` (`id`),
	CONSTRAINT `acv_institution_consultant_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_institution_consultant_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('institution_consultant_assignment_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','institution_consultant_assignment_version');

		$this->createIndex('institution_consultant_assignment_aid_fk','institution_consultant_assignment_version','id');

		$this->addColumn('institution_consultant_assignment_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('institution_consultant_assignment_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','institution_consultant_assignment_version','version_id');
		$this->alterColumn('institution_consultant_assignment_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `issue_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(1024) DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_issue_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_issue_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_issue_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_issue_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('issue_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','issue_version');

		$this->createIndex('issue_aid_fk','issue_version','id');
		$this->addForeignKey('issue_aid_fk','issue_version','id','issue','id');

		$this->addColumn('issue_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('issue_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','issue_version','version_id');
		$this->alterColumn('issue_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `language_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(32) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_language_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_language_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_language_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_language_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('language_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','language_version');

		$this->createIndex('language_aid_fk','language_version','id');

		$this->addColumn('language_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('language_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','language_version','version_id');
		$this->alterColumn('language_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `manual_contact_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`contact_type_id` int(10) unsigned NOT NULL,
	`contact_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_manual_contact_contact_id_fk_1` (`contact_id`),
	KEY `acv_manual_contact_contact_type_id_fk_2` (`contact_type_id`),
	KEY `acv_manual_contact_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_manual_contact_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_manual_contact_contact_id_fk_1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
	CONSTRAINT `acv_manual_contact_contact_type_id_fk_2` FOREIGN KEY (`contact_type_id`) REFERENCES `contact_type` (`id`),
	CONSTRAINT `acv_manual_contact_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_manual_contact_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('manual_contact_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','manual_contact_version');

		$this->createIndex('manual_contact_aid_fk','manual_contact_version','id');

		$this->addColumn('manual_contact_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('manual_contact_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','manual_contact_version','version_id');
		$this->alterColumn('manual_contact_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `medication_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`patient_id` int(10) unsigned NOT NULL,
	`drug_id` int(10) unsigned NOT NULL,
	`route_id` int(10) unsigned NOT NULL,
	`option_id` int(10) unsigned DEFAULT NULL,
	`frequency_id` int(10) unsigned NOT NULL,
	`start_date` date NOT NULL,
	`end_date` date DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_medication_lmui_fk` (`last_modified_user_id`),
	KEY `acv_medication_cui_fk` (`created_user_id`),
	KEY `acv_medication_drug_id_fk` (`drug_id`),
	KEY `acv_medication_route_id_fk` (`route_id`),
	KEY `acv_medication_option_id_fk` (`option_id`),
	KEY `acv_medication_frequency_id_fk` (`frequency_id`),
	CONSTRAINT `acv_medication_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_medication_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_medication_drug_id_fk` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`id`),
	CONSTRAINT `acv_medication_route_id_fk` FOREIGN KEY (`route_id`) REFERENCES `drug_route` (`id`),
	CONSTRAINT `acv_medication_option_id_fk` FOREIGN KEY (`option_id`) REFERENCES `drug_route_option` (`id`),
	CONSTRAINT `acv_medication_frequency_id_fk` FOREIGN KEY (`frequency_id`) REFERENCES `drug_frequency` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('medication_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','medication_version');

		$this->createIndex('medication_aid_fk','medication_version','id');
		$this->addForeignKey('medication_aid_fk','medication_version','id','medication','id');

		$this->addColumn('medication_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('medication_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','medication_version','version_id');
		$this->alterColumn('medication_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `nsc_grade_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(3) NOT NULL,
	`type` tinyint(1) DEFAULT '0',
	`medical_phrase` varchar(5000) NOT NULL,
	`layman_phrase` varchar(1000) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_nsc_grade_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_nsc_grade_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_nsc_grade_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_nsc_grade_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('nsc_grade_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','nsc_grade_version');

		$this->createIndex('nsc_grade_aid_fk','nsc_grade_version','id');
		$this->addForeignKey('nsc_grade_aid_fk','nsc_grade_version','id','nsc_grade','id');

		$this->addColumn('nsc_grade_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('nsc_grade_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','nsc_grade_version','version_id');
		$this->alterColumn('nsc_grade_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `opcs_code_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) CHARACTER SET latin1 NOT NULL,
	`description` varchar(255) CHARACTER SET latin1 NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_opcs_code_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_opcs_code_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_opcs_code_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_opcs_code_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('opcs_code_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','opcs_code_version');

		$this->createIndex('opcs_code_aid_fk','opcs_code_version','id');
		$this->addForeignKey('opcs_code_aid_fk','opcs_code_version','id','opcs_code','id');

		$this->addColumn('opcs_code_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('opcs_code_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','opcs_code_version','version_id');
		$this->alterColumn('opcs_code_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `operative_device_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_operative_device_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_operative_device_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_operative_device_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_operative_device_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('operative_device_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','operative_device_version');

		$this->createIndex('operative_device_aid_fk','operative_device_version','id');
		$this->addForeignKey('operative_device_aid_fk','operative_device_version','id','operative_device','id');

		$this->addColumn('operative_device_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('operative_device_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','operative_device_version','version_id');
		$this->alterColumn('operative_device_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `patient_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`pas_key` int(10) unsigned DEFAULT NULL,
	`dob` date DEFAULT NULL,
	`gender` varchar(1) DEFAULT NULL,
	`hos_num` varchar(40) DEFAULT NULL,
	`nhs_num` varchar(40) DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`gp_id` int(10) unsigned DEFAULT NULL,
	`date_of_death` date DEFAULT NULL,
	`practice_id` int(10) unsigned DEFAULT NULL,
	`ethnic_group_id` int(10) unsigned DEFAULT NULL,
	`contact_id` int(10) unsigned NOT NULL,
	`no_allergies_date` datetime DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_patient_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_patient_created_user_id_fk` (`created_user_id`),
	KEY `acv_patient_gp_id_fk` (`gp_id`),
	KEY `acv_patient_practice_id_fk` (`practice_id`),
	KEY `acv_patient_ethnic_group_id_fk` (`ethnic_group_id`),
	KEY `acv_patient_contact_id_fk` (`contact_id`),
	CONSTRAINT `acv_patient_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
	CONSTRAINT `acv_patient_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_patient_ethnic_group_id_fk` FOREIGN KEY (`ethnic_group_id`) REFERENCES `ethnic_group` (`id`),
	CONSTRAINT `acv_patient_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_patient_practice_id_fk` FOREIGN KEY (`practice_id`) REFERENCES `practice` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('patient_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','patient_version');

		$this->createIndex('patient_aid_fk','patient_version','id');

		$this->addColumn('patient_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('patient_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','patient_version','version_id');
		$this->alterColumn('patient_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `patient_allergy_assignment_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`patient_id` int(10) unsigned NOT NULL,
	`allergy_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_patient_allergy_assignment_patient_id_fk` (`patient_id`),
	KEY `acv_patient_allergy_assignment_allergy_id_fk` (`allergy_id`),
	KEY `acv_patient_allergy_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_patient_allergy_assignment_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_patient_allergy_assignment_allergy_id_fk` FOREIGN KEY (`allergy_id`) REFERENCES `allergy` (`id`),
	CONSTRAINT `acv_patient_allergy_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_patient_allergy_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_patient_allergy_assignment_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('patient_allergy_assignment_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','patient_allergy_assignment_version');

		$this->createIndex('patient_allergy_assignment_aid_fk','patient_allergy_assignment_version','id');

		$this->addColumn('patient_allergy_assignment_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('patient_allergy_assignment_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','patient_allergy_assignment_version','version_id');
		$this->alterColumn('patient_allergy_assignment_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `patient_contact_assignment_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`patient_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`location_id` int(10) unsigned DEFAULT NULL,
	`contact_id` int(10) unsigned DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_patient_consultant_assignment_patient_id_fk` (`patient_id`),
	KEY `acv_patient_consultant_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_patient_consultant_assignment_created_user_id_fk` (`created_user_id`),
	KEY `acv_patient_contact_assignment_location_id_fk` (`location_id`),
	KEY `acv_patient_contact_assignment_contact_id_fk` (`contact_id`),
	CONSTRAINT `acv_patient_consultant_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_patient_consultant_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_patient_consultant_assignment_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
	CONSTRAINT `acv_patient_contact_assignment_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('patient_contact_assignment_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','patient_contact_assignment_version');

		$this->createIndex('patient_contact_assignment_aid_fk','patient_contact_assignment_version','id');

		$this->addColumn('patient_contact_assignment_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('patient_contact_assignment_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','patient_contact_assignment_version','version_id');
		$this->alterColumn('patient_contact_assignment_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `patient_oph_info_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`patient_id` int(10) unsigned NOT NULL,
	`cvi_status_date` varchar(10) NOT NULL,
	`cvi_status_id` int(10) unsigned NOT NULL,
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_patient_oph_info_patient_id_fk` (`patient_id`),
	KEY `acv_patient_oph_info_cvi_status_id_fk` (`cvi_status_id`),
	KEY `acv_patient_oph_info_lmui_fk` (`last_modified_user_id`),
	KEY `acv_patient_oph_info_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_patient_oph_info_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
	CONSTRAINT `acv_patient_oph_info_cvi_status_id_fk` FOREIGN KEY (`cvi_status_id`) REFERENCES `patient_oph_info_cvi_status` (`id`),
	CONSTRAINT `acv_patient_oph_info_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_patient_oph_info_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('patient_oph_info_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','patient_oph_info_version');

		$this->createIndex('patient_oph_info_aid_fk','patient_oph_info_version','id');

		$this->addColumn('patient_oph_info_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('patient_oph_info_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','patient_oph_info_version','version_id');
		$this->alterColumn('patient_oph_info_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `patient_oph_info_cvi_status_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(128) NOT NULL,
	`display_order` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_patient_oph_info_cvi_status_lmui_fk` (`last_modified_user_id`),
	KEY `acv_patient_oph_info_cvi_status_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_patient_oph_info_cvi_status_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_patient_oph_info_cvi_status_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('patient_oph_info_cvi_status_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','patient_oph_info_cvi_status_version');

		$this->createIndex('patient_oph_info_cvi_status_aid_fk','patient_oph_info_cvi_status_version','id');
		$this->addForeignKey('patient_oph_info_cvi_status_aid_fk','patient_oph_info_cvi_status_version','id','patient_oph_info_cvi_status','id');

		$this->addColumn('patient_oph_info_cvi_status_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('patient_oph_info_cvi_status_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','patient_oph_info_cvi_status_version','version_id');
		$this->alterColumn('patient_oph_info_cvi_status_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `patient_shortcode_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`event_type_id` int(10) unsigned DEFAULT NULL,
	`default_code` varchar(3) NOT NULL,
	`code` varchar(3) NOT NULL,
	`method` varchar(64) NOT NULL,
	`description` varchar(1024) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_patient_shortcode_event_type_id_fk` (`event_type_id`),
	KEY `acv_patient_shortcode_lmui_fk` (`last_modified_user_id`),
	KEY `acv_patient_shortcode_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_patient_shortcode_event_type_id_fk` FOREIGN KEY (`event_type_id`) REFERENCES `event_type` (`id`),
	CONSTRAINT `acv_patient_shortcode_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_patient_shortcode_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('patient_shortcode_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','patient_shortcode_version');

		$this->createIndex('patient_shortcode_aid_fk','patient_shortcode_version','id');

		$this->addColumn('patient_shortcode_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('patient_shortcode_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','patient_shortcode_version','version_id');
		$this->alterColumn('patient_shortcode_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `period_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(128) NOT NULL,
	`display_order` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_period_lmui_fk` (`last_modified_user_id`),
	KEY `acv_period_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_period_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_period_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('period_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','period_version');

		$this->createIndex('period_aid_fk','period_version','id');

		$this->addColumn('period_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('period_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','period_version','version_id');
		$this->alterColumn('period_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `person_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`contact_id` int(10) unsigned NOT NULL,
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`source_id` int(10) unsigned DEFAULT NULL,
	`remote_id` varchar(40) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_person_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_person_created_user_id_fk` (`created_user_id`),
	KEY `acv_person_source_id_fk` (`source_id`),
	KEY `acv_person_contact_id_fk` (`contact_id`),
	CONSTRAINT `acv_person_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
	CONSTRAINT `acv_person_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_person_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_person_source_id_fk` FOREIGN KEY (`source_id`) REFERENCES `import_source` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('person_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','person_version');

		$this->createIndex('person_aid_fk','person_version','id');
		$this->addForeignKey('person_aid_fk','person_version','id','person','id');

		$this->addColumn('person_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('person_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','person_version','version_id');
		$this->alterColumn('person_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `practice_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`code` varchar(64) NOT NULL,
	`phone` varchar(64) NOT NULL,
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`contact_id` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_practice_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_practice_created_user_id_fk` (`created_user_id`),
	KEY `acv_practice_contact_id_fk` (`contact_id`),
	CONSTRAINT `acv_practice_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
	CONSTRAINT `acv_practice_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_practice_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('practice_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','practice_version');

		$this->createIndex('practice_aid_fk','practice_version','id');

		$this->addColumn('practice_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('practice_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','practice_version','version_id');
		$this->alterColumn('practice_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `previous_operation_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`patient_id` int(10) unsigned NOT NULL,
	`side_id` int(10) unsigned DEFAULT NULL,
	`operation` varchar(1024) NOT NULL,
	`date` varchar(10) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_previous_operation_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_previous_operation_created_user_id_fk` (`created_user_id`),
	KEY `acv_previous_operation_patient_id_fk` (`patient_id`),
	KEY `acv_previous_operation_side_id_fk` (`side_id`),
	CONSTRAINT `acv_previous_operation_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_previous_operation_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_previous_operation_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
	CONSTRAINT `acv_previous_operation_side_id_fk` FOREIGN KEY (`side_id`) REFERENCES `eye` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('previous_operation_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','previous_operation_version');

		$this->createIndex('previous_operation_aid_fk','previous_operation_version','id');

		$this->addColumn('previous_operation_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('previous_operation_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','previous_operation_version','version_id');
		$this->alterColumn('previous_operation_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `priority_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(10) DEFAULT NULL,
	`created_user_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL,
	`created_date` date NOT NULL DEFAULT '1900-01-01',
	`last_modified_date` date NOT NULL DEFAULT '1900-01-01',
	PRIMARY KEY (`id`),
	KEY `acv_priority_created_user_id_fk` (`created_user_id`),
	KEY `acv_priority_last_modified_user_id_fk` (`last_modified_user_id`),
	CONSTRAINT `acv_priority_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_priority_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('priority_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','priority_version');

		$this->createIndex('priority_aid_fk','priority_version','id');

		$this->addColumn('priority_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('priority_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','priority_version','version_id');
		$this->alterColumn('priority_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `proc_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`term` varchar(255) CHARACTER SET latin1 NOT NULL,
	`short_format` varchar(255) CHARACTER SET latin1 NOT NULL,
	`default_duration` smallint(5) unsigned NOT NULL,
	`snomed_code` varchar(20) NOT NULL,
	`snomed_term` varchar(255) NOT NULL DEFAULT '0',
	`aliases` text NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`unbooked` tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `acv_proc_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_proc_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_proc_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_proc_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('proc_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','proc_version');

		$this->createIndex('proc_aid_fk','proc_version','id');
		$this->addForeignKey('proc_aid_fk','proc_version','id','proc','id');

		$this->addColumn('proc_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('proc_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','proc_version','version_id');
		$this->alterColumn('proc_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `proc_opcs_assignment_version` (
	`proc_id` int(10) unsigned NOT NULL,
	`opcs_code_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`id`),
	KEY `acv_opcs_code_id` (`opcs_code_id`),
	KEY `acv_procedure_id` (`proc_id`),
	KEY `acv_proc_opcs_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_proc_opcs_assignment_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_opcs_code_fk` FOREIGN KEY (`opcs_code_id`) REFERENCES `opcs_code` (`id`),
	CONSTRAINT `acv_proc_opcs_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_proc_opcs_assignment_ibfk_1` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`),
	CONSTRAINT `acv_proc_opcs_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('proc_opcs_assignment_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','proc_opcs_assignment_version');

		$this->createIndex('proc_opcs_assignment_aid_fk','proc_opcs_assignment_version','id');

		$this->addColumn('proc_opcs_assignment_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('proc_opcs_assignment_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','proc_opcs_assignment_version','version_id');
		$this->alterColumn('proc_opcs_assignment_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `proc_subspecialty_assignment_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`proc_id` int(10) unsigned NOT NULL,
	`subspecialty_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_proc_subspecialty_assignment_proc_id_fk` (`proc_id`),
	KEY `acv_proc_subspecialty_assignment_subspecialty_id_fk` (`subspecialty_id`),
	KEY `acv_proc_subspecialty_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_proc_subspecialty_assignment_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_proc_subspecialty_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_proc_subspecialty_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_proc_subspecialty_assignment_ibfk_1` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`),
	CONSTRAINT `acv_proc_subspecialty_assignment_ibfk_2` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('proc_subspecialty_assignment_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','proc_subspecialty_assignment_version');

		$this->createIndex('proc_subspecialty_assignment_aid_fk','proc_subspecialty_assignment_version','id');

		$this->addColumn('proc_subspecialty_assignment_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('proc_subspecialty_assignment_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','proc_subspecialty_assignment_version','version_id');
		$this->alterColumn('proc_subspecialty_assignment_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `proc_subspecialty_subsection_assignment_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`proc_id` int(10) unsigned NOT NULL,
	`subspecialty_subsection_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_proc_subspecialty_subsection_assignment_proc_id_fk` (`proc_id`),
	KEY `acv_pssa_subspecialty_subsection_id_fk` (`subspecialty_subsection_id`),
	KEY `acv__subspecialty_subsection_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_proc_subspecialty_subsection_assignment_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_proc_subspecialty_subsection_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv__subspecialty_subsection_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_proc_subspecialty_subsection_assignment_proc_id_fk` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`),
	CONSTRAINT `acv_pssa_subspecialty_subsection_id_fk` FOREIGN KEY (`subspecialty_subsection_id`) REFERENCES `subspecialty_subsection` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('proc_subspecialty_subsection_assignment_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','proc_subspecialty_subsection_assignment_version');

		$this->createIndex('proc_subspecialty_subsection_assignment_aid_fk','proc_subspecialty_subsection_assignment_version','id');

		$this->addColumn('proc_subspecialty_subsection_assignment_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('proc_subspecialty_subsection_assignment_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','proc_subspecialty_subsection_assignment_version','version_id');
		$this->alterColumn('proc_subspecialty_subsection_assignment_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `procedure_additional_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`proc_id` int(10) unsigned NOT NULL,
	`additional_proc_id` int(10) unsigned NOT NULL,
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_procedure_additional_proc_id_fk` (`proc_id`),
	KEY `acv_procedure_additional_additional_proc_id_fk` (`additional_proc_id`),
	KEY `acv_procedure_additional_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_procedure_additional_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_procedure_additional_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_procedure_additional_proc_id_fk` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`),
	CONSTRAINT `acv_procedure_additional_additional_proc_id_fk` FOREIGN KEY (`additional_proc_id`) REFERENCES `proc` (`id`),
	CONSTRAINT `acv_procedure_additional_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('procedure_additional_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','procedure_additional_version');

		$this->createIndex('procedure_additional_aid_fk','procedure_additional_version','id');

		$this->addColumn('procedure_additional_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('procedure_additional_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','procedure_additional_version','version_id');
		$this->alterColumn('procedure_additional_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `procedure_benefit_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`proc_id` int(10) unsigned NOT NULL,
	`benefit_id` int(10) unsigned NOT NULL,
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_procedure_benefit_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_procedure_benefit_created_user_id_fk` (`created_user_id`),
	KEY `acv_procedure_benefit_proc_id_fk` (`proc_id`),
	KEY `acv_procedure_benefit_benefit_id_fk` (`benefit_id`),
	CONSTRAINT `acv_procedure_benefit_benefit_id_fk` FOREIGN KEY (`benefit_id`) REFERENCES `benefit` (`id`),
	CONSTRAINT `acv_procedure_benefit_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_procedure_benefit_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_procedure_benefit_proc_id_fk` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('procedure_benefit_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','procedure_benefit_version');

		$this->createIndex('procedure_benefit_aid_fk','procedure_benefit_version','id');

		$this->addColumn('procedure_benefit_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('procedure_benefit_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','procedure_benefit_version','version_id');
		$this->alterColumn('procedure_benefit_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `procedure_complication_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`proc_id` int(10) unsigned NOT NULL,
	`complication_id` int(10) unsigned NOT NULL,
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_procedure_complication_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_procedure_complication_created_user_id_fk` (`created_user_id`),
	KEY `acv_procedure_complication_proc_id_fk` (`proc_id`),
	KEY `acv_procedure_complication_complication_id_fk` (`complication_id`),
	CONSTRAINT `acv_procedure_complication_complication_id_fk` FOREIGN KEY (`complication_id`) REFERENCES `complication` (`id`),
	CONSTRAINT `acv_procedure_complication_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_procedure_complication_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_procedure_complication_proc_id_fk` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('procedure_complication_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','procedure_complication_version');

		$this->createIndex('procedure_complication_aid_fk','procedure_complication_version','id');

		$this->addColumn('procedure_complication_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('procedure_complication_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','procedure_complication_version','version_id');
		$this->alterColumn('procedure_complication_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `protected_file_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uid` varchar(64) NOT NULL,
	`name` varchar(64) NOT NULL,
	`title` varchar(64) NOT NULL,
	`description` varchar(64) NOT NULL,
	`mimetype` varchar(64) NOT NULL,
	`size` varchar(64) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_asset_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_asset_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_asset_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_asset_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('protected_file_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','protected_file_version');

		$this->createIndex('protected_file_aid_fk','protected_file_version','id');

		$this->addColumn('protected_file_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('protected_file_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','protected_file_version','version_id');
		$this->alterColumn('protected_file_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `referral_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`refno` varchar(64) NOT NULL,
	`patient_id` int(10) unsigned NOT NULL,
	`referral_type_id` int(10) unsigned NOT NULL,
	`received_date` date NOT NULL,
	`closed_date` date DEFAULT NULL,
	`referrer` varchar(32) NOT NULL,
	`firm_id` int(10) unsigned DEFAULT NULL,
	`service_subspecialty_assignment_id` int(10) unsigned DEFAULT NULL,
	`gp_id` int(10) unsigned DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_referral_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_referral_created_user_id_fk` (`created_user_id`),
	KEY `acv_referral_patient_id_fk` (`patient_id`),
	KEY `acv_referral_firm_id_fk` (`firm_id`),
	KEY `acv_referral_gp_id_fk` (`gp_id`),
	KEY `acv_referral_referral_type_id_fk` (`referral_type_id`),
	KEY `acv_referral_service_subspecialty_assignment_id_fk` (`service_subspecialty_assignment_id`),
	CONSTRAINT `acv_referral_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_referral_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_referral_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
	CONSTRAINT `acv_referral_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
	CONSTRAINT `acv_referral_referral_type_id_fk` FOREIGN KEY (`referral_type_id`) REFERENCES `referral_type` (`id`),
	CONSTRAINT `acv_referral_service_subspecialty_assignment_id_fk` FOREIGN KEY (`service_subspecialty_assignment_id`) REFERENCES `service_subspecialty_assignment` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('referral_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','referral_version');

		$this->createIndex('referral_aid_fk','referral_version','id');

		$this->addColumn('referral_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('referral_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','referral_version','version_id');
		$this->alterColumn('referral_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `referral_episode_assignment_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`referral_id` int(10) unsigned NOT NULL,
	`episode_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_referral_episode_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_referral_episode_assignment_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_referral_episode_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_referral_episode_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('referral_episode_assignment_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','referral_episode_assignment_version');

		$this->createIndex('referral_episode_assignment_aid_fk','referral_episode_assignment_version','id');

		$this->addColumn('referral_episode_assignment_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('referral_episode_assignment_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','referral_episode_assignment_version','version_id');
		$this->alterColumn('referral_episode_assignment_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `referral_type_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`code` varchar(8) NOT NULL,
	`name` varchar(64) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_referral_type_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_referral_type_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_referral_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_referral_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('referral_type_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','referral_type_version');

		$this->createIndex('referral_type_aid_fk','referral_type_version','id');
		$this->addForeignKey('referral_type_aid_fk','referral_type_version','id','referral_type','id');

		$this->addColumn('referral_type_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('referral_type_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','referral_type_version','version_id');
		$this->alterColumn('referral_type_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `secondary_diagnosis_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`disorder_id` int(10) unsigned NOT NULL,
	`eye_id` int(10) unsigned DEFAULT NULL,
	`patient_id` int(10) unsigned NOT NULL,
	`date` varchar(10) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_secondary_diagnosis_disorder_id_fk` (`disorder_id`),
	KEY `acv_secondary_diagnosis_eye_id_fk` (`eye_id`),
	KEY `acv_secondary_diagnosis_patient_id_fk` (`patient_id`),
	KEY `acv_secondary_diagnosis_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_secondary_diagnosis_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_secondary_diagnosis_disorder_id_fk` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`),
	CONSTRAINT `acv_secondary_diagnosis_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
	CONSTRAINT `acv_secondary_diagnosis_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
	CONSTRAINT `acv_secondary_diagnosis_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_secondary_diagnosis_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('secondary_diagnosis_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','secondary_diagnosis_version');

		$this->createIndex('secondary_diagnosis_aid_fk','secondary_diagnosis_version','id');

		$this->addColumn('secondary_diagnosis_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('secondary_diagnosis_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','secondary_diagnosis_version','version_id');
		$this->alterColumn('secondary_diagnosis_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `service_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(40) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_service_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_service_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_service_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_service_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('service_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','service_version');

		$this->createIndex('service_aid_fk','service_version','id');

		$this->addColumn('service_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('service_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','service_version','version_id');
		$this->alterColumn('service_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `service_subspecialty_assignment_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`service_id` int(10) unsigned NOT NULL,
	`subspecialty_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_service_id` (`service_id`),
	KEY `acv_subspecialty_id` (`subspecialty_id`),
	KEY `acv_service_subspecialty_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_service_subspecialty_assignment_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_service_subspecialty_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_service_subspecialty_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_service_subspecialty_assignment_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`),
	CONSTRAINT `acv_service_subspecialty_assignment_ibfk_2` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
		");

		$this->alterColumn('service_subspecialty_assignment_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','service_subspecialty_assignment_version');

		$this->createIndex('service_subspecialty_assignment_aid_fk','service_subspecialty_assignment_version','id');

		$this->addColumn('service_subspecialty_assignment_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('service_subspecialty_assignment_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','service_subspecialty_assignment_version','version_id');
		$this->alterColumn('service_subspecialty_assignment_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `setting_field_type_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_setting_field_type_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_setting_field_type_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_setting_field_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_setting_field_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('setting_field_type_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','setting_field_type_version');

		$this->createIndex('setting_field_type_aid_fk','setting_field_type_version','id');

		$this->addColumn('setting_field_type_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('setting_field_type_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','setting_field_type_version','version_id');
		$this->alterColumn('setting_field_type_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `setting_firm_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`firm_id` int(10) unsigned NOT NULL,
	`element_type_id` int(10) unsigned NOT NULL,
	`key` varchar(64) NOT NULL,
	`value` varchar(64) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_setting_firm_firm_id_fk` (`firm_id`),
	KEY `acv_setting_firm_element_type_id_fk` (`element_type_id`),
	KEY `acv_setting_firm_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_setting_firm_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_setting_firm_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
	CONSTRAINT `acv_setting_firm_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
	CONSTRAINT `acv_setting_firm_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_setting_firm_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('setting_firm_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','setting_firm_version');

		$this->createIndex('setting_firm_aid_fk','setting_firm_version','id');

		$this->addColumn('setting_firm_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('setting_firm_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','setting_firm_version','version_id');
		$this->alterColumn('setting_firm_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `setting_installation_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`element_type_id` int(10) unsigned NOT NULL,
	`key` varchar(64) NOT NULL,
	`value` varchar(64) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_setting_installation_element_type_id_fk` (`element_type_id`),
	KEY `acv_setting_installation_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_setting_installation_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_setting_installation_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
	CONSTRAINT `acv_setting_installation_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_setting_installation_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('setting_installation_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','setting_installation_version');

		$this->createIndex('setting_installation_aid_fk','setting_installation_version','id');

		$this->addColumn('setting_installation_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('setting_installation_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','setting_installation_version','version_id');
		$this->alterColumn('setting_installation_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `setting_institution_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`institution_id` int(10) unsigned NOT NULL,
	`element_type_id` int(10) unsigned NOT NULL,
	`key` varchar(64) NOT NULL,
	`value` varchar(64) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_setting_institution_institution_id_fk` (`institution_id`),
	KEY `acv_setting_institution_element_type_id_fk` (`element_type_id`),
	KEY `acv_setting_institution_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_setting_institution_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_setting_institution_institution_id_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`),
	CONSTRAINT `acv_setting_institution_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
	CONSTRAINT `acv_setting_institution_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_setting_institution_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('setting_institution_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','setting_institution_version');

		$this->createIndex('setting_institution_aid_fk','setting_institution_version','id');

		$this->addColumn('setting_institution_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('setting_institution_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','setting_institution_version','version_id');
		$this->alterColumn('setting_institution_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `setting_metadata_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`element_type_id` int(10) unsigned NOT NULL,
	`display_order` tinyint(3) unsigned DEFAULT '0',
	`field_type_id` int(10) unsigned NOT NULL,
	`key` varchar(64) NOT NULL,
	`name` varchar(64) NOT NULL,
	`data` varchar(4096) NOT NULL,
	`default_value` varchar(64) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_setting_metadata_element_type_id_fk` (`element_type_id`),
	KEY `acv_setting_metadata_field_type_id_fk` (`field_type_id`),
	KEY `acv_setting_metadata_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_setting_metadata_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_setting_metadata_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
	CONSTRAINT `acv_setting_metadata_field_type_id_fk` FOREIGN KEY (`field_type_id`) REFERENCES `setting_field_type` (`id`),
	CONSTRAINT `acv_setting_metadata_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_setting_metadata_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('setting_metadata_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','setting_metadata_version');

		$this->createIndex('setting_metadata_aid_fk','setting_metadata_version','id');

		$this->addColumn('setting_metadata_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('setting_metadata_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','setting_metadata_version','version_id');
		$this->alterColumn('setting_metadata_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `setting_site_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`site_id` int(10) unsigned NOT NULL,
	`element_type_id` int(10) unsigned NOT NULL,
	`key` varchar(64) NOT NULL,
	`value` varchar(64) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_setting_site_site_id_fk` (`site_id`),
	KEY `acv_setting_site_element_type_id_fk` (`element_type_id`),
	KEY `acv_setting_site_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_setting_site_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_setting_site_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
	CONSTRAINT `acv_setting_site_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
	CONSTRAINT `acv_setting_site_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_setting_site_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('setting_site_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','setting_site_version');

		$this->createIndex('setting_site_aid_fk','setting_site_version','id');

		$this->addColumn('setting_site_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('setting_site_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','setting_site_version','version_id');
		$this->alterColumn('setting_site_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `setting_specialty_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`specialty_id` int(10) unsigned NOT NULL,
	`element_type_id` int(10) unsigned NOT NULL,
	`key` varchar(64) NOT NULL,
	`value` varchar(64) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_setting_specialty_specialty_id_fk` (`specialty_id`),
	KEY `acv_setting_specialty_element_type_id_fk` (`element_type_id`),
	KEY `acv_setting_specialty_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_setting_specialty_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_setting_specialty_specialty_id_fk` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`),
	CONSTRAINT `acv_setting_specialty_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
	CONSTRAINT `acv_setting_specialty_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_setting_specialty_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('setting_specialty_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','setting_specialty_version');

		$this->createIndex('setting_specialty_aid_fk','setting_specialty_version','id');

		$this->addColumn('setting_specialty_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('setting_specialty_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','setting_specialty_version','version_id');
		$this->alterColumn('setting_specialty_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `setting_subspecialty_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`subspecialty_id` int(10) unsigned NOT NULL,
	`element_type_id` int(10) unsigned NOT NULL,
	`key` varchar(64) NOT NULL,
	`value` varchar(64) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_setting_subspecialty_subspecialty_id_fk` (`subspecialty_id`),
	KEY `acv_setting_subspecialty_element_type_id_fk` (`element_type_id`),
	KEY `acv_setting_subspecialty_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_setting_subspecialty_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_setting_subspecialty_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
	CONSTRAINT `acv_setting_subspecialty_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
	CONSTRAINT `acv_setting_subspecialty_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_setting_subspecialty_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('setting_subspecialty_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','setting_subspecialty_version');

		$this->createIndex('setting_subspecialty_aid_fk','setting_subspecialty_version','id');

		$this->addColumn('setting_subspecialty_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('setting_subspecialty_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','setting_subspecialty_version','version_id');
		$this->alterColumn('setting_subspecialty_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `setting_user_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned NOT NULL,
	`element_type_id` int(10) unsigned NOT NULL,
	`key` varchar(64) NOT NULL,
	`value` varchar(64) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_setting_user_user_id_fk` (`user_id`),
	KEY `acv_setting_user_element_type_id_fk` (`element_type_id`),
	KEY `acv_setting_user_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_setting_user_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_setting_user_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_setting_user_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
	CONSTRAINT `acv_setting_user_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_setting_user_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('setting_user_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','setting_user_version');

		$this->createIndex('setting_user_aid_fk','setting_user_version','id');

		$this->addColumn('setting_user_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('setting_user_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','setting_user_version','version_id');
		$this->alterColumn('setting_user_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `site_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`remote_id` varchar(10) NOT NULL,
	`short_name` varchar(255) NOT NULL,
	`fax` varchar(255) NOT NULL,
	`telephone` varchar(255) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`institution_id` int(10) unsigned NOT NULL DEFAULT '1',
	`location` varchar(64) NOT NULL,
	`contact_id` int(10) unsigned NOT NULL,
	`replyto_contact_id` int(10) unsigned DEFAULT NULL,
	`source_id` int(10) unsigned DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_site_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_site_created_user_id_fk` (`created_user_id`),
	KEY `acv_site_institution_id_fk` (`institution_id`),
	KEY `acv_site_contact_id_fk` (`contact_id`),
	KEY `acv_site_replyto_contact_id_fk` (`replyto_contact_id`),
	KEY `acv_site_source_id_fk` (`source_id`),
	CONSTRAINT `acv_site_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
	CONSTRAINT `acv_site_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_site_institution_id_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`),
	CONSTRAINT `acv_site_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_site_replyto_contact_id_fk` FOREIGN KEY (`replyto_contact_id`) REFERENCES `contact` (`id`),
	CONSTRAINT `acv_site_source_id_fk` FOREIGN KEY (`source_id`) REFERENCES `import_source` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('site_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','site_version');

		$this->createIndex('site_aid_fk','site_version','id');
		$this->addForeignKey('site_aid_fk','site_version','id','site','id');

		$this->addColumn('site_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('site_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','site_version','version_id');
		$this->alterColumn('site_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `site_consultant_assignment_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`site_id` int(10) unsigned NOT NULL,
	`consultant_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_site_consultant_assignment_site_id_fk` (`site_id`),
	KEY `acv_site_consultant_assignment_consultant_id_fk` (`consultant_id`),
	KEY `acv_site_consultant_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_site_consultant_assignment_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_site_consultant_assignment_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
	CONSTRAINT `acv_site_consultant_assignment_consultant_id_fk` FOREIGN KEY (`consultant_id`) REFERENCES `consultant` (`id`),
	CONSTRAINT `acv_site_consultant_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_site_consultant_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('site_consultant_assignment_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','site_consultant_assignment_version');

		$this->createIndex('site_consultant_assignment_aid_fk','site_consultant_assignment_version','id');

		$this->addColumn('site_consultant_assignment_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('site_consultant_assignment_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','site_consultant_assignment_version','version_id');
		$this->alterColumn('site_consultant_assignment_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `site_subspecialty_anaesthetic_agent_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`site_id` int(10) unsigned NOT NULL,
	`subspecialty_id` int(10) unsigned NOT NULL,
	`anaesthetic_agent_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_site_subspecialty_anaesthetic_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_site_subspecialty_anaesthetic_created_user_id_fk` (`created_user_id`),
	KEY `acv_site_subspecialty_anaesthetic_site_id` (`site_id`),
	KEY `acv_site_subspecialty_anaesthetic_subspecialty_id` (`subspecialty_id`),
	KEY `acv_site_subspecialty_anaesthetic_anaesthetic_agent_id` (`anaesthetic_agent_id`),
	CONSTRAINT `acv_site_subspecialty_anaesthetic_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_site_subspecialty_anaesthetic_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_site_subspecialty_anaesthetic_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
	CONSTRAINT `acv_site_subspecialty_anaesthetic_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
	CONSTRAINT `acv_site_subspecialty_anaesthetic_anaesthetic_agent_id_fk` FOREIGN KEY (`anaesthetic_agent_id`) REFERENCES `anaesthetic_agent` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('site_subspecialty_anaesthetic_agent_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','site_subspecialty_anaesthetic_agent_version');

		$this->createIndex('site_subspecialty_anaesthetic_agent_aid_fk','site_subspecialty_anaesthetic_agent_version','id');

		$this->addColumn('site_subspecialty_anaesthetic_agent_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('site_subspecialty_anaesthetic_agent_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','site_subspecialty_anaesthetic_agent_version','version_id');
		$this->alterColumn('site_subspecialty_anaesthetic_agent_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `site_subspecialty_anaesthetic_agent_default_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`site_id` int(10) unsigned NOT NULL,
	`subspecialty_id` int(10) unsigned NOT NULL,
	`anaesthetic_agent_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_site_subspecialty_anaesthetic_def_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_site_subspecialty_anaesthetic_def_created_user_id_fk` (`created_user_id`),
	KEY `acv_site_subspecialty_anaesthetic_def_site_id` (`site_id`),
	KEY `acv_site_subspecialty_anaesthetic_def_subspecialty_id` (`subspecialty_id`),
	KEY `acv_site_subspecialty_anaesthetic_def_anaesthetic_agent_id` (`anaesthetic_agent_id`),
	CONSTRAINT `acv_site_subspecialty_anaesthetic_def_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_site_subspecialty_anaesthetic_def_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_site_subspecialty_anaesthetic_def_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
	CONSTRAINT `acv_site_subspecialty_anaesthetic_def_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
	CONSTRAINT `acv_site_subspecialty_anaesthetic_def_anaesthetic_agent_id_fk` FOREIGN KEY (`anaesthetic_agent_id`) REFERENCES `anaesthetic_agent` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('site_subspecialty_anaesthetic_agent_default_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','site_subspecialty_anaesthetic_agent_default_version');

		$this->createIndex('site_subspecialty_anaesthetic_agent_default_aid_fk','site_subspecialty_anaesthetic_agent_default_version','id');

		$this->addColumn('site_subspecialty_anaesthetic_agent_default_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('site_subspecialty_anaesthetic_agent_default_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','site_subspecialty_anaesthetic_agent_default_version','version_id');
		$this->alterColumn('site_subspecialty_anaesthetic_agent_default_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `site_subspecialty_drug_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`site_id` int(10) unsigned NOT NULL,
	`subspecialty_id` int(10) unsigned NOT NULL,
	`drug_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_site_subspecialty_drug_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_site_subspecialty_drug_created_user_id_fk` (`created_user_id`),
	KEY `acv_site_subspecialty_drug_site_id` (`site_id`),
	KEY `acv_site_subspecialty_drug_subspecialty_id` (`subspecialty_id`),
	KEY `acv_site_subspecialty_drug_drug_id` (`drug_id`),
	CONSTRAINT `acv_site_subspecialty_drug_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_site_subspecialty_drug_drug_id_fk` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`id`),
	CONSTRAINT `acv_site_subspecialty_drug_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_site_subspecialty_drug_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
	CONSTRAINT `acv_site_subspecialty_drug_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('site_subspecialty_drug_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','site_subspecialty_drug_version');

		$this->createIndex('site_subspecialty_drug_aid_fk','site_subspecialty_drug_version','id');

		$this->addColumn('site_subspecialty_drug_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('site_subspecialty_drug_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','site_subspecialty_drug_version','version_id');
		$this->alterColumn('site_subspecialty_drug_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `site_subspecialty_operative_device_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`site_id` int(10) unsigned NOT NULL,
	`subspecialty_id` int(10) unsigned NOT NULL,
	`operative_device_id` int(10) unsigned NOT NULL,
	`display_order` tinyint(3) unsigned NOT NULL,
	`default` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_ss_operative_device_site_id_fk` (`site_id`),
	KEY `acv_ss_operative_device_subspecialty_id_fk` (`subspecialty_id`),
	KEY `acv_ss_operative_device_operative_device_id` (`operative_device_id`),
	KEY `acv_ss_operative_device_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_ss_operative_device_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_ss_operative_device_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
	CONSTRAINT `acv_ss_operative_device_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
	CONSTRAINT `acv_ss_operative_device_operative_device_id_fk` FOREIGN KEY (`operative_device_id`) REFERENCES `operative_device` (`id`),
	CONSTRAINT `acv_ss_operative_device_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ss_operative_device_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('site_subspecialty_operative_device_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','site_subspecialty_operative_device_version');

		$this->createIndex('site_subspecialty_operative_device_aid_fk','site_subspecialty_operative_device_version','id');

		$this->addColumn('site_subspecialty_operative_device_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('site_subspecialty_operative_device_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','site_subspecialty_operative_device_version','version_id');
		$this->alterColumn('site_subspecialty_operative_device_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `specialty_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`code` int(10) unsigned NOT NULL,
	`specialty_type_id` int(10) unsigned NOT NULL,
	`default_title` varchar(64) NOT NULL,
	`default_is_surgeon` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`adjective` varchar(64) NOT NULL,
	`abbreviation` char(3) NOT NULL,
	`created_user_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL,
	`created_date` date NOT NULL DEFAULT '1900-01-01',
	`last_modified_date` date NOT NULL DEFAULT '1900-01-01',
	PRIMARY KEY (`id`),
	KEY `acv_specialty_specialty_type_id_fk` (`specialty_type_id`),
	KEY `acv_specialty_created_user_id_fk` (`created_user_id`),
	KEY `acv_specialty_last_modified_user_id_fk` (`last_modified_user_id`),
	CONSTRAINT `acv_specialty_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_specialty_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_specialty_specialty_type_id_fk` FOREIGN KEY (`specialty_type_id`) REFERENCES `specialty_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('specialty_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','specialty_version');

		$this->createIndex('specialty_aid_fk','specialty_version','id');

		$this->addColumn('specialty_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('specialty_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','specialty_version','version_id');
		$this->alterColumn('specialty_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `specialty_type_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`display_order` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_specialty_type_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_specialty_type_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_specialty_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_specialty_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('specialty_type_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','specialty_type_version');

		$this->createIndex('specialty_type_aid_fk','specialty_type_version','id');
		$this->addForeignKey('specialty_type_aid_fk','specialty_type_version','id','specialty_type','id');

		$this->addColumn('specialty_type_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('specialty_type_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','specialty_type_version','version_id');
		$this->alterColumn('specialty_type_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `subspecialty_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(40) NOT NULL,
	`ref_spec` varchar(3) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`specialty_id` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_subspecialty_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_subspecialty_created_user_id_fk` (`created_user_id`),
	KEY `acv_subspecialty_specialty_id_fk` (`specialty_id`),
	CONSTRAINT `acv_subspecialty_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_subspecialty_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_subspecialty_specialty_id_fk` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('subspecialty_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','subspecialty_version');

		$this->createIndex('subspecialty_aid_fk','subspecialty_version','id');

		$this->addColumn('subspecialty_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('subspecialty_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','subspecialty_version','version_id');
		$this->alterColumn('subspecialty_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `subspecialty_subsection_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`subspecialty_id` int(10) unsigned NOT NULL,
	`name` varchar(255) CHARACTER SET latin1 NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_subspecialty_subsection_subspecialty_id_fk` (`subspecialty_id`),
	KEY `acv_subspecialty_subsection_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_subspecialty_subsection_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_subspecialty_subsection_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_subspecialty_subsection_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_subspecialty_subsection_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('subspecialty_subsection_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','subspecialty_subsection_version');

		$this->createIndex('subspecialty_subsection_aid_fk','subspecialty_subsection_version','id');
		$this->addForeignKey('subspecialty_subsection_aid_fk','subspecialty_subsection_version','id','subspecialty_subsection','id');

		$this->addColumn('subspecialty_subsection_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('subspecialty_subsection_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','subspecialty_subsection_version','version_id');
		$this->alterColumn('subspecialty_subsection_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `user_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`username` varchar(40) CHARACTER SET utf8 NOT NULL,
	`first_name` varchar(40) CHARACTER SET utf8 NOT NULL,
	`last_name` varchar(40) CHARACTER SET utf8 NOT NULL,
	`email` varchar(80) CHARACTER SET utf8 NOT NULL,
	`active` tinyint(1) NOT NULL,
	`global_firm_rights` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`title` varchar(40) NOT NULL,
	`qualifications` varchar(255) NOT NULL,
	`role` varchar(255) NOT NULL,
	`code` varchar(255) DEFAULT NULL,
	`password` varchar(40) DEFAULT NULL,
	`salt` varchar(10) DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`last_firm_id` int(11) unsigned DEFAULT NULL,
	`is_doctor` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`access_level` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`contact_id` int(10) unsigned DEFAULT NULL,
	`last_site_id` int(10) unsigned DEFAULT NULL,
	`is_clinical` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`is_consultant` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`is_surgeon` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`has_selected_firms` tinyint(1) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_user_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_user_created_user_id_fk` (`created_user_id`),
	KEY `acv_user_last_firm_id_fk` (`last_firm_id`),
	KEY `acv_user_contact_id_fk` (`contact_id`),
	KEY `acv_user_last_site_id_fk` (`last_site_id`),
	CONSTRAINT `acv_user_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
	CONSTRAINT `acv_user_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_user_last_firm_id_fk` FOREIGN KEY (`last_firm_id`) REFERENCES `firm` (`id`),
	CONSTRAINT `acv_user_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_user_last_site_id_fk` FOREIGN KEY (`last_site_id`) REFERENCES `site` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('user_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','user_version');

		$this->createIndex('user_aid_fk','user_version','id');
		$this->addForeignKey('user_aid_fk','user_version','id','user','id');

		$this->addColumn('user_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('user_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','user_version','version_id');
		$this->alterColumn('user_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `user_firm_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned NOT NULL,
	`firm_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_user_firm_user_id_fk` (`user_id`),
	KEY `acv_user_firm_firm_id_fk` (`firm_id`),
	KEY `acv_user_firm_lmui_fk` (`last_modified_user_id`),
	KEY `acv_user_firm_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_user_firm_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_user_firm_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
	CONSTRAINT `acv_user_firm_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_user_firm_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('user_firm_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','user_firm_version');

		$this->createIndex('user_firm_aid_fk','user_firm_version','id');

		$this->addColumn('user_firm_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('user_firm_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','user_firm_version','version_id');
		$this->alterColumn('user_firm_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `user_firm_preference_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned NOT NULL,
	`firm_id` int(10) unsigned NOT NULL,
	`position` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_user_firm_preference_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_user_firm_preference_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_user_firm_preference_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_user_firm_preference_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('user_firm_preference_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','user_firm_preference_version');

		$this->createIndex('user_firm_preference_aid_fk','user_firm_preference_version','id');

		$this->addColumn('user_firm_preference_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('user_firm_preference_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','user_firm_preference_version','version_id');
		$this->alterColumn('user_firm_preference_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `user_firm_rights_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned NOT NULL,
	`firm_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_user_firm_rights_fk_1` (`user_id`),
	KEY `acv_user_firm_rights_fk_2` (`firm_id`),
	KEY `acv_user_firm_rights_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_user_firm_rights_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_user_firm_rights_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_user_firm_rights_fk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_user_firm_rights_fk_2` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
	CONSTRAINT `acv_user_firm_rights_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('user_firm_rights_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','user_firm_rights_version');

		$this->createIndex('user_firm_rights_aid_fk','user_firm_rights_version','id');

		$this->addColumn('user_firm_rights_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('user_firm_rights_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','user_firm_rights_version','version_id');
		$this->alterColumn('user_firm_rights_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `user_service_rights_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned NOT NULL,
	`service_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_user_service_rights_fk_1` (`user_id`),
	KEY `acv_user_service_rights_fk_2` (`service_id`),
	KEY `acv_user_service_rights_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_user_service_rights_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_user_service_rights_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_user_service_rights_fk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_user_service_rights_fk_2` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`),
	CONSTRAINT `acv_user_service_rights_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('user_service_rights_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','user_service_rights_version');

		$this->createIndex('user_service_rights_aid_fk','user_service_rights_version','id');

		$this->addColumn('user_service_rights_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('user_service_rights_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','user_service_rights_version','version_id');
		$this->alterColumn('user_service_rights_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `user_site_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned NOT NULL,
	`site_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_user_site_user_id_fk` (`user_id`),
	KEY `acv_user_site_site_id_fk` (`site_id`),
	KEY `acv_user_site_lmui_fk` (`last_modified_user_id`),
	KEY `acv_user_site_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_user_site_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_user_site_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
	CONSTRAINT `acv_user_site_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_user_site_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('user_site_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','user_site_version');

		$this->createIndex('user_site_aid_fk','user_site_version','id');

		$this->addColumn('user_site_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('user_site_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','user_site_version','version_id');
		$this->alterColumn('user_site_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$null_ids = array();

		$limit = 10000;
		$offset = 0;

		while (1) {
			$data = $this->dbConnection->createCommand()->select("id,data")->from("audit")->where("data is not null and data != :blank",array(":blank" => ""))->order("id asc")->limit($limit)->offset($offset)->queryAll();

			if (empty($data)) break;

			foreach ($data as $row) {
				if (@unserialize($row['data'])) {
					$null_ids[] = $row['id'];

					if (count($null_ids) >= 1000) {
						$this->resetData($null_ids);
						$null_ids = array();
					}
				}
			}

			$offset += $limit;
		}

		if (!empty($null_ids)) {
			$this->resetData($null_ids);
		}

		$this->update('audit',array('data' => null),"data = ''");

		$this->addColumn('address_type','deleted','tinyint(1) unsigned not null');
		$this->addColumn('address_type_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('allergy','deleted','tinyint(1) unsigned not null');
		$this->addColumn('allergy_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('anaesthetic_agent','deleted','tinyint(1) unsigned not null');
		$this->addColumn('anaesthetic_agent_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('anaesthetic_complication','deleted','tinyint(1) unsigned not null');
		$this->addColumn('anaesthetic_complication_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('anaesthetic_delivery','deleted','tinyint(1) unsigned not null');
		$this->addColumn('anaesthetic_delivery_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('anaesthetic_type','deleted','tinyint(1) unsigned not null');
		$this->addColumn('anaesthetic_type_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('anaesthetist','deleted','tinyint(1) unsigned not null');
		$this->addColumn('anaesthetist_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('benefit','deleted','tinyint(1) unsigned not null');
		$this->addColumn('benefit_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('commissioning_body','deleted','tinyint(1) unsigned not null');
		$this->addColumn('commissioning_body_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('commissioning_body_service','deleted','tinyint(1) unsigned not null');
		$this->addColumn('commissioning_body_service_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('commissioning_body_service_type','deleted','tinyint(1) unsigned not null');
		$this->addColumn('commissioning_body_service_type_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('commissioning_body_type','deleted','tinyint(1) unsigned not null');
		$this->addColumn('commissioning_body_type_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('complication','deleted','tinyint(1) unsigned not null');
		$this->addColumn('complication_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('consultant','deleted','tinyint(1) unsigned not null');
		$this->addColumn('consultant_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('contact','deleted','tinyint(1) unsigned not null');
		$this->addColumn('contact_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('contact_label','deleted','tinyint(1) unsigned not null');
		$this->addColumn('contact_label_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('contact_type','deleted','tinyint(1) unsigned not null');
		$this->addColumn('contact_type_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('country','deleted','tinyint(1) unsigned not null');
		$this->addColumn('country_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('disorder','deleted','tinyint(1) unsigned not null');
		$this->addColumn('disorder_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('drug','deleted','tinyint(1) unsigned not null');
		$this->addColumn('drug_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('drug_duration','deleted','tinyint(1) unsigned not null');
		$this->addColumn('drug_duration_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('drug_form','deleted','tinyint(1) unsigned not null');
		$this->addColumn('drug_form_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('drug_frequency','deleted','tinyint(1) unsigned not null');
		$this->addColumn('drug_frequency_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('drug_route','deleted','tinyint(1) unsigned not null');
		$this->addColumn('drug_route_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('drug_route_option','deleted','tinyint(1) unsigned not null');
		$this->addColumn('drug_route_option_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('drug_set','deleted','tinyint(1) unsigned not null');
		$this->addColumn('drug_set_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('drug_type','deleted','tinyint(1) unsigned not null');
		$this->addColumn('drug_type_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('family_history_condition','deleted','tinyint(1) unsigned not null');
		$this->addColumn('family_history_condition_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('family_history_relative','deleted','tinyint(1) unsigned not null');
		$this->addColumn('family_history_relative_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('family_history_side','deleted','tinyint(1) unsigned not null');
		$this->addColumn('family_history_side_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('firm','deleted','tinyint(1) unsigned not null');
		$this->addColumn('firm_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('institution','deleted','tinyint(1) unsigned not null');
		$this->addColumn('institution_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('issue','deleted','tinyint(1) unsigned not null');
		$this->addColumn('issue_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('medication','deleted','tinyint(1) unsigned not null');
		$this->addColumn('medication_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('nsc_grade','deleted','tinyint(1) unsigned not null');
		$this->addColumn('nsc_grade_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('opcs_code','deleted','tinyint(1) unsigned not null');
		$this->addColumn('opcs_code_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('operative_device','deleted','tinyint(1) unsigned not null');
		$this->addColumn('operative_device_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('patient_oph_info_cvi_status','deleted','tinyint(1) unsigned not null');
		$this->addColumn('patient_oph_info_cvi_status_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('person','deleted','tinyint(1) unsigned not null');
		$this->addColumn('person_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('proc','deleted','tinyint(1) unsigned not null');
		$this->addColumn('proc_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('referral_type','deleted','tinyint(1) unsigned not null');
		$this->addColumn('referral_type_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('site','deleted','tinyint(1) unsigned not null');
		$this->addColumn('site_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('specialty_type','deleted','tinyint(1) unsigned not null');
		$this->addColumn('specialty_type_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('subspecialty_subsection','deleted','tinyint(1) unsigned not null');
		$this->addColumn('subspecialty_subsection_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('user','deleted','tinyint(1) unsigned not null');
		$this->addColumn('user_version','deleted','tinyint(1) unsigned not null');
	}

	public function resetData($null_ids)
	{
		$this->update('audit',array('data' => null),"id in (".implode(",",$null_ids).")");
	}

	public function down()
	{
		$this->dropColumn('address_type','deleted');
		$this->dropColumn('address_type_version','deleted');
		$this->dropColumn('allergy','deleted');
		$this->dropColumn('allergy_version','deleted');
		$this->dropColumn('anaesthetic_agent','deleted');
		$this->dropColumn('anaesthetic_agent_version','deleted');
		$this->dropColumn('anaesthetic_complication','deleted');
		$this->dropColumn('anaesthetic_complication_version','deleted');
		$this->dropColumn('anaesthetic_delivery','deleted');
		$this->dropColumn('anaesthetic_delivery_version','deleted');
		$this->dropColumn('anaesthetic_type','deleted');
		$this->dropColumn('anaesthetic_type_version','deleted');
		$this->dropColumn('anaesthetist','deleted');
		$this->dropColumn('anaesthetist_version','deleted');
		$this->dropColumn('benefit','deleted');
		$this->dropColumn('benefit_version','deleted');
		$this->dropColumn('commissioning_body','deleted');
		$this->dropColumn('commissioning_body_version','deleted');
		$this->dropColumn('commissioning_body_service','deleted');
		$this->dropColumn('commissioning_body_service_version','deleted');
		$this->dropColumn('commissioning_body_service_type','deleted');
		$this->dropColumn('commissioning_body_service_type_version','deleted');
		$this->dropColumn('commissioning_body_type','deleted');
		$this->dropColumn('commissioning_body_type_version','deleted');
		$this->dropColumn('complication','deleted');
		$this->dropColumn('complication_version','deleted');
		$this->dropColumn('consultant','deleted');
		$this->dropColumn('consultant_version','deleted');
		$this->dropColumn('contact','deleted');
		$this->dropColumn('contact_version','deleted');
		$this->dropColumn('contact_label','deleted');
		$this->dropColumn('contact_label_version','deleted');
		$this->dropColumn('contact_type','deleted');
		$this->dropColumn('contact_type_version','deleted');
		$this->dropColumn('country','deleted');
		$this->dropColumn('country_version','deleted');
		$this->dropColumn('disorder','deleted');
		$this->dropColumn('disorder_version','deleted');
		$this->dropColumn('drug','deleted');
		$this->dropColumn('drug_version','deleted');
		$this->dropColumn('drug_duration','deleted');
		$this->dropColumn('drug_duration_version','deleted');
		$this->dropColumn('drug_form','deleted');
		$this->dropColumn('drug_form_version','deleted');
		$this->dropColumn('drug_frequency','deleted');
		$this->dropColumn('drug_frequency_version','deleted');
		$this->dropColumn('drug_route','deleted');
		$this->dropColumn('drug_route_version','deleted');
		$this->dropColumn('drug_route_option','deleted');
		$this->dropColumn('drug_route_option_version','deleted');
		$this->dropColumn('drug_set','deleted');
		$this->dropColumn('drug_set_version','deleted');
		$this->dropColumn('drug_type','deleted');
		$this->dropColumn('drug_type_version','deleted');
		$this->dropColumn('family_history_condition','deleted');
		$this->dropColumn('family_history_condition_version','deleted');
		$this->dropColumn('family_history_relative','deleted');
		$this->dropColumn('family_history_relative_version','deleted');
		$this->dropColumn('family_history_side','deleted');
		$this->dropColumn('family_history_side_version','deleted');
		$this->dropColumn('firm','deleted');
		$this->dropColumn('firm_version','deleted');
		$this->dropColumn('institution','deleted');
		$this->dropColumn('institution_version','deleted');
		$this->dropColumn('issue','deleted');
		$this->dropColumn('medication','deleted');
		$this->dropColumn('medication_version','deleted');
		$this->dropColumn('nsc_grade','deleted');
		$this->dropColumn('nsc_grade_version','deleted');
		$this->dropColumn('opcs_code','deleted');
		$this->dropColumn('opcs_code_version','deleted');
		$this->dropColumn('operative_device','deleted');
		$this->dropColumn('operative_device_version','deleted');
		$this->dropColumn('patient_oph_info_cvi_status','deleted');
		$this->dropColumn('patient_oph_info_cvi_status_version','deleted');
		$this->dropColumn('person','deleted');
		$this->dropColumn('person_version','deleted');
		$this->dropColumn('proc','deleted');
		$this->dropColumn('proc_version','deleted');
		$this->dropColumn('referral_type','deleted');
		$this->dropColumn('referral_type_version','deleted');
		$this->dropColumn('site','deleted');
		$this->dropColumn('site_version','deleted');
		$this->dropColumn('specialty_type','deleted');
		$this->dropColumn('specialty_type_version','deleted');
		$this->dropColumn('subspecialty_subsection','deleted');
		$this->dropColumn('subspecialty_subsection_version','deleted');
		$this->dropColumn('user','deleted');
		$this->dropColumn('user_version','deleted');

		$this->dropTable('address_version');
		$this->dropTable('address_type_version');
		$this->dropTable('allergy_version');
		$this->dropTable('anaesthetic_agent_version');
		$this->dropTable('anaesthetic_complication_version');
		$this->dropTable('anaesthetic_delivery_version');
		$this->dropTable('anaesthetic_type_version');
		$this->dropTable('anaesthetist_version');
		$this->dropTable('benefit_version');
		$this->dropTable('commissioning_body_version');
		$this->dropTable('commissioning_body_patient_assignment_version');
		$this->dropTable('commissioning_body_practice_assignment_version');
		$this->dropTable('commissioning_body_service_version');
		$this->dropTable('commissioning_body_service_type_version');
		$this->dropTable('commissioning_body_type_version');
		$this->dropTable('common_ophthalmic_disorder_version');
		$this->dropTable('common_previous_operation_version');
		$this->dropTable('common_systemic_disorder_version');
		$this->dropTable('complication_version');
		$this->dropTable('consultant_version');
		$this->dropTable('contact_version');
		$this->dropTable('contact_label_version');
		$this->dropTable('contact_location_version');
		$this->dropTable('contact_metadata_version');
		$this->dropTable('contact_type_version');
		$this->dropTable('country_version');
		$this->dropTable('disorder_version');
		$this->dropTable('disorder_tree_version');
		$this->dropTable('drug_version');
		$this->dropTable('drug_allergy_assignment_version');
		$this->dropTable('drug_duration_version');
		$this->dropTable('drug_form_version');
		$this->dropTable('drug_frequency_version');
		$this->dropTable('drug_route_version');
		$this->dropTable('drug_route_option_version');
		$this->dropTable('drug_set_version');
		$this->dropTable('drug_set_item_version');
		$this->dropTable('drug_set_item_taper_version');
		$this->dropTable('drug_type_version');
		$this->dropTable('element_type_version');
		$this->dropTable('element_type_anaesthetic_agent_version');
		$this->dropTable('element_type_anaesthetic_complication_version');
		$this->dropTable('element_type_anaesthetic_delivery_version');
		$this->dropTable('element_type_anaesthetic_type_version');
		$this->dropTable('element_type_anaesthetist_version');
		$this->dropTable('element_type_eye_version');
		$this->dropTable('element_type_priority_version');
		$this->dropTable('episode_version');
		$this->dropTable('episode_status_version');
		$this->dropTable('ethnic_group_version');
		$this->dropTable('event_version');
		$this->dropTable('event_group_version');
		$this->dropTable('event_issue_version');
		$this->dropTable('event_type_version');
		$this->dropTable('family_history_version');
		$this->dropTable('family_history_condition_version');
		$this->dropTable('family_history_relative_version');
		$this->dropTable('family_history_side_version');
		$this->dropTable('firm_version');
		$this->dropTable('firm_user_assignment_version');
		$this->dropTable('gp_version');
		$this->dropTable('institution_version');
		$this->dropTable('institution_consultant_assignment_version');
		$this->dropTable('issue_version');
		$this->dropTable('language_version');
		$this->dropTable('manual_contact_version');
		$this->dropTable('medication_version');
		$this->dropTable('nsc_grade_version');
		$this->dropTable('opcs_code_version');
		$this->dropTable('operative_device_version');
		$this->dropTable('patient_version');
		$this->dropTable('patient_allergy_assignment_version');
		$this->dropTable('patient_contact_assignment_version');
		$this->dropTable('patient_oph_info_version');
		$this->dropTable('patient_oph_info_cvi_status_version');
		$this->dropTable('patient_shortcode_version');
		$this->dropTable('period_version');
		$this->dropTable('person_version');
		$this->dropTable('practice_version');
		$this->dropTable('previous_operation_version');
		$this->dropTable('priority_version');
		$this->dropTable('proc_version');
		$this->dropTable('proc_opcs_assignment_version');
		$this->dropTable('proc_subspecialty_assignment_version');
		$this->dropTable('proc_subspecialty_subsection_assignment_version');
		$this->dropTable('procedure_additional_version');
		$this->dropTable('procedure_benefit_version');
		$this->dropTable('procedure_complication_version');
		$this->dropTable('protected_file_version');
		$this->dropTable('referral_version');
		$this->dropTable('referral_episode_assignment_version');
		$this->dropTable('referral_type_version');
		$this->dropTable('secondary_diagnosis_version');
		$this->dropTable('service_version');
		$this->dropTable('service_subspecialty_assignment_version');
		$this->dropTable('setting_field_type_version');
		$this->dropTable('setting_firm_version');
		$this->dropTable('setting_installation_version');
		$this->dropTable('setting_institution_version');
		$this->dropTable('setting_metadata_version');
		$this->dropTable('setting_site_version');
		$this->dropTable('setting_specialty_version');
		$this->dropTable('setting_subspecialty_version');
		$this->dropTable('setting_user_version');
		$this->dropTable('site_version');
		$this->dropTable('site_consultant_assignment_version');
		$this->dropTable('site_subspecialty_anaesthetic_agent_version');
		$this->dropTable('site_subspecialty_anaesthetic_agent_default_version');
		$this->dropTable('site_subspecialty_drug_version');
		$this->dropTable('site_subspecialty_operative_device_version');
		$this->dropTable('specialty_version');
		$this->dropTable('specialty_type_version');
		$this->dropTable('subspecialty_version');
		$this->dropTable('subspecialty_subsection_version');
		$this->dropTable('user_version');
		$this->dropTable('user_firm_version');
		$this->dropTable('user_firm_preference_version');
		$this->dropTable('user_firm_rights_version');
		$this->dropTable('user_service_rights_version');
		$this->dropTable('user_site_version');

		$this->alterColumn('disorder_tree','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','disorder_tree');
		
		$this->dropColumn('disorder_tree','id');

		$this->renameColumn('disorder_tree','disorder_id','id');
	}
}
