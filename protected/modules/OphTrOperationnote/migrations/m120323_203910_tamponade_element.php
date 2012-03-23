<?php

class m120323_203910_tamponade_element extends CDbMigration
{
	public function up()
	{
		$this->createTable('et_ophtroperationnote_gas_type', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(5) COLLATE utf8_bin NOT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_gas_type_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_gas_type_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_gas_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_gas_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('et_ophtroperationnote_gas_type',array('id'=>1,'name'=>'Air','display_order'=>1));
		$this->insert('et_ophtroperationnote_gas_type',array('id'=>2,'name'=>'SF6','display_order'=>2));
		$this->insert('et_ophtroperationnote_gas_type',array('id'=>3,'name'=>'C2F6','display_order'=>3));
		$this->insert('et_ophtroperationnote_gas_type',array('id'=>4,'name'=>'C3F8','display_order'=>4));
		$this->insert('et_ophtroperationnote_gas_type',array('id'=>5,'name'=>'1000cS oil','display_order'=>5));
		$this->insert('et_ophtroperationnote_gas_type',array('id'=>6,'name'=>'2000cS oil','display_order'=>6));
		$this->insert('et_ophtroperationnote_gas_type',array('id'=>7,'name'=>'5000cS oil','display_order'=>7));
		$this->insert('et_ophtroperationnote_gas_type',array('id'=>8,'name'=>'Densiron','display_order'=>8));
		$this->insert('et_ophtroperationnote_gas_type',array('id'=>9,'name'=>'Oxane HD','display_order'=>9));
		$this->insert('et_ophtroperationnote_gas_type',array('id'=>10,'name'=>'PFCL','display_order'=>10));

		$this->createTable('et_ophtroperationnote_tamponade', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'gas_type_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'percentage' => 'int(10) unsigned NOT NULL DEFAULT 0',
				'volume' => 'int(10) unsigned NOT NULL DEFAULT 0',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_tp_gas_type_id_fk` (`gas_type_id`)',
				'KEY `et_ophtroperationnote_tp_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_tp_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_tp_gas_type_id_fk` FOREIGN KEY (`gas_type_id`) REFERENCES `et_ophtroperationnote_gas_type` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_tp_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_tp_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$this->insert('element_type', array('name' => 'Tamponade', 'class_name' => 'ElementTamponade', 'event_type_id' => $event_type['id'], 'display_order' => 4, 'default' => 0));

		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementTamponade'))->queryRow();

		$proc = $this->dbConnection->createCommand()->select('id')->from('proc')->where('snomed_term=:snomed',array(':snomed'=>'Injection of gas'))->queryRow();
		$this->insert('et_ophtroperationnote_procedure_element',array('procedure_id'=>$proc['id'],'element_type_id'=>$element_type['id']));
	}

	public function down()
	{
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementTamponade'))->queryRow();

		$this->delete('et_ophtroperationnote_procedure_element','element_type_id='.$element_type['id']);

		$this->delete('element_type','event_type_id = '.$event_type['id']." and class_name = 'ElementTamponade'");

		$this->dropTable('et_ophtroperationnote_tamponade');
		$this->dropTable('et_ophtroperationnote_gas_type');
	}
}
