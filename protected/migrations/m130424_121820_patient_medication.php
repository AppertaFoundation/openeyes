<?php

class m130424_121820_patient_medication extends CDbMigration
{
	public function up()
	{
		$this->createTable('medication', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'patient_id' => 'int(10) unsigned NOT NULL',
				'drug_id' => 'int(10) unsigned NOT NULL',
				'route_id' => 'int(10) unsigned NOT NULL',
				'option_id' => 'int(10) unsigned NULL',
				'frequency_id' => 'int(10) unsigned NOT NULL',
				'start_date' => 'date NOT NULL',
				'end_date' => 'date NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `medication_lmui_fk` (`last_modified_user_id`)',
				'KEY `medication_cui_fk` (`created_user_id`)',
				'KEY `medication_drug_id_fk` (`drug_id`)',
				'KEY `medication_route_id_fk` (`route_id`)',
				'KEY `medication_option_id_fk` (`option_id`)',
				'KEY `medication_frequency_id_fk` (`frequency_id`)',
				'CONSTRAINT `medication_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `medication_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `medication_drug_id_fk` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`id`)',
				'CONSTRAINT `medication_route_id_fk` FOREIGN KEY (`route_id`) REFERENCES `drug_route` (`id`)',
				'CONSTRAINT `medication_option_id_fk` FOREIGN KEY (`option_id`) REFERENCES `drug_route_option` (`id`)',
				'CONSTRAINT `medication_frequency_id_fk` FOREIGN KEY (`frequency_id`) REFERENCES `drug_frequency` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
	}

	public function down()
	{
		$this->dropTable('medication');
	}
}
