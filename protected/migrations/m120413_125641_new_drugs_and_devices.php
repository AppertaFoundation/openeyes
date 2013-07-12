<?php

class m120413_125641_new_drugs_and_devices extends CDbMigration
{
	public function up()
	{
		$this->createTable('operative_device',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `operative_device_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `operative_device_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `operative_device_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `operative_device_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('site_subspecialty_operative_device',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'site_id' => 'int(10) unsigned NOT NULL',
				'subspecialty_id' => 'int(10) unsigned NOT NULL',
				'operative_device_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'default' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ss_operative_device_site_id_fk` (`site_id`)',
				'KEY `ss_operative_device_subspecialty_id_fk` (`subspecialty_id`)',
				'KEY `ss_operative_device_operative_device_id` (`operative_device_id`)',
				'KEY `ss_operative_device_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `ss_operative_device_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `ss_operative_device_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
				'CONSTRAINT `ss_operative_device_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)',
				'CONSTRAINT `ss_operative_device_operative_device_id_fk` FOREIGN KEY (`operative_device_id`) REFERENCES `operative_device` (`id`)',
				'CONSTRAINT `ss_operative_device_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ss_operative_device_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('operative_device',array('id'=>1,'name'=>'Vision blue'));
		$this->insert('operative_device',array('id'=>2,'name'=>'Intracameral phenylephrine'));
		$this->insert('operative_device',array('id'=>3,'name'=>'Triamcinolone'));
		$this->insert('operative_device',array('id'=>4,'name'=>'Healon'));
		$this->insert('operative_device',array('id'=>5,'name'=>'Healon GV'));
		$this->insert('operative_device',array('id'=>6,'name'=>'Provisc'));
		$this->insert('operative_device',array('id'=>7,'name'=>'HPMC'));
		$this->insert('operative_device',array('id'=>8,'name'=>'Healon 5'));

		$ophthalmology = $this->dbConnection->createCommand()->select('id')->from('specialty')->where('name=:name',array(':name'=>'Ophthalmology'))->queryRow();
		$subspecialty = $this->dbConnection->createCommand()->select('id')->from('subspecialty')->where('name=:name and specialty_id=:specialty_id',array(':name'=>'Cataract',':specialty_id'=>$ophthalmology['id']))->queryRow();

		if ($subspecialty) {
			$this->insert('site_subspecialty_operative_device',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'operative_device_id'=>1,'display_order'=>1,'default'=>0));
			$this->insert('site_subspecialty_operative_device',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'operative_device_id'=>2,'display_order'=>2,'default'=>0));
			$this->insert('site_subspecialty_operative_device',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'operative_device_id'=>3,'display_order'=>3,'default'=>0));
			$this->insert('site_subspecialty_operative_device',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'operative_device_id'=>4,'display_order'=>4,'default'=>0));
			$this->insert('site_subspecialty_operative_device',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'operative_device_id'=>5,'display_order'=>5,'default'=>0));
			$this->insert('site_subspecialty_operative_device',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'operative_device_id'=>6,'display_order'=>6,'default'=>1));
			$this->insert('site_subspecialty_operative_device',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'operative_device_id'=>7,'display_order'=>7,'default'=>1));
			$this->insert('site_subspecialty_operative_device',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'operative_device_id'=>8,'display_order'=>8,'default'=>0));
		}

		$this->dropTable('site_subspecialty_drug_default');

		$this->delete('site_subspecialty_drug');

		$this->addColumn('site_subspecialty_drug','display_order','tinyint(3) unsigned NOT NULL');
		$this->addColumn('site_subspecialty_drug','default','tinyint(1) unsigned NOT NULL DEFAULT 0');

		$this->delete('drug');

		$this->insert('drug',array('id'=>1,'name'=>'Intracameral Cefuroxime 1mg'));
		$this->insert('drug',array('id'=>2,'name'=>'S/C Cefuroxime'));
		$this->insert('drug',array('id'=>3,'name'=>'S/C Gentamicin 10/20mg'));
		$this->insert('drug',array('id'=>4,'name'=>'S/C Dexamethasone'));
		$this->insert('drug',array('id'=>5,'name'=>'S/C Betnosol'));

		if ($subspecialty) {
			$this->insert('site_subspecialty_drug',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'drug_id'=>1,'display_order'=>1,'default'=>1));
			$this->insert('site_subspecialty_drug',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'drug_id'=>2,'display_order'=>2,'default'=>0));
			$this->insert('site_subspecialty_drug',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'drug_id'=>3,'display_order'=>3,'default'=>0));
			$this->insert('site_subspecialty_drug',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'drug_id'=>4,'display_order'=>4,'default'=>0));
			$this->insert('site_subspecialty_drug',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'drug_id'=>5,'display_order'=>5,'default'=>0));
		}
	}

	public function down()
	{
		$this->delete('site_subspecialty_drug');

		$this->dropColumn('site_subspecialty_drug','default');
		$this->dropColumn('site_subspecialty_drug','display_order');

		$this->delete('drug');

		$this->insert('drug',array('id'=>1,'name'=>'Healon'));
		$this->insert('drug',array('id'=>2,'name'=>'Healon GV'));
		$this->insert('drug',array('id'=>3,'name'=>'Hypromellose'));
		$this->insert('drug',array('id'=>4,'name'=>'Miochol'));

		$this->dropTable('site_subspecialty_operative_device');
		$this->dropTable('operative_device');

		$specialty = $this->dbConnection->createCommand()->select('id')->from('specialty')->where('code=:code', array(':code'=>'OPH'))->queryRow();
		$subspecialty = $this->dbConnection->createCommand()->select('id')->from('subspecialty')->where('specialty_id=:specialty_id and ref_spec=:ref_spec', array(':specialty_id'=>$specialty['id'], ':ref_spec'=>'CA'))->queryRow();

		$this->insert('site_subspecialty_drug',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'drug_id'=>1));
		$this->insert('site_subspecialty_drug',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'drug_id'=>2));
		$this->insert('site_subspecialty_drug',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'drug_id'=>3));
		$this->insert('site_subspecialty_drug',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'drug_id'=>4));

		$this->createTable('site_subspecialty_drug_default',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'site_id' => 'int(10) unsigned NOT NULL',
				'subspecialty_id' => 'int(10) unsigned NOT NULL',
				'drug_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `site_subspecialty_drug_def_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `site_subspecialty_drug_def_created_user_id_fk` (`created_user_id`)',
				'KEY `site_subspecialty_drug_def_site_id` (`site_id`)',
				'KEY `site_subspecialty_drug_def_subspecialty_id` (`subspecialty_id`)',
				'KEY `site_subspecialty_drug_def_drug_id` (`drug_id`)',
				'CONSTRAINT `site_subspecialty_drug_def_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `site_subspecialty_drug_def_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `site_subspecialty_drug_def_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
				'CONSTRAINT `site_subspecialty_drug_def_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)',
				'CONSTRAINT `site_subspecialty_drug_def_drug_id_fk` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('site_subspecialty_drug_default',array('site_id'=>1,'subspecialty_id'=>4,'drug_id'=>3));
	}
}
