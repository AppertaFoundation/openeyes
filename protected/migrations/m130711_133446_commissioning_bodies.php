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
				'KEY `commissioningbody_type_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `commissioningbody_type_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `commissioningbody_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `commissioningbody_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
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
				'KEY `commissioningbody_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `commissioningbody_created_user_id_fk` (`created_user_id`)',
				'KEY `commissioningbody_tid_fk` (`commissioningbody_type_id`)',
				'KEY `commissioningbody_cid_fk` (`contact_id`)',
				'CONSTRAINT `commissioningbody_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `commissioningbody_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `commissioningbody_tid_fk` FOREIGN KEY (`commissioningbody_type_id`) REFERENCES `commissioningbody_type` (`id`)',
				'CONSTRAINT `commissioningbody_cid_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`)',
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
		
		$this->createTable('commissioningbodyservice_type', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'shortname' => 'varchar(16)',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `commissioningbodyservice_type_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `commissioningbodyservice_type_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `commissioningbodyservice_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `commissioningbodyservice_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
		),
				'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		
		$this->insert('commissioningbodyservice_type', array('name' => 'Diabetic Retinopathy Screening Service', 'shortname' => 'DRSS'));
		
		$this->createTable('commissioningbodyservice',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'code' => 'varchar(16)',
				'commissioningbodyservice_type_id' => 'int(10) unsigned NOT NULL',
				'commissioningbody_id' => 'int(10) unsigned',
				'contact_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `commissioningbodyservice_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `commissioningbodyservice_created_user_id_fk` (`created_user_id`)',
				'KEY `commissioningbodyservice_tid_fk` (`commissioningbodyservice_type_id`)',
				'KEY `commissioningbodyservice_cbid_fk` (`commissioningbody_id`)',
				'KEY `commissioningbodyservice_cid_fk` (`contact_id`)',
				'CONSTRAINT `commissioningbodyservice_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `commissioningbodyservice_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `commissioningbodyservice_tid_fk` FOREIGN KEY (`commissioningbodyservice_type_id`) REFERENCES `commissioningbodyservice_type` (`id`)',
				'CONSTRAINT `commissioningbodyservice_cbid_fk` FOREIGN KEY (`commissioningbody_id`) REFERENCES `commissioningbody` (`id`)',
				'CONSTRAINT `commissioningbodyservice_cid_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`)',
		),
				'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		
		// A couple of test records
		/*
		$this->insert('contact', array('first_name' => '', 'last_name' => ''));
		$cid = $this->dbConnection->lastInsertID;
		$this->insert('commissioningbody', array('name' => 'Surrey Health PCT', 'code' => '06L', 'contact_id' => $cid, 'commissioningbody_type_id' => 1));
		$surrey_id = $this->dbConnection->lastInsertID;
		$this->insert('address', array('address1' => 'Surrey Heath House', 'address2' => 'Knoll Road', 
				'city' => 'Camberley', 'county' => 'Surrey', 'postcode' => 'GU15 3HD', 'country_id' => 1, 'email' => 'SHCCG.ContactUs@nhs.net', 
				'parent_class' => 'Contact', 'parent_id' => $cid, 'address_type_id' => 3));
		$this->insert('contact', array('first_name' => '', 'last_name' => ''));
		$cid = $this->dbConnection->lastInsertID;
		$this->insert('commissioningbody', array('name' => 'NHS Nene CCG', 'code' => 'SPD', 'contact_id' => $cid, 'commissioningbody_type_id' => 1));
		$nene_id = $this->dbConnection->lastInsertID;
		$this->insert('address', array('address1' => 'Francis Crick House', 'address2' => 'Summerhouse Road, Moulton Park',
				'city' => 'Northampton', 'county' => 'Northamptionshire', 'postcode' => 'NN3 6BF', 'country_id' => 1, 'email' => 'involvement.nene@nhs.net',
				'parent_class' => 'Contact', 'parent_id' => $cid, 'address_type_id' => 3));
		
		// lucky old violet coffin
		$this->insert('commissioningbody_patient_assignment', array('commissioningbody_id' => $surrey_id, 'patient_id' => 19434));
		
		$this->insert('commissioningbody_practice_assignment', array('commissioningbody_id' => $nene_id, 'practice_id' => 2));
		
		$this->insert('contact', array('first_name' => '', 'last_name' => ''));
		$cid = $this->dbConnection->lastInsertID;
		$this->insert('commissioningbodyservice', array('name' => 'Northamptonshire Diabetic Eye Screening Service', 'code' => 'SPD', 
				'contact_id' => $cid, 'commissioningbodyservice_type_id' => 1, 'commissioningbody_id' => $nene_id));
		$nene_id = $this->dbConnection->lastInsertID;
		$this->insert('address', array('address1' => 'Abbey Block', 'address2' => 'Isebrook Hospital, Irthlingborough Road',
				'city' => 'Wellingborough', 'county' => 'Northamptionshire', 'postcode' => 'NN8 1LP', 'country_id' => 1, 'email' => 'drsnorthants@nhs.net',
				'parent_class' => 'Contact', 'parent_id' => $cid, 'address_type_id' => 3));
		
		$this->insert('contact', array('first_name' => '', 'last_name' => ''));
		$cid = $this->dbConnection->lastInsertID;
		$this->insert('commissioningbodyservice', array('name' => 'Surrey NHS Diabetic Eye Screening Programme', 'code' => 'SPD',
				'contact_id' => $cid, 'commissioningbodyservice_type_id' => 1, 'commissioningbody_id' => $surrey_id));
		$nene_id = $this->dbConnection->lastInsertID;
		$this->insert('address', array('address1' => 'Farnham Hospital', 'address2' => 'Hale Road',
				'city' => 'Farnham', 'county' => 'Surrey', 'postcode' => 'GU9 9QL', 'country_id' => 1, 'email' => 'reg.parsons@nhs.net',
				'parent_class' => 'Contact', 'parent_id' => $cid, 'address_type_id' => 3));
		*/
	}

	public function down()
	{
		
		$cb_records = $this->dbConnection->createCommand()->select(array('contact_id'))
		->from('commissioningbody')
		->queryAll();
		
		$cbs_records = $this->dbConnection->createCommand()->select(array('contact_id'))
		->from('commissioningbodyservice')
		->queryAll(); 
		
		$this->dropTable('commissioningbodyservice');
		$this->dropTable('commissioningbodyservice_type');
		$this->dropTable('commissioningbody_patient_assignment');
		$this->dropTable('commissioningbody_practice_assignment');
		$this->dropTable('commissioningbody');
		$this->dropTable('commissioningbody_type');
		
		foreach ($cb_records as $rec) {
			$this->delete('contact',
					'id = :id',
					array(
							':id' => $rec['contact_id'],
					) );
		}
		
		foreach ($cbs_records as $rec) {
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