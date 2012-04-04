<?php

class m120403_160907_opnote_drugs_element extends CDbMigration
{
	public function up()
	{
		$this->createTable('et_ophtroperationnote_drugs',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_drugs_type_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_drugs_type_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_drugs_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_drugs_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('et_ophtroperationnote_drugs_drug',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'et_ophtroperationnote_drugs_id' => 'int(10) unsigned NOT NULL',
				'drug_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_dd_drugs_id_fk` (`et_ophtroperationnote_drugs_id`)',
				'KEY `et_ophtroperationnote_dd_drug_id_fk` (`drug_id`)',
				'KEY `et_ophtroperationnote_dd_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_dd_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_dd_drugs_id_fk` FOREIGN KEY (`et_ophtroperationnote_drugs_id`) REFERENCES `et_ophtroperationnote_drugs` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_dd_drug_id_fk` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_dd_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_dd_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$this->insert('element_type', array('name' => 'Drugs', 'class_name' => 'ElementDrugs', 'event_type_id' => $event_type['id'], 'display_order' => 8, 'default' => 1));
	}

	public function down()
	{
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementDrugs'))->queryRow();

		$this->delete('element_type','id='.$element_type['id']);

		$this->dropTable('et_ophtroperationnote_drugs_drug');
		$this->dropTable('et_ophtroperationnote_drugs');
	}
}
