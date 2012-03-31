<?php

class m120331_133427_procedurelist_new_anaesthetic_fields extends CDbMigration
{
	public function up()
	{
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementProcedureList'))->queryRow();

		$this->addColumn('et_ophtroperationnote_procedurelist','anaesthetist_id','integer(10) unsigned NOT NULL DEFAULT 1');
		$this->createIndex('et_ophtroperationnote_procedurelist_anaesthetist_fk','et_ophtroperationnote_procedurelist','anaesthetist_id');
		$this->addForeignKey('et_ophtroperationnote_procedurelist_anaesthetist_fk','et_ophtroperationnote_procedurelist','anaesthetist_id','anaesthetist','id');

		$this->insert('element_type_anaesthetist',array('element_type_id'=>$element_type['id'],'anaesthetist_id'=>1,'display_order'=>1));
		$this->insert('element_type_anaesthetist',array('element_type_id'=>$element_type['id'],'anaesthetist_id'=>2,'display_order'=>2));
		$this->insert('element_type_anaesthetist',array('element_type_id'=>$element_type['id'],'anaesthetist_id'=>3,'display_order'=>3));
		$this->insert('element_type_anaesthetist',array('element_type_id'=>$element_type['id'],'anaesthetist_id'=>4,'display_order'=>4));
		$this->insert('element_type_anaesthetist',array('element_type_id'=>$element_type['id'],'anaesthetist_id'=>5,'display_order'=>5));

		$this->addColumn('et_ophtroperationnote_procedurelist','anaesthetic_delivery_id','integer(10) unsigned NOT NULL DEFAULT 1');
		$this->createIndex('et_ophtroperationnote_procedurelist_anaesthetic_delivery_fk','et_ophtroperationnote_procedurelist','anaesthetic_delivery_id');
		$this->addForeignKey('et_ophtroperationnote_procedurelist_anaesthetic_delivery_fk','et_ophtroperationnote_procedurelist','anaesthetic_delivery_id','anaesthetic_delivery','id');

		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$element_type['id'],'anaesthetic_delivery_id'=>1,'display_order'=>1));
		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$element_type['id'],'anaesthetic_delivery_id'=>2,'display_order'=>2));
		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$element_type['id'],'anaesthetic_delivery_id'=>3,'display_order'=>3));
		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$element_type['id'],'anaesthetic_delivery_id'=>4,'display_order'=>4));
		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$element_type['id'],'anaesthetic_delivery_id'=>5,'display_order'=>5));
		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$element_type['id'],'anaesthetic_delivery_id'=>6,'display_order'=>6));
		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$element_type['id'],'anaesthetic_delivery_id'=>7,'display_order'=>7));

		$this->createTable('et_ophtroperationnote_procedurelist_anaesthetic_agent', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'procedurelist_id' => 'int(10) unsigned NOT NULL',
				'anaesthetic_agent_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_paa_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_paa_created_user_id_fk` (`created_user_id`)',
				'KEY `et_ophtroperationnote_paa_procedurelist_id_fk` (`procedurelist_id`)',
				'KEY `et_ophtroperationnote_paa_anaesthetic_agent_id_fk` (`anaesthetic_agent_id`)',
				'CONSTRAINT `et_ophtroperationnote_paa_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_paa_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_paa_procedurelist_id_fk` FOREIGN KEY (`procedurelist_id`) REFERENCES `et_ophtroperationnote_procedurelist` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_paa_anaesthetic_agent_id_fk` FOREIGN KEY (`anaesthetic_agent_id`) REFERENCES `anaesthetic_agent` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('element_type_anaesthetic_agent',array('element_type_id'=>$element_type['id'],'anaesthetic_agent_id'=>1,'display_order'=>1));
		$this->insert('element_type_anaesthetic_agent',array('element_type_id'=>$element_type['id'],'anaesthetic_agent_id'=>2,'display_order'=>2));
		$this->insert('element_type_anaesthetic_agent',array('element_type_id'=>$element_type['id'],'anaesthetic_agent_id'=>3,'display_order'=>3));
		$this->insert('element_type_anaesthetic_agent',array('element_type_id'=>$element_type['id'],'anaesthetic_agent_id'=>4,'display_order'=>4));
		$this->insert('element_type_anaesthetic_agent',array('element_type_id'=>$element_type['id'],'anaesthetic_agent_id'=>5,'display_order'=>5));
		$this->insert('element_type_anaesthetic_agent',array('element_type_id'=>$element_type['id'],'anaesthetic_agent_id'=>6,'display_order'=>6));

		$this->createTable('et_ophtroperationnote_procedurelist_anaesthetic_complication', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'procedurelist_id' => 'int(10) unsigned NOT NULL',
				'anaesthetic_complication_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_pac_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_pac_created_user_id_fk` (`created_user_id`)',
				'KEY `et_ophtroperationnote_pac_procedurelist_id_fk` (`procedurelist_id`)',
				'KEY `et_ophtroperationnote_pac_anaesthetic_complication_id_fk` (`anaesthetic_complication_id`)',
				'CONSTRAINT `et_ophtroperationnote_pac_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_pac_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_pac_procedurelist_id_fk` FOREIGN KEY (`procedurelist_id`) REFERENCES `et_ophtroperationnote_procedurelist` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_pac_anaesthetic_complication_id_fk` FOREIGN KEY (`anaesthetic_complication_id`) REFERENCES `anaesthetic_complication` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$element_type['id'],'anaesthetic_complication_id'=>1,'display_order'=>1));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$element_type['id'],'anaesthetic_complication_id'=>2,'display_order'=>2));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$element_type['id'],'anaesthetic_complication_id'=>3,'display_order'=>3));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$element_type['id'],'anaesthetic_complication_id'=>4,'display_order'=>4));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$element_type['id'],'anaesthetic_complication_id'=>5,'display_order'=>5));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$element_type['id'],'anaesthetic_complication_id'=>6,'display_order'=>6));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$element_type['id'],'anaesthetic_complication_id'=>7,'display_order'=>7));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$element_type['id'],'anaesthetic_complication_id'=>8,'display_order'=>8));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$element_type['id'],'anaesthetic_complication_id'=>9,'display_order'=>9));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$element_type['id'],'anaesthetic_complication_id'=>10,'display_order'=>10));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$element_type['id'],'anaesthetic_complication_id'=>11,'display_order'=>11));

		$this->addColumn('et_ophtroperationnote_procedurelist','anaesthetic_comment','varchar(1024) COLLATE utf8_bin NULL');
	}

	public function down()
	{
		$this->dropColumn('et_ophtroperationnote_procedurelist','anaesthetic_comment');

		$this->dropTable('et_ophtroperationnote_procedurelist_anaesthetic_complication');
		$this->dropTable('et_ophtroperationnote_procedurelist_anaesthetic_agent');

		$this->dropForeignKey('et_ophtroperationnote_procedurelist_anaesthetic_delivery_fk','et_ophtroperationnote_procedurelist');
		$this->dropIndex('et_ophtroperationnote_procedurelist_anaesthetic_delivery_fk','et_ophtroperationnote_procedurelist');
		$this->dropColumn('et_ophtroperationnote_procedurelist','anaesthetic_delivery_id');

		$this->dropForeignKey('et_ophtroperationnote_procedurelist_anaesthetist_fk','et_ophtroperationnote_procedurelist');
		$this->dropIndex('et_ophtroperationnote_procedurelist_anaesthetist_fk','et_ophtroperationnote_procedurelist');
		$this->dropColumn('et_ophtroperationnote_procedurelist','anaesthetist_id');

		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementProcedureList'))->queryRow();

		$this->delete('element_type_anaesthetist','element_type_id='.$element_type['id']);
		$this->delete('element_type_anaesthetic_delivery','element_type_id='.$element_type['id']);
		$this->delete('element_type_anaesthetic_agent','element_type_id='.$element_type['id']);
		$this->delete('element_type_anaesthetic_complication','element_type_id='.$element_type['id']);
	}
}
