<?php

class m130711_133446_commissioning_bodies extends CDbMigration
{
	public function up()
	{
		$this->createTable('commissioningbody_type', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'shortname' => 'varchar(16)',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `commissiongbody_type_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `commissiongbody_type_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `commissiongbody_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `commissiongbody_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
		),
				'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		
		$this->insert('commissioningbody_type', array('name' => 'Clinical Commissioning Group', 'shortname' => 'CCG'));
		
		$this->createTable('commissioningbody',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'code' => 'varchar(16)',
				'commissioningbody_type_id' => 'int(10) unsigned NOT NULL',
				'contact_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `commissiongbody_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `commissiongbody_created_user_id_fk` (`created_user_id`)',
				'KEY `commissiongbody_tid_fk` (`commissioningbody_type_id`)',
				'KEY `commissiongbody_cid_fk` (`contact_id`)',
				'CONSTRAINT `commissiongbody_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `commissiongbody_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `commissiongbody_tid_fk` FOREIGN KEY (`commissioningbody_type_id`) REFERENCES `commissioningbody_type` (`id`)',
				'CONSTRAINT `commissiongbody_cid_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`)',
		),
				'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		
		$this->createTable('commissioningbody_practice_assignment',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'commissioningbody_id' => 'int(10) unsigned NOT NULL',
				'practice_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `commissioningbody_practice_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `commissioningbody_practice_assignment_created_user_id_fk` (`created_user_id`)',
				'KEY `commissioningbody_practice_assignment_cbid_fk` (`commissioningbody_id`)',
				'KEY `commissioningbody_practice_assignment_pid_fk` (`practice_id`)',
				'CONSTRAINT `commissioningbody_practice_assignment_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `commissioningbody_practice_assignment_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `commissioningbody_practice_assignment_cbid_fk` FOREIGN KEY (`commissioningbody_id`) REFERENCES `commissioningbody` (`id`)',
				'CONSTRAINT `commissioningbody_practice_assignment_pid_fk` FOREIGN KEY (`practice_id`) REFERENCES `practice` (`id`)',
		),
				'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('commissioningbody_patient_assignment',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'commissioningbody_id' => 'int(10) unsigned NOT NULL',
				'patient_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `commissioningbody_patient_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `commissioningbody_patient_assignment_created_user_id_fk` (`created_user_id`)',
				'KEY `commissioningbody_patient_assignment_cbid_fk` (`commissioningbody_id`)',
				'KEY `commissioningbody_patient_assignment_pid_fk` (`patient_id`)',
				'CONSTRAINT `commissioningbody_patient_assignment_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `commissioningbody_patient_assignment_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `commissioningbody_patient_assignment_cbid_fk` FOREIGN KEY (`commissioningbody_id`) REFERENCES `commissioningbody` (`id`)',
				'CONSTRAINT `commissioningbody_patient_assignment_pid_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)',
		),
				'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		
		// A couple of test records
		/*
		$this->insert('contact', array('first_name' => '', 'last_name' => ''));
		$cid = $this->dbConnection->lastInsertID;
		$this->insert('commissioningbody', array('name' => 'SURREY PCT', 'code' => '06L', 'contact_id' => $cid, 'commissioningbody_type_id' => 1));
		$this->insert('contact', array('first_name' => '', 'last_name' => ''));
		$cid = $this->dbConnection->lastInsertID;
		$this->insert('commissioningbody', array('name' => 'NORTHAMPTONSHIRE PCT', 'code' => '06L', 'contact_id' => $cid, 'commissioningbody_type_id' => 1));
		*/
	}

	public function down()
	{
		$records = $this->dbConnection->createCommand()->select(array('contact_id'))
		->from('commissioningbody')
		->queryAll();
		
		$this->dropTable('commissioningbody_patient_assignment');
		$this->dropTable('commissioningbody_practice_assignment');
		$this->dropTable('commissioningbody');
		$this->dropTable('commissioningbody_type');
		
		foreach ($records as $rec) {
			$this->delete('contact',
					'id = :id',
					array(
							':id' => $rec['contact_id'],
					) );
		}
		
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