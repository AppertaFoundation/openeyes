<?php

class m120327_140430_element_cataract extends CDbMigration
{
	public function up()
	{
		$this->createTable('et_ophtroperationnote_cataract_incision_site', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(16) COLLATE utf8_bin NOT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_cis_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_cis_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_cis_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_cis_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('et_ophtroperationnote_cataract_incision_site',array('id'=>1,'name'=>'Corneal','display_order'=>1));
		$this->insert('et_ophtroperationnote_cataract_incision_site',array('id'=>2,'name'=>'Limbal','display_order'=>2));
		$this->insert('et_ophtroperationnote_cataract_incision_site',array('id'=>3,'name'=>'Scleral','display_order'=>3));

		$this->createTable('et_ophtroperationnote_cataract_incision_type', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(16) COLLATE utf8_bin NOT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_cit_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_cit_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_cit_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_cit_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('et_ophtroperationnote_cataract_incision_type',array('id'=>1,'name'=>'Pocket','display_order'=>1));
		$this->insert('et_ophtroperationnote_cataract_incision_type',array('id'=>2,'name'=>'Section','display_order'=>2));

		$this->createTable('et_ophtroperationnote_cataract', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'incision_site_id' => 'int(10) unsigned NOT NULL DEFAULT 0',
				'length' => 'varchar(5) COLLATE utf8_bin NOT NULL',
				'meridian' => 'varchar(5) COLLATE utf8_bin NOT NULL',
				'incision_type_id' => 'int(10) unsigned NOT NULL DEFAULT 0',
				'eyedraw' => 'varchar(4096) COLLATE utf8_bin NOT NULL',
				'report' => 'varchar(4096) COLLATE utf8_bin NOT NULL',
				'wound_burn' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'iris_trauma' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'zonular_dialysis' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'pc_rupture' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'decentered_iol' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'iol_exchange' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'dropped_nucleus' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'op_cancelled' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'corneal_odema' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'iris_prolapse' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'zonular_rupture' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'vitreous_loss' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'iol_into_vitreous' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'other_iol_problem' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'choroidal_haem' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_ca_incision_site_id_fk` (`incision_site_id`)',
				'KEY `et_ophtroperationnote_ca_incision_type_id_fk` (`incision_type_id`)',
				'KEY `et_ophtroperationnote_ca_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_ca_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_ca_incision_site_id_fk` FOREIGN KEY (`incision_site_id`) REFERENCES `et_ophtroperationnote_cataract_incision_site` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_ca_incision_type_id_fk` FOREIGN KEY (`incision_type_id`) REFERENCES `et_ophtroperationnote_cataract_incision_type` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_ca_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_ca_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$this->insert('element_type', array('name' => 'Cataract', 'class_name' => 'ElementCataract', 'event_type_id' => $event_type['id'], 'display_order' => 6, 'default' => 0));

		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementCataract'))->queryRow();

		foreach (array('361191005','415089008','225703004','64854001','75170007','231752003','88234006','231751005','373416003','373415004','417709003','414470005','69724002','172542008','308694002') as $snomed_code) {
			$proc = $this->dbConnection->createCommand()->select('id')->from('proc')->where('snomed_code=:snomed',array(':snomed'=>$snomed_code))->queryRow();
			$this->insert('et_ophtroperationnote_procedure_element',array('procedure_id'=>$proc['id'],'element_type_id'=>$element_type['id']));
		}
	}

	public function down()
	{
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementCataract'))->queryRow();

		$this->delete('et_ophtroperationnote_procedure_element','element_type_id='.$element_type['id']);

		$this->delete('element_type','event_type_id = '.$event_type['id']." and class_name = 'ElementCataract'");

		$this->dropTable('et_ophtroperationnote_cataract');
		$this->dropTable('et_ophtroperationnote_cataract_incision_type');
		$this->dropTable('et_ophtroperationnote_cataract_incision_site');
	}
}
