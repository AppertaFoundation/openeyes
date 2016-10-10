<?php 
class m160728_080358_event_type_Internalreferral extends CDbMigration
{
	public function up()
	{
		if (!$this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'Internalreferral'))->queryRow()) {
			$group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name',array(':name'=>'Communication events'))->queryRow();
			$this->insert('event_type', array('class_name' => 'Internalreferral', 'name' => 'Internal Referral','event_group_id' => $group['id']));
		}
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'Internalreferral'))->queryRow();

		if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name and event_type_id=:eventTypeId', array(':name'=>'Referral Details',':eventTypeId'=>$event_type['id']))->queryRow()) {
			$this->insert('element_type', array('name' => 'Referral Details','class_name' => 'OEModule\Internalreferral\models\Element_Internalreferral_ReferralDetails', 'event_type_id' => $event_type['id'], 'display_order' => 1));
		}

		$this->createTable('et_internalreferral_referraldet', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'referrer_id' => 'int(10) unsigned NOT NULL',
				'from_subspecialty_id' => 'int(10) unsigned NOT NULL',
				'to_subspecialty_id' => 'int(10) unsigned NOT NULL',
				'integration_data' => 'text',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'deleted' => 'tinyint(1) unsigned not null',
				'PRIMARY KEY (`id`)',
				'KEY `et_internalreferral_referraldet_lmui_fk` (`last_modified_user_id`)',
				'KEY `et_internalreferral_referraldet_cui_fk` (`created_user_id`)',
				'KEY `et_internalreferral_referraldet_ev_fk` (`event_id`)',
				'KEY `et_internalreferral_referraldet_referrer_id_fk` (`referrer_id`)',
				'KEY `et_internalreferral_referraldet_from_subspecialty_id_fk` (`from_subspecialty_id`)',
				'KEY `et_internalreferral_referraldet_to_subspecialty_id_fk` (`to_subspecialty_id`)',
				'CONSTRAINT `et_internalreferral_referraldet_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_internalreferral_referraldet_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_internalreferral_referraldet_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `et_internalreferral_referraldet_referrer_id_fk` FOREIGN KEY (`referrer_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_internalreferral_referraldet_from_subspecialty_id_fk` FOREIGN KEY (`from_subspecialty_id`) REFERENCES `subspecialty` (`id`)',
				'CONSTRAINT `et_internalreferral_referraldet_to_subspecialty_id_fk` FOREIGN KEY (`to_subspecialty_id`) REFERENCES `subspecialty` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('et_internalreferral_referraldet_version', array(
				'id' => 'int(10) unsigned NOT NULL',
				'event_id' => 'int(10) unsigned NOT NULL',
				'referrer_id' => 'int(10) unsigned NOT NULL', // referrer
				'from_subspecialty_id' => 'int(10) unsigned NOT NULL', // From
				'to_subspecialty_id' => 'int(10) unsigned NOT NULL', // To
				'integration_data' => 'text',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'deleted' => 'tinyint(1) unsigned not null',
				'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'PRIMARY KEY (`version_id`)',
				'KEY `acv_et_internalreferral_referraldet_lmui_fk` (`last_modified_user_id`)',
				'KEY `acv_et_internalreferral_referraldet_cui_fk` (`created_user_id`)',
				'KEY `acv_et_internalreferral_referraldet_ev_fk` (`event_id`)',
				'KEY `et_internalreferral_referraldet_aid_fk` (`id`)',
				'KEY `acv_et_internalreferral_referraldet_referrer_id_fk` (`referrer_id`)',
				'KEY `acv_et_internalreferral_referraldet_from_subspecialty_id_fk` (`from_subspecialty_id`)',
				'KEY `acv_et_internalreferral_referraldet_to_subspecialty_id_fk` (`to_subspecialty_id`)',
				'CONSTRAINT `acv_et_internalreferral_referraldet_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `acv_et_internalreferral_referraldet_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `acv_et_internalreferral_referraldet_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `et_internalreferral_referraldet_aid_fk` FOREIGN KEY (`id`) REFERENCES `et_internalreferral_referraldet` (`id`)',
				'CONSTRAINT `acv_et_internalreferral_referraldet_referrer_id_fk` FOREIGN KEY (`referrer_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `acv_et_internalreferral_referraldet_from_subspecialty_id_fk` FOREIGN KEY (`from_subspecialty_id`) REFERENCES `subspecialty` (`id`)',
				'CONSTRAINT `acv_et_internalreferral_referraldet_to_subspecialty_id_fk` FOREIGN KEY (`to_subspecialty_id`) REFERENCES `subspecialty` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

	}

	public function down()
	{
		$this->dropTable('et_internalreferral_referraldet_version');
		$this->dropTable('et_internalreferral_referraldet');

		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'Internalreferral'))->queryRow();

		foreach ($this->dbConnection->createCommand()->select('id')->from('event')->where('event_type_id=:event_type_id', array(':event_type_id'=>$event_type['id']))->queryAll() as $row) {
			$this->delete('audit', 'event_id='.$row['id']);
			$this->delete('event', 'id='.$row['id']);
		}

		$this->delete('element_type', 'event_type_id='.$event_type['id']);
		$this->delete('event_type', 'id='.$event_type['id']);
	}
}
