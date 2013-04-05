<?php

class m130320_144412_contacts_refactoring extends CDbMigration
{
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

		$at_replyto = Yii::app()->db->createCommand()->select("*")->from("address_type")->where("name=:name",array(':name'=>'Reply to'))->queryRow();

		$this->addColumn('address','address_type_id','int(10) unsigned NULL');
		$this->createIndex('address_address_type_id_fk','address','address_type_id');
		$this->addForeignKey('address_address_type_id_fk','address','address_type_id','address_type','id');

		$this->addColumn('contact','contact_label_id','int(10) unsigned NULL');
		$this->createIndex('contact_contact_label_id_fk','contact','contact_label_id');
		$this->addForeignKey('contact_contact_label_id_fk','contact','contact_label_id','contact_label','id');

		/* Specialists */

		foreach (Yii::app()->db->createCommand()->select("*")->from("specialist")->queryAll() as $specialist) {
			$specialist_type = Yii::app()->db->createCommand()->select("*")->from("specialist_type")->where("id=:id",array(':id'=>$specialist['specialist_type_id']))->queryRow();

			$label = $this->getLabel($specialist_type['name']);

			if ($contact = Yii::app()->db->createCommand()->select("id")->from("contact")->where("parent_class=:parent_class and parent_id=:parent_id",array(':parent_class'=>'Specialist',':parent_id'=>$specialist['id']))->queryRow()) {
				$this->update('contact',array('contact_label_id'=>$label['id'],'parent_class'=>''),"id={$contact['id']}");

				$this->insert('person',array('contact_id'=>$contact['id']));

				if ($specialist['gmc_number']) {
					$this->insert('contact_metadata',array('contact_id'=>$contact['id'],'key'=>'gmc_number','value'=>$specialist['gmc_number']));
				}
				if ($specialist['practitioner_code']) {
					$this->insert('contact_metadata',array('contact_id'=>$contact['id'],'key'=>'practitioner_code','value'=>$specialist['practitioner_code']));
				}
				if ($specialist['gender']) {
					$this->insert('contact_metadata',array('contact_id'=>$contact['id'],'key'=>'gender','value'=>$specialist['gender']));
				}
				if ($specialist['surgeon']) {
					$this->insert('contact_metadata',array('contact_id'=>$contact['id'],'key'=>'surgeon','value'=>$specialist['surgeon']));
				}

				foreach (Yii::app()->db->createCommand()->select("*")->from("site_specialist_assignment")->where("specialist_id=:specialist_id",array(':specialist_id'=>$specialist['id']))->queryAll() as $row) {
					$this->insert('contact_location',array('contact_id'=>$contact['id'],'site_id'=>$row['site_id']));
				}

				foreach (Yii::app()->db->createCommand()->select("*")->from("institution_specialist_assignment")->where("specialist_id=:specialist_id",array(':specialist_id'=>$specialist['id']))->queryAll() as $row) {
					$this->insert('contact_location',array('contact_id'=>$contact['id'],'institution_id'=>$row['institution_id']));
				}
			} else {
				echo "WARNING: contact missing for specialist: {$specialist['id']}\n";
			}
		}

		$this->dropTable('site_specialist_assignment');
		$this->dropTable('institution_specialist_assignment');
		$this->dropTable('specialist');
		$this->dropTable('specialist_type');

		/* Firm consultants */

		$this->addColumn('firm','consultant_id','int(10) unsigned NOT NULL');

		foreach (Firm::model()->findAll() as $firm) {
			$this->update('firm',array('consultant_id'=>$firm->getConsultantUser()->id),"id=$firm->id");
		}

		$this->createIndex('firm_consultant_id_fk','firm','consultant_id');
		$this->addForeignKey('firm_consultant_id_fk','firm','consultant_id','user','id');

		/* User consultants */

		$this->addColumn('user','contact_id','int(10) unsigned NOT NULL');

		$co = $this->getLabel('Consultant Ophthalmologist');

		foreach (Yii::app()->db->createCommand()->select("*")->from("user")->queryAll() as $user) {
			$uca = Yii::app()->db->createCommand()->select("*")->from("user_contact_assignment")->where("user_id=:user_id",array(':user_id'=>$user['id']))->queryRow();

			$this->update('user',array('contact_id'=>$uca['contact_id']),"id={$user['id']}");

			$contact = Yii::app()->db->createCommand()->select("*")->from("contact")->where("id = :id",array(':id'=>$uca['contact_id']))->queryRow();

			if ($contact['parent_class'] == 'Consultant') {
				$this->update('contact',array('contact_label_id'=>$co['id']),"id={$contact['id']}");
				$consultant = Yii::app()->db->createCommand()->select("*")->from("consultant")->where("id=:id",array(':id'=>$contact['parent_id']))->queryRow();
				if ($consultant['gmc_number']) {
					$this->insert('contact_metadata',array('contact_id'=>$contact['id'],'key'=>'gmc_number','value'=>$consultant['gmc_number']));
				}
				if ($consultant['practitioner_code']) {
					$this->insert('contact_metadata',array('contact_id'=>$contact['id'],'key'=>'practitioner_code','value'=>$consultant['practitioner_code']));
				}
				if ($consultant['gender']) {
					$this->insert('contact_metadata',array('contact_id'=>$contact['id'],'key'=>'gender','value'=>$consultant['gender']));
				}
			}

			$this->update('contact',array('parent_class'=>''),"id={$contact['id']}");

			foreach (Yii::app()->db->createCommand()->select("*")->from("site_consultant_assignment")->where("consultant_id = :consultant_id",array(':consultant_id'=>$contact['parent_id']))->queryAll() as $sca) {
				$this->insert('contact_location',array('contact_id'=>$uca['contact_id'],'site_id'=>$sca['site_id']));
			}

			foreach (Yii::app()->db->createCommand()->select("*")->from("institution_consultant_assignment")->where("consultant_id = :consultant_id",array(':consultant_id'=>$contact['parent_id']))->queryAll() as $sca) {
				$this->insert('contact_location',array('contact_id'=>$uca['contact_id'],'institution_id'=>$sca['institution_id']));
			}
		}

		$this->createIndex('user_contact_id_fk','user','contact_id');
		$this->addForeignKey('user_contact_id_fk','user','contact_id','contact','id');

		$this->dropTable('user_contact_assignment');

		/* External ophthalmic consultants */

		$label = $this->getLabel('Consultant Ophthalmologist');

		foreach (Yii::app()->db->createCommand()->select("*")->from("contact")->where("parent_class = 'Consultant'")->queryAll() as $contact) {
			$this->insert('person',array('contact_id'=>$contact['id']));
			$this->update('contact',array('parent_class'=>'','contact_label_id'=>$label['id']),"id={$contact['id']}");
		}

		/* GPs */

		$this->addColumn('gp','contact_id','int(10) unsigned NOT NULL');

		$gpl = $this->getLabel('General Practitioner');

		foreach (Yii::app()->db->createCommand()->select("*")->from("gp")->queryAll() as $gp) {
			if ($contact = Yii::app()->db->createCommand()->select("*")->from("contact")->where("parent_class=:parent_class and parent_id=:parent_id",array(':parent_class'=>'Gp',':parent_id'=>$gp['id']))->queryRow()) {
				$this->update('gp',array('contact_id'=>$contact['id']),"id={$gp['id']}");
				$this->update('contact',array('contact_label_id'=>$gpl['id'],'parent_class'=>''),"id={$contact['id']}");
			}
		}

		$this->createIndex('gp_contact_id_fk','gp','contact_id');
		$this->addForeignKey('gp_contact_id_fk','gp','contact_id','contact','id');

		/* Sites */

		foreach (Yii::app()->db->createCommand()->select("*")->from("site")->queryAll() as $site) {
			if ($contact = Yii::app()->db->createCommand()->select("*")->from("contact")->where("parent_class=:parent_class and parent_id=:parent_id",array(':parent_class'=>'Site_ReplyTo',':parent_id'=>$site['id']))->queryRow()) {
				if ($address = Yii::app()->db->createCommand()->select("*")->from("address")->where("parent_class=:parent_class and parent_id=:parent_id",array(':parent_class'=>'Contact',':parent_id'=>$contact['id']))->queryRow()) {
					$this->update('address',array('address_type_id'=>$at_replyto['id'],'parent_class'=>'Site','parent_id'=>$site['id']),"id={$address['id']}");
				}
			}

			$this->insert('address',array('address1'=>$site['address1'],'address2'=>$site['address2'],'city'=>$site['address3'],'postcode'=>$site['postcode'],'parent_class'=>'Site','parent_id'=>$site['id'],'country_id'=>1));
		}

		$this->dropColumn('site','address1');
		$this->dropColumn('site','address2');
		$this->dropColumn('site','address3');
		$this->dropColumn('site','postcode');

		$this->delete('contact',"parent_class in ('Consultant','Specialist','Gp','Site_ReplyTo')");
		$this->dropColumn('contact','parent_class');
		$this->dropColumn('contact','parent_id');

		/* Patient contact assignments */

		$this->addColumn('patient_contact_assignment','location_id','int(10) unsigned NOT NULL');

		foreach (Yii::app()->db->createCommand()->select("*")->from("patient_contact_assignment")->queryAll() as $pca) {
			if ($pca['site_id']) {
				if (!$location = Yii::app()->db->createCommand()->select("*")->from("contact_location")->where("contact_id=:contact_id and site_id=:site_id",array(':contact_id'=>$pca['contact_id'],':site_id'=>$pca['site_id']))->queryRow()) {
					$this->insert('contact_location',array('contact_id'=>$pca['contact_id'],'site_id'=>$pca['site_id']));
					$location = Yii::app()->db->createCommand()->select("*")->from("contact_location")->where("contact_id=:contact_id and site_id=:site_id",array(':contact_id'=>$pca['contact_id'],':site_id'=>$pca['site_id']))->queryRow();
				}
			} else {
				if (!$location = Yii::app()->db->createCommand()->select("*")->from("contact_location")->where("contact_id=:contact_id and institution_id=:institution_id",array(':contact_id'=>$pca['contact_id'],':institution_id'=>$pca['institution_id']))->queryRow()) {
					$this->insert('contact_location',array('contact_id'=>$pca['contact_id'],'institution_id'=>$pca['institution_id']));
					$location = Yii::app()->db->createCommand()->select("*")->from("contact_location")->where("contact_id=:contact_id and institution_id=:institution_id",array(':contact_id'=>$pca['contact_id'],':institution_id'=>$pca['institution_id']))->queryRow();
				}
			}
			$this->update('patient_contact_assignment',array('location_id'=>$location['id']),"id={$pca['id']}");
		}

		$this->createIndex('patient_contact_assignment_location_id_fk','patient_contact_assignment','location_id');
		$this->addForeignKey('patient_contact_assignment_location_id_fk','patient_contact_assignment','location_id','contact_location','id');

		$this->dropForeignKey('patient_contact_assignment_site_id_fk','patient_contact_assignment');
		$this->dropForeignKey('patient_contact_assignment_institution_id_fk','patient_contact_assignment');
		$this->dropIndex('patient_contact_assignment_site_id_fk','patient_contact_assignment');
		$this->dropIndex('patient_contact_assignment_institution_id_fk','patient_contact_assignment');
		$this->dropColumn('patient_contact_assignment','site_id');
		$this->dropColumn('patient_contact_assignment','institution_id');
	}

	public function getLabel($name) {
		if ($label = Yii::app()->db->createCommand()->select("*")->from("contact_label")->where("name=:name",array(":name"=>$name))->queryRow()) {
			return $label;
		}

		$this->insert('contact_label',array('name'=>$name));

		return $this->getLabel($name);
	}

	public function down()
	{
	}
}
