<?php

class m120324_112615_eyedraw_buckle_element extends CDbMigration
{
	public function up()
	{
		$this->createTable('et_ophtroperationnote_buckle_drainage_type', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(16) COLLATE utf8_bin NOT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_bdt_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_bdt_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_bdt_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_bdt_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('et_ophtroperationnote_buckle_drainage_type',array('id'=>1,'name'=>'None','display_order'=>1));
		$this->insert('et_ophtroperationnote_buckle_drainage_type',array('id'=>2,'name'=>'Suture needle','display_order'=>2));
		$this->insert('et_ophtroperationnote_buckle_drainage_type',array('id'=>3,'name'=>'Laser','display_order'=>3));
		$this->insert('et_ophtroperationnote_buckle_drainage_type',array('id'=>4,'name'=>'Cutdown','display_order'=>4));

		$this->createTable('et_ophtroperationnote_buckle', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'drainage_type_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'drain_haem' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'deep_suture' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'eyedraw' => 'varchar(1024) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_bu_drainage_type_id_fk` (`drainage_type_id`)',
				'KEY `et_ophtroperationnote_bu_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_bu_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_bu_drainage_type_id_fk` FOREIGN KEY (`drainage_type_id`) REFERENCES `et_ophtroperationnote_buckle_drainage_type` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_bu_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_bu_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$this->insert('element_type', array('name' => 'Buckle', 'class_name' => 'ElementBuckle', 'event_type_id' => $event_type['id'], 'display_order' => 5, 'default' => 0));

		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementBuckle'))->queryRow();

		$proc = $this->dbConnection->createCommand()->select('id')->from('proc')->where('snomed_term=:snomed',array(':snomed'=>'Scleral buckling'))->queryRow();
		$this->insert('et_ophtroperationnote_procedure_element',array('procedure_id'=>$proc['id'],'element_type_id'=>$element_type['id']));
	}

	public function down()
	{
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementBuckle'))->queryRow();

		$this->delete('et_ophtroperationnote_procedure_element','element_type_id='.$element_type['id']);

		$this->delete('element_type','event_type_id = '.$event_type['id']." and class_name = 'ElementBuckle'");

		$this->dropTable('et_ophtroperationnote_buckle');
		$this->dropTable('et_ophtroperationnote_buckle_drainage_type');
	}
}
