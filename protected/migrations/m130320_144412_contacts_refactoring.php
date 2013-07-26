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
			$this->update('firm',array('consultant_id'=>$this->getConsultantUserID($firm)),"id=$firm->id");
		}

		$this->update('firm',array('consultant_id'=>1),'consultant_id=0');

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

		$this->addColumn('site','contact_id','int(10) unsigned NOT NULL');
		$this->addColumn('site','replyto_contact_id','int(10) unsigned NULL');

		foreach (Yii::app()->db->createCommand()->select("*")->from("site")->queryAll() as $site) {
			$update = array();

			if ($contact = Yii::app()->db->createCommand()->select("*")->from("contact")->where("parent_class=:parent_class and parent_id=:parent_id",array(':parent_class'=>'Site_ReplyTo',':parent_id'=>$site['id']))->queryRow()) {
				$this->update('contact',array('parent_class'=>''),"id={$contact['id']}");
				$update['replyto_contact_id'] = $contact['id'];
			}

			$this->insert('contact',array());
			$contact_id = Yii::app()->db->createCommand()->select("max(id)")->from("contact")->queryScalar();

			$update['contact_id'] = $contact_id;

			$this->update('site',$update,"id={$site['id']}");

			$this->insert('address',array('address1'=>$site['address1'],'address2'=>$site['address2'],'city'=>$site['address3'],'postcode'=>$site['postcode'],'parent_class'=>'Contact','parent_id'=>$contact_id,'country_id'=>1));
		}

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

		foreach (Yii::app()->db->createCommand()->select("*")->from("institution")->queryAll() as $institution) {
			$this->insert('contact',array());
			$contact_id = Yii::app()->db->createCommand()->select("max(id)")->from("contact")->queryScalar();

			$this->update('institution',array('contact_id'=>$contact_id),"id={$institution['id']}");

			if ($address = Yii::app()->db->createCommand()->select("*")->from("address")->where("parent_class = :parent_class and parent_id = :parent_id",array(':parent_class'=>'Institution',':parent_id'=>$institution['id']))->queryRow()) {
				$this->update('address',array('parent_class'=>'Contact','parent_id'=>$contact_id),"id={$address['id']}");
			}
		}

		$this->createIndex('institution_contact_id_fk','institution','contact_id');
		$this->addForeignKey('institution_contact_id_fk','institution','contact_id','contact','id');

		/* Patients */

		$this->addColumn('patient','contact_id','int(10) unsigned NOT NULL');

		foreach (Yii::app()->db->createCommand()->select("*")->from("patient")->queryAll() as $patient) {
			if ($contact = Yii::app()->db->createCommand()->select("*")->from("contact")->where("parent_class=:parent_class and parent_id=:parent_id",array(':parent_class'=>'Patient',':parent_id'=>$patient['id']))->queryRow()) {
				$this->update('patient',array('contact_id'=>$contact['id']),"id={$patient['id']}");
				$this->update('contact',array('parent_class'=>''),"id={$contact['id']}");

				$this->update('address',array('parent_class'=>'Contact','parent_id'=>$contact['id']),"parent_class = 'Patient' and parent_id = {$patient['id']}");
			}
		}

		$this->createIndex('patient_contact_id_fk','patient','contact_id');
		$this->addForeignKey('patient_contact_id_fk','patient','contact_id','contact','id');

		$this->delete('contact',"parent_class in ('Consultant','Specialist','Gp','Site_ReplyTo','Patient')");
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

		/* Address types */

		$this->update('address',array('address_type_id'=>$at_home['id']),"type = 'H'");
		$this->update('address',array('address_type_id'=>$at_correspondence['id']),"type = 'C'");
		$this->update('address',array('address_type_id'=>$at_transport['id']),"type = 'T'");

		$this->dropColumn('address','type');

		$this->delete('address',"parent_class = 'Patient'");

		/* Practices */

		$this->addColumn('practice','contact_id','int(10) unsigned NOT NULL');

		foreach (Yii::app()->db->createCommand()->select("*")->from("practice")->queryAll() as $practice) {
			$address = Yii::app()->db->createCommand()->select("*")->from("address")->where("parent_class = :parent_class and parent_id = :parent_id",array(':parent_class'=>'Practice',':parent_id'=>$practice['id']))->queryRow();

			$this->insert('contact',array());
			$contact_id = Yii::app()->db->createCommand()->select("max(id)")->from("contact")->queryScalar();

			$this->update('practice',array('contact_id'=>$contact_id),"id={$practice['id']}");
			$this->update('contact',array('primary_phone'=>$practice['phone']),"id=$contact_id");

			if ($address) {
				$this->update('address',array('parent_class'=>'Contact','parent_id'=>$contact_id),"id={$address['id']}");
			}
		}

		$this->createIndex('practice_contact_id_fk','practice','contact_id');
		$this->addForeignKey('practice_contact_id_fk','practice','contact_id','contact','id');

		$this->delete('address',"parent_class = 'Practice'");
	}

	public function getConsultantUserID($firm)
	{
		$result = Yii::app()->db->createCommand()
			->select('u.id as id')
			->from('consultant cslt')
			->join('contact c', "c.parent_id = cslt.id and c.parent_class = 'Consultant'")
			->join('user_contact_assignment uca', 'uca.contact_id = c.id')
			->join('user u', 'u.id = uca.user_id')
			->join('firm_user_assignment fua', 'fua.user_id = u.id')
			->join('firm f', 'f.id = fua.firm_id')
			->where('f.id = :fid', array(
				':fid' => $firm->id
			))
			->queryRow();

		return $result['id'];
	}

	public function getLabel($name)
	{
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
