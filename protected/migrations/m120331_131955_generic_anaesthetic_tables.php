<?php

class m120331_131955_generic_anaesthetic_tables extends CDbMigration
{
	public function up()
	{
		$this->createTable('anaesthetist', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `anaesthetist_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `anaesthetist_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `anaesthetist_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `anaesthetist_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('anaesthetist',array('id'=>1,'name'=>'Anaesthetist','display_order'=>1));
		$this->insert('anaesthetist',array('id'=>2,'name'=>'Surgeon','display_order'=>2));
		$this->insert('anaesthetist',array('id'=>3,'name'=>'Nurse','display_order'=>3));
		$this->insert('anaesthetist',array('id'=>4,'name'=>'Anaesthetic technician','display_order'=>4));
		$this->insert('anaesthetist',array('id'=>5,'name'=>'Other','display_order'=>5));

		$this->createTable('element_type_anaesthetist', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'anaesthetist_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `element_type_anaesthetist_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_type_anaesthetist_created_user_id_fk` (`created_user_id`)',
				'KEY `element_type_anaesthetist_element_type_id_fk` (`element_type_id`)',
				'KEY `element_type_anaesthetist_anaesthetist_id_fk` (`anaesthetist_id`)',
				'CONSTRAINT `element_type_anaesthetist_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_anaesthetist_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_anaesthetist_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `element_type_anaesthetist_anaesthetist_id_fk` FOREIGN KEY (`anaesthetist_id`) REFERENCES `anaesthetist` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('anaesthetic_delivery', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_del_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_del_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_del_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_del_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('anaesthetic_delivery',array('id'=>1,'name'=>'Retrobulbar','display_order'=>1));
		$this->insert('anaesthetic_delivery',array('id'=>2,'name'=>'Peribulbar','display_order'=>2));
		$this->insert('anaesthetic_delivery',array('id'=>3,'name'=>'Subtenons','display_order'=>3));
		$this->insert('anaesthetic_delivery',array('id'=>4,'name'=>'Subconjunctival','display_order'=>4));
		$this->insert('anaesthetic_delivery',array('id'=>5,'name'=>'Topical','display_order'=>5));
		$this->insert('anaesthetic_delivery',array('id'=>6,'name'=>'Intracameral','display_order'=>6));
		$this->insert('anaesthetic_delivery',array('id'=>7,'name'=>'Other','display_order'=>7));

		$this->createTable('element_type_anaesthetic_delivery', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'anaesthetic_delivery_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `element_type_anaesthetic_delivery_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_type_anaesthetic_delivery_created_user_id_fk` (`created_user_id`)',
				'KEY `element_type_anaesthetic_delivery_element_type_id_fk` (`element_type_id`)',
				'KEY `element_type_anaesthetic_delivery_anaesthetic_delivery_id_fk` (`anaesthetic_delivery_id`)',
				'CONSTRAINT `element_type_anaesthetic_delivery_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_anaesthetic_delivery_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_anaesthetic_delivery_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `element_type_anaesthetic_delivery_anaesthetic_delivery_id_fk` FOREIGN KEY (`anaesthetic_delivery_id`) REFERENCES `anaesthetic_delivery` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('anaesthetic_agent', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_agent_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_agent_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_agent_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_agent_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('anaesthetic_agent',array('id'=>1,'name'=>'G Amethocaine','display_order'=>1));
		$this->insert('anaesthetic_agent',array('id'=>2,'name'=>'G Benoxinate','display_order'=>2));
		$this->insert('anaesthetic_agent',array('id'=>3,'name'=>'G Proxymetacaine','display_order'=>3));
		$this->insert('anaesthetic_agent',array('id'=>4,'name'=>'Lignocaine 1%','display_order'=>4));
		$this->insert('anaesthetic_agent',array('id'=>5,'name'=>'Bupivocaine','display_order'=>5));
		$this->insert('anaesthetic_agent',array('id'=>6,'name'=>'Hyalase','display_order'=>6));

		$this->createTable('element_type_anaesthetic_agent', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'anaesthetic_agent_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `element_type_anaesthetic_agent_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_type_anaesthetic_agent_created_user_id_fk` (`created_user_id`)',
				'KEY `element_type_anaesthetic_agent_element_type_id_fk` (`element_type_id`)',
				'KEY `element_type_anaesthetic_agent_anaesthetic_agent_id_fk` (`anaesthetic_agent_id`)',
				'CONSTRAINT `element_type_anaesthetic_agent_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_anaesthetic_agent_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_anaesthetic_agent_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `element_type_anaesthetic_agent_anaesthetic_agent_id_fk` FOREIGN KEY (`anaesthetic_agent_id`) REFERENCES `anaesthetic_agent` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('anaesthetic_complication', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_age_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_age_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_age_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_age_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('anaesthetic_complication',array('id'=>1,'name'=>'No complications','display_order'=>1));
		$this->insert('anaesthetic_complication',array('id'=>2,'name'=>'Eyelid haemorrhage/bruising','display_order'=>2));
		$this->insert('anaesthetic_complication',array('id'=>3,'name'=>'Conjunctival chemosis','display_order'=>3));
		$this->insert('anaesthetic_complication',array('id'=>4,'name'=>'Retro bulbar/peribulbar haemorrhage','display_order'=>4));
		$this->insert('anaesthetic_complication',array('id'=>5,'name'=>'Globe/optic nerve penetration','display_order'=>5));
		$this->insert('anaesthetic_complication',array('id'=>6,'name'=>'Inadequate akinesia','display_order'=>6));
		$this->insert('anaesthetic_complication',array('id'=>7,'name'=>'Patient pain - Mild','display_order'=>7));
		$this->insert('anaesthetic_complication',array('id'=>8,'name'=>'Patient pain - Moderate','display_order'=>8));
		$this->insert('anaesthetic_complication',array('id'=>9,'name'=>'Patient pain - Severe','display_order'=>9));
		$this->insert('anaesthetic_complication',array('id'=>10,'name'=>'Systemic problems','display_order'=>10));
		$this->insert('anaesthetic_complication',array('id'=>11,'name'=>'Operation cancelled due to complication','display_order'=>11));

		$this->createTable('element_type_anaesthetic_complication', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'anaesthetic_complication_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `element_type_ac_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_type_ac_created_user_id_fk` (`created_user_id`)',
				'KEY `element_type_ac_element_type_id_fk` (`element_type_id`)',
				'KEY `element_type_ac_anaesthetic_complication_id_fk` (`anaesthetic_complication_id`)',
				'CONSTRAINT `element_type_ac_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_ac_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_type_ac_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)',
				'CONSTRAINT `element_type_ac_anaesthetic_complication_id_fk` FOREIGN KEY (`anaesthetic_complication_id`) REFERENCES `anaesthetic_complication` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
	}

	public function down()
	{
		$this->dropTable('element_type_anaesthetic_complication');
		$this->dropTable('anaesthetic_complication');
		$this->dropTable('element_type_anaesthetic_agent');
		$this->dropTable('anaesthetic_agent');
		$this->dropTable('element_type_anaesthetic_delivery');
		$this->dropTable('anaesthetic_delivery');
		$this->dropTable('element_type_anaesthetist');
		$this->dropTable('anaesthetist');
	}
}
