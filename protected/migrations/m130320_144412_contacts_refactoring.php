<?php

class m130320_144412_contacts_refactoring extends ParallelMigration
{
	public $fp;

	public function up()
	{
		$this->createTable('person',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'contact_id' => 'int(10) unsigned NOT NULL',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `person_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `person_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `person_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `person_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('contact_metadata',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'contact_id' => 'int(10) unsigned NOT NULL',
				'key' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'value' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `contact_metadata_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `contact_metadata_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `contact_metadata_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `contact_metadata_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('contact_label',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `contact_label_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `contact_label_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `contact_label_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `contact_label_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('contact_location',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'contact_id' => 'int(10) unsigned NOT NULL',
				'site_id' => 'int(10) unsigned NULL',
				'institution_id' => 'int(10) unsigned NULL',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `contact_location_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `contact_location_created_user_id_fk` (`created_user_id`)',
				'KEY `contact_location_site_id_fk` (`site_id`)',
				'KEY `contact_location_institution_id_fk` (`institution_id`)',
				'CONSTRAINT `contact_location_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `contact_location_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `contact_location_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
				'CONSTRAINT `contact_location_institution_id_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`)',
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('address_type',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `address_type_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `address_type_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `address_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `address_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('address_type',array('name'=>'Reply to'));
		$this->insert('address_type',array('name'=>'Home'));
		$this->insert('address_type',array('name'=>'Correspondence'));
		$this->insert('address_type',array('name'=>'Transport'));

		$at_replyto = Yii::app()->db->createCommand()->select("*")->from("address_type")->where("name=:name",array(':name'=>'Reply to'))->queryRow();
		$at_home = Yii::app()->db->createCommand()->select("*")->from("address_type")->where("name=:name",array(':name'=>'Home'))->queryRow();
		$at_correspondence = Yii::app()->db->createCommand()->select("*")->from("address_type")->where("name=:name",array(':name'=>'Correspondence'))->queryRow();
		$at_transport = Yii::app()->db->createCommand()->select("*")->from("address_type")->where("name=:name",array(':name'=>'Transport'))->queryRow();

		$this->addColumn('address','address_type_id','int(10) unsigned NULL');
		$this->createIndex('address_address_type_id_fk','address','address_type_id');
		$this->addForeignKey('address_address_type_id_fk','address','address_type_id','address_type','id');

		$this->addColumn('contact','contact_label_id','int(10) unsigned NULL');
		$this->createIndex('contact_contact_label_id_fk','contact','contact_label_id');
		$this->addForeignKey('contact_contact_label_id_fk','contact','contact_label_id','contact_label','id');

		/* Specialists */

		$specialists = Yii::app()->db->createCommand()
			->select("specialist.*, specialist_type.name as specialist_type, contact.id as contact_id")
			->from("specialist")
			->join("specialist_type","specialist.specialist_type_id = specialist_type.id")
			->leftJoin("contact","contact.parent_class = 'Specialist' and contact.parent_id = specialist.id")
			->queryAll();

		$this->parallelise('migrateSpecialists',$specialists);

		$this->dropTable('site_specialist_assignment');
		$this->dropTable('institution_specialist_assignment');
		$this->dropTable('specialist');
		$this->dropTable('specialist_type');

		/* Firm consultants */

		$this->addColumn('firm','consultant_id','int(10) unsigned NOT NULL');

		$firm_ids = array();
		foreach (Yii::app()->db->createCommand()->select("id")->from("firm")->queryAll() as $firm) {
			$firm_ids[] = $firm['id'];
		}
		$this->parallelise("setFirmConsultants",$firm_ids);

		$this->update('firm',array('consultant_id'=>1),'consultant_id=0');

		$this->createIndex('firm_consultant_id_fk','firm','consultant_id');
		$this->addForeignKey('firm_consultant_id_fk','firm','consultant_id','user','id');

		/* User consultants */

		$this->addColumn('user','contact_id','int(10) unsigned NOT NULL');

		$users = Yii::app()->db->createCommand()
			->select("user.*, user_contact_assignment.contact_id, contact.parent_class, contact.parent_id, consultant.gmc_number, consultant.practitioner_code, consultant.gender")
			->from("user")
			->join("user_contact_assignment","user_contact_assignment.user_id = user.id")
			->join("contact","user_contact_assignment.contact_id = contact.id")
			->join("consultant","contact.parent_id = consultant.id")
			->queryAll();

		$this->parallelise('migrateUserContacts',$users);

		$this->createIndex('user_contact_id_fk','user','contact_id');
		$this->addForeignKey('user_contact_id_fk','user','contact_id','contact','id');

		$this->dropTable('user_contact_assignment');

		/* External ophthalmic consultants */

		$consultants = Yii::app()->db->createCommand()->select("*")->from("contact")->where("parent_class = 'Consultant'")->queryAll();

		$this->parallelise('migrateConsultantContacts',$consultants);

		/* GPs */

		$this->addColumn('gp','contact_id','int(10) unsigned NOT NULL');

		$gps = Yii::app()->db->createCommand()
			->select("gp.*, contact.id as contact_id")
			->from("gp")
			->join("contact","contact.parent_class = 'Gp' and contact.parent_id = gp.id")
			->queryAll();

		$this->parallelise('migrateGPContacts',$gps);

		$this->createIndex('gp_contact_id_fk','gp','contact_id');
		$this->addForeignKey('gp_contact_id_fk','gp','contact_id','contact','id');

		/* Sites */

		$this->addColumn('site','contact_id','int(10) unsigned NOT NULL');
		$this->addColumn('site','replyto_contact_id','int(10) unsigned NULL');

		$sites = Yii::app()->db->createCommand()
			->select("site.*, contact.id as contact_id")
			->from("site")
			->leftJoin("contact","contact.parent_class = 'Site_ReplyTo' and contact.parent_id = site.id")
			->queryAll();

		$this->parallelise('migrateSiteContacts',$sites);

		$this->createIndex('site_contact_id_fk','site','contact_id');
		$this->addForeignKey('site_contact_id_fk','site','contact_id','contact','id');
		$this->createIndex('site_replyto_contact_id_fk','site','replyto_contact_id');
		$this->addForeignKey('site_replyto_contact_id_fk','site','replyto_contact_id','contact','id');

		$this->dropColumn('site','address1');
		$this->dropColumn('site','address2');
		$this->dropColumn('site','address3');
		$this->dropColumn('site','postcode');

		/* Institutions */

		$this->addColumn('institution','contact_id','int(10) unsigned NOT NULL');

		$institutions = Yii::app()->db->createCommand()
			->select("institution.*, address.id as address_id")
			->from("institution")
			->leftJoin("address","address.parent_class = 'Institution' and address.parent_id = institution.id")
			->queryAll();

		$this->parallelise('migrateInstitutionContacts',$institutions);

		$this->createIndex('institution_contact_id_fk','institution','contact_id');
		$this->addForeignKey('institution_contact_id_fk','institution','contact_id','contact','id');

		/* Patients */

		$this->addColumn('patient','contact_id','int(10) unsigned NOT NULL');

		$patients = Yii::app()->db->createCommand()
			->select("patient.*, contact.id as contact_id")
			->from("patient")
			->join("contact","contact.parent_class = 'Patient' and contact.parent_id = patient.id")
			->queryAll();

		$this->parallelise('migratePatientContacts',$patients);

		$this->createIndex('patient_contact_id_fk','patient','contact_id');
		$this->addForeignKey('patient_contact_id_fk','patient','contact_id','contact','id');

		/* Patient contact assignments */

		$this->addColumn('patient_contact_assignment','location_id','int(10) unsigned NOT NULL');

		$pcas = Yii::app()->db->createCommand()
			->select("patient_contact_assignment.*, contact_location1.id as location_id1, contact_location2.id as location_id2")
			->from("patient_contact_assignment")
			->leftJoin("contact_location contact_location1","contact_location1.contact_id = patient_contact_assignment.contact_id and contact_location1.site_id = patient_contact_assignment.site_id")
			->leftJoin("contact_location contact_location2","contact_location2.contact_id = patient_contact_assignment.contact_id and contact_location2.institution_id = patient_contact_assignment.institution_id")
			->queryAll();

		$this->parallelise('migratePatientContactAssignments',$pcas);

		$this->createIndex('patient_contact_assignment_location_id_fk','patient_contact_assignment','location_id');
		$this->addForeignKey('patient_contact_assignment_location_id_fk','patient_contact_assignment','location_id','contact_location','id');

		$this->dropForeignKey('patient_contact_assignment_site_id_fk','patient_contact_assignment');
		$this->dropForeignKey('patient_contact_assignment_institution_id_fk','patient_contact_assignment');
		$this->dropIndex('patient_contact_assignment_site_id_fk','patient_contact_assignment');
		$this->dropIndex('patient_contact_assignment_institution_id_fk','patient_contact_assignment');
		$this->dropColumn('patient_contact_assignment','site_id');
		$this->dropColumn('patient_contact_assignment','institution_id');

		/* Address types */

		if ($this->canFork) {
			if ($this->fork() == 0) {
				$this->update('address',array('address_type_id'=>$at_home['id']),"type = 'H'");
				exit;
			}

			if ($this->fork() == 0) {
				$this->update('address',array('address_type_id'=>$at_correspondence['id']),"type = 'C'");
				exit;
			}

			if ($this->fork() == 0) {
				$this->update('address',array('address_type_id'=>$at_transport['id']),"type = 'T'");
				exit;
			}

			$this->waitForThreads();
		} else {
			$this->update('address',array('address_type_id'=>$at_home['id']),"type = 'H'");
			$this->update('address',array('address_type_id'=>$at_correspondence['id']),"type = 'C'");
			$this->update('address',array('address_type_id'=>$at_transport['id']),"type = 'T'");
		}

		$this->dropColumn('address','type');

		$this->delete('address',"parent_class = 'Patient'");

		/* Practices */

		$this->addColumn('practice','contact_id','int(10) unsigned NOT NULL');

		$practices = Yii::app()->db->createCommand()
			->select("practice.*, address.id as address_id")
			->from("practice")
			->leftJoin("address","address.parent_class = 'Practice' and address.parent_id = practice.id")
			->queryAll();

		$this->parallelise('migratePracticeContacts',$practices);

		$this->createIndex('practice_contact_id_fk','practice','contact_id');
		$this->addForeignKey('practice_contact_id_fk','practice','contact_id','contact','id');

		$this->delete('address',"parent_class = 'Practice'");

		$this->delete('contact',"parent_class in ('Consultant','Specialist','Gp','Site_ReplyTo','Patient')");
		$this->dropColumn('contact','parent_class');
		$this->dropColumn('contact','parent_id');
	}

	public function getConsultantUserID($firm_id) {
		$result = Yii::app()->db->createCommand()
			->select('u.id as id')
			->from('consultant cslt')
			->join('contact c', "c.parent_id = cslt.id and c.parent_class = 'Consultant'")
			->join('user_contact_assignment uca', 'uca.contact_id = c.id')
			->join('user u', 'u.id = uca.user_id')
			->join('firm_user_assignment fua', 'fua.user_id = u.id')
			->join('firm f', 'f.id = fua.firm_id')
			->where('f.id = :fid', array(
				':fid' => $firm_id
			))
			->queryRow();

		return $result['id'];
	}

	public function getLabel($name) {
		if ($label = Yii::app()->db->createCommand()->select("*")->from("contact_label")->where("name=:name",array(":name"=>$name))->queryRow()) {
			return $label;
		}

		$this->insert('contact_label',array('name'=>$name));

		return $this->getLabel($name);
	}

	public function migrateSpecialists($specialists) {
		foreach ($specialists as $specialist) {
			$label = $this->getLabel($specialist['specialist_type']);

			if ($specialist['contact_id']) {
				$this->update('contact',array('contact_label_id'=>$label['id'],'parent_class'=>''),"id={$specialist['contact_id']}");

				$this->insert('person',array('contact_id'=>$specialist['contact_id']));

				if ($specialist['gmc_number']) {
					$this->insert('contact_metadata',array('contact_id'=>$specialist['contact_id'],'key'=>'gmc_number','value'=>$specialist['gmc_number']));
				}
				if ($specialist['practitioner_code']) {
					$this->insert('contact_metadata',array('contact_id'=>$specialist['contact_id'],'key'=>'practitioner_code','value'=>$specialist['practitioner_code']));
				}
				if ($specialist['gender']) {
					$this->insert('contact_metadata',array('contact_id'=>$specialist['contact_id'],'key'=>'gender','value'=>$specialist['gender']));
				}
				if ($specialist['surgeon']) {
					$this->insert('contact_metadata',array('contact_id'=>$specialist['contact_id'],'key'=>'surgeon','value'=>$specialist['surgeon']));
				}

				foreach (Yii::app()->db->createCommand()->select("*")->from("site_specialist_assignment")->where("specialist_id=:specialist_id",array(':specialist_id'=>$specialist['id']))->queryAll() as $row) {
					$this->insert('contact_location',array('contact_id'=>$specialist['contact_id'],'site_id'=>$row['site_id']));
				}

				foreach (Yii::app()->db->createCommand()->select("*")->from("institution_specialist_assignment")->where("specialist_id=:specialist_id",array(':specialist_id'=>$specialist['id']))->queryAll() as $row) {
					$this->insert('contact_location',array('contact_id'=>$specialist['contact_id'],'institution_id'=>$row['institution_id']));
				}
			} else {
				echo "WARNING: contact missing for specialist: {$specialist['id']}\n";
			}
		}
	}

	public function setFirmConsultants($firm_ids) {
		foreach ($firm_ids as $firm_id) {
			$this->update('firm',array('consultant_id'=>$this->getConsultantUserID($firm_id)),"id=$firm_id");
		}
	}

	public function migrateUserContacts($users) {
		$co = $this->getLabel('Consultant Ophthalmologist');

		foreach ($users as $user) {
			$this->update('user',array('contact_id'=>$user['contact_id']),"id={$user['id']}");

			if ($user['parent_class'] == 'Consultant') {
				$this->update('contact',array('contact_label_id'=>$co['id']),"id={$user['contact_id']}");

				if ($user['gmc_number']) {
					$this->insert('contact_metadata',array('contact_id'=>$user['contact_id'],'key'=>'gmc_number','value'=>$user['gmc_number']));
				}
				if ($user['practitioner_code']) {
					$this->insert('contact_metadata',array('contact_id'=>$user['contact_id'],'key'=>'practitioner_code','value'=>$user['practitioner_code']));
				}
				if ($user['gender']) {
					$this->insert('contact_metadata',array('contact_id'=>$user['contact_id'],'key'=>'gender','value'=>$user['gender']));
				}
			}

			$this->update('contact',array('parent_class'=>''),"id={$user['contact_id']}");

			foreach (Yii::app()->db->createCommand()->select("*")->from("site_consultant_assignment")->where("consultant_id = :consultant_id",array(':consultant_id'=>$user['parent_id']))->queryAll() as $sca) {
				$this->insert('contact_location',array('contact_id'=>$user['contact_id'],'site_id'=>$sca['site_id']));
			}

			foreach (Yii::app()->db->createCommand()->select("*")->from("institution_consultant_assignment")->where("consultant_id = :consultant_id",array(':consultant_id'=>$user['parent_id']))->queryAll() as $sca) {
				$this->insert('contact_location',array('contact_id'=>$user['contact_id'],'institution_id'=>$sca['institution_id']));
			}
		}
	}

	public function migrateConsultantContacts($consultants) {
		$label = $this->getLabel('Consultant Ophthalmologist');

		foreach ($consultants as $contact) {
			$this->insert('person',array('contact_id'=>$contact['id']));
			$this->update('contact',array('parent_class'=>'','contact_label_id'=>$label['id']),"id={$contact['id']}");
		}
	}

	public function migrateGPContacts($gps) {
		$gpl = $this->getLabel('General Practitioner');

		foreach ($gps as $gp) {
			$this->update('gp',array('contact_id'=>$gp['contact_id']),"id={$gp['id']}");
			$this->update('contact',array('contact_label_id'=>$gpl['id'],'parent_class'=>''),"id={$gp['contact_id']}");
		}
	}

	public function migrateSiteContacts($sites) {
		foreach ($sites as $site) {
			$update = array();

			if ($site['contact_id']) {
				$this->update('contact',array('parent_class'=>''),"id={$site['contact_id']}");
				$update['replyto_contact_id'] = $site['contact_id'];
			}

			$this->obtainLock();

			$this->insert('contact',array());
			$contact_id = Yii::app()->db->createCommand()->select("max(id)")->from("contact")->queryScalar();

			$this->releaseLock();

			$update['contact_id'] = $contact_id;

			$this->update('site',$update,"id={$site['id']}");

			$this->insert('address',array('address1'=>$site['address1'],'address2'=>$site['address2'],'city'=>$site['address3'],'postcode'=>$site['postcode'],'parent_class'=>'Contact','parent_id'=>$contact_id,'country_id'=>1));
		}
	}

	public function migrateInstitutionContacts($institutions) {
		foreach ($institutions as $institution) {
			$this->obtainLock();

			$this->insert('contact',array());
			$contact_id = Yii::app()->db->createCommand()->select("max(id)")->from("contact")->queryScalar();

			$this->releaseLock();

			$this->update('institution',array('contact_id'=>$contact_id),"id={$institution['id']}");

			if ($institution['address_id']) {
				$this->update('address',array('parent_class'=>'Contact','parent_id'=>$contact_id),"id={$institution['address_id']}");
			}
		}
	}

	public function migratePatientContacts($patients) {
		foreach ($patients as $patient) {
			$this->update('patient',array('contact_id'=>$patient['contact_id']),"id={$patient['id']}");
			$this->update('contact',array('parent_class'=>''),"id={$patient['contact_id']}");

			$this->update('address',array('parent_class'=>'Contact','parent_id'=>$patient['contact_id']),"parent_class = 'Patient' and parent_id = {$patient['id']}");
		}
	}

	public function migratePatientContactAssignments($pcas) {
		foreach ($pcas as $pca) {
			if ($pca['site_id']) {
				if (!$pca['location_id1']) {
					$this->insert('contact_location',array('contact_id'=>$pca['contact_id'],'site_id'=>$pca['site_id']));
					$location = Yii::app()->db->createCommand()->select("id")->from("contact_location")->where("contact_id=:contact_id and site_id=:site_id",array(':contact_id'=>$pca['contact_id'],':site_id'=>$pca['site_id']))->queryRow();
					$location_id = $location['id'];
				} else {
					$location_id = $pca['location_id1'];
				}
			} else {
				if (!$pca['location_id2']) {
					$this->insert('contact_location',array('contact_id'=>$pca['contact_id'],'institution_id'=>$pca['institution_id']));
					$location = Yii::app()->db->createCommand()->select("id")->from("contact_location")->where("contact_id=:contact_id and institution_id=:institution_id",array(':contact_id'=>$pca['contact_id'],':institution_id'=>$pca['institution_id']))->queryRow();
					$location_id = $location['id'];
				} else {
					$location_id = $pca['location_id2'];
				}
			}

			$this->update('patient_contact_assignment',array('location_id'=>$location_id),"id={$pca['id']}");
		}
	}

	public function migratePracticeContacts($practices) {
		foreach ($practices as $practice) {
			$this->obtainLock();

			$this->insert('contact',array());
			$contact_id = Yii::app()->db->createCommand()->select("max(id)")->from("contact")->queryScalar();

			$this->releaseLock();

			$this->update('practice',array('contact_id'=>$contact_id),"id={$practice['id']}");
			$this->update('contact',array('primary_phone'=>$practice['phone']),"id=$contact_id");

			if ($practice['address_id']) {
				$this->update('address',array('parent_class'=>'Contact','parent_id'=>$contact_id),"id={$practice['address_id']}");
			}
		}
	}

	public function down()
	{
	}
}
