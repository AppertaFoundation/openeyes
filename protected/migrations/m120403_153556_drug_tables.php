<?php

class m120403_153556_drug_tables extends CDbMigration
{
	public function up()
	{
		$this->createTable('drug',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `drug_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `drug_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `drug_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `drug_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('drug',array('id'=>1,'name'=>'Healon'));
		$this->insert('drug',array('id'=>2,'name'=>'Healon GV'));
		$this->insert('drug',array('id'=>3,'name'=>'Hypromellose'));
		$this->insert('drug',array('id'=>4,'name'=>'Miochol'));

		$this->createTable('site_subspecialty_drug',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'site_id' => 'int(10) unsigned NOT NULL',
				'subspecialty_id' => 'int(10) unsigned NOT NULL',
				'drug_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `site_subspecialty_drug_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `site_subspecialty_drug_created_user_id_fk` (`created_user_id`)',
				'KEY `site_subspecialty_drug_site_id` (`site_id`)',
				'KEY `site_subspecialty_drug_subspecialty_id` (`subspecialty_id`)',
				'KEY `site_subspecialty_drug_drug_id` (`drug_id`)',
				'CONSTRAINT `site_subspecialty_drug_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `site_subspecialty_drug_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `site_subspecialty_drug_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
				'CONSTRAINT `site_subspecialty_drug_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)',
				'CONSTRAINT `site_subspecialty_drug_drug_id_fk` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$specialty = $this->dbConnection->createCommand()->select('id')->from('specialty')->where('code=:code', array(':code'=>'OPH'))->queryRow();
		$subspecialty = $this->dbConnection->createCommand()->select('id')->from('subspecialty')->where('specialty_id=:specialty_id and ref_spec=:ref_spec', array(':specialty_id'=>$specialty['id'], ':ref_spec'=>'CA'))->queryRow();

		if ($specialty && $subspecialty) {
			$this->insert('site_subspecialty_drug',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'drug_id'=>1));
			$this->insert('site_subspecialty_drug',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'drug_id'=>2));
			$this->insert('site_subspecialty_drug',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'drug_id'=>3));
			$this->insert('site_subspecialty_drug',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'drug_id'=>4));
		}
	}

	public function down()
	{
		$this->dropTable('site_subspecialty_drug');
		$this->dropTable('drug');
	}
}
