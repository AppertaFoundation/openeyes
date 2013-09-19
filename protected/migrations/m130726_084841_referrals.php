<?php

class m130726_084841_referrals extends CDbMigration
{
	public function up()
	{
		$this->dropTable('referral');

		$this->createTable('referral_type', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'code' => 'varchar(8) COLLATE utf8_bin NOT NULL',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `referral_type_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `referral_type_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `referral_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `referral_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('referral', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'refno' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'patient_id' => 'int(10) unsigned NOT NULL',
				'referral_type_id' => 'int(10) unsigned NOT NULL',
				'received_date' => 'date NOT NULL',
				'closed_date' => 'date NULL',
				'referrer' => 'varchar(32) COLLATE utf8_bin NOT NULL',
				'firm_id' => 'int(10) unsigned NULL',
				'service_subspecialty_assignment_id' => 'int(10) unsigned NULL',
				'gp_id' => 'int(10) unsigned NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `referral_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `referral_created_user_id_fk` (`created_user_id`)',
				'KEY `referral_patient_id_fk` (`patient_id`)',
				'KEY `referral_firm_id_fk` (`firm_id`)',
				'KEY `referral_gp_id_fk` (`gp_id`)',
				'KEY `referral_referral_type_id_fk` (`referral_type_id`)',
				'KEY `referral_service_subspecialty_assignment_id_fk` (`service_subspecialty_assignment_id`)',
				'CONSTRAINT `referral_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `referral_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `referral_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)',
				'CONSTRAINT `referral_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
				'CONSTRAINT `referral_gp_id_fk` FOREIGN KEY (`gp_id`) REFERENCES `gp` (`id`)',
				'CONSTRAINT `referral_referral_type_id_fk` FOREIGN KEY (`referral_type_id`) REFERENCES `referral_type` (`id`)',
				'CONSTRAINT `referral_service_subspecialty_assignment_id_fk` FOREIGN KEY (`service_subspecialty_assignment_id`) REFERENCES `service_subspecialty_assignment` (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
	}

	public function down()
	{
		$this->dropTable('referral');
		$this->dropTable('referral_type');

		$this->createTable('referral', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'refno' => 'int(10) unsigned NOT NULL',
				'patient_id' => 'int(10) unsigned NOT NULL',
				'closed' => 'tinyint(1) DEFAULT 0',
				'service_subspecialty_assignment_id' => 'int(10) unsigned NOT NULL',
				'firm_id' => 'int(10) unsigned DEFAULT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `firm_fk` (`firm_id`)',
				'KEY `referral_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `referral_created_user_id_fk` (`created_user_id`)',
				'KEY `referral_service_subspecialty_assignment_id_fk` (`service_subspecialty_assignment_id`)',
				'CONSTRAINT `firm_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
				'CONSTRAINT `referral_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `referral_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `referral_service_subspecialty_assignment_id_fk` FOREIGN KEY (`service_subspecialty_assignment_id`) REFERENCES `service_subspecialty_assignment` (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
	}
}
