<?php

class m121114_110229_patient_oph_info extends CDbMigration
{
	public function up()
	{
		$this->createTable('patient_oph_info_cvi_status', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(128) COLLATE utf8_bin NOT NULL',
				'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `patient_oph_info_cvi_status_lmui_fk` (`last_modified_user_id`)',
				'KEY `patient_oph_info_cvi_status_cui_fk` (`created_user_id`)',
				'CONSTRAINT `patient_oph_info_cvi_status_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `patient_oph_info_cvi_status_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->insert('patient_oph_info_cvi_status',array('name'=>'Unknown','display_order'=>1));
		$this->insert('patient_oph_info_cvi_status',array('name'=>'Not Certified','display_order'=>2));
		$this->insert('patient_oph_info_cvi_status',array('name'=>'Sight Impaired','display_order'=>3));
		$this->insert('patient_oph_info_cvi_status',array('name'=>'Severely Sight Impaired','display_order'=>4));

		$this->createTable('patient_oph_info',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'patient_id' => 'int(10) unsigned NOT NULL',
				'cvi_status_date' => 'varchar(10) NOT NULL',
				'cvi_status_id' => 'int(10) unsigned NOT NULL',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `patient_oph_info_patient_id_fk` (`patient_id`)',
				'KEY `patient_oph_info_cvi_status_id_fk` (`cvi_status_id`)',
				'KEY `patient_oph_info_lmui_fk` (`last_modified_user_id`)',
				'KEY `patient_oph_info_cui_fk` (`created_user_id`)',
				'CONSTRAINT `patient_oph_info_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)',
				'CONSTRAINT `patient_oph_info_cvi_status_id_fk` FOREIGN KEY (`cvi_status_id`) REFERENCES `patient_oph_info_cvi_status` (`id`)',
				'CONSTRAINT `patient_oph_info_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `patient_oph_info_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
	}

	public function down()
	{
		$this->dropTable('patient_oph_info');
		$this->dropTable('patient_oph_info_cvi_status');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}
