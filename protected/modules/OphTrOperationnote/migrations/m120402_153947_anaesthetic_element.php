<?php

class m120402_153947_anaesthetic_element extends CDbMigration
{
	public function up()
	{
		$this->createTable('et_ophtroperationnote_anaesthetic',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'anaesthetic_type_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'anaesthetist_id' => 'integer(10) unsigned NOT NULL DEFAULT 1',
				'anaesthetic_delivery_id' => 'integer(10) unsigned NOT NULL DEFAULT 1',
				'anaesthetic_comment' => 'varchar(1024) COLLATE utf8_bin DEFAULT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_ana_type_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_ana_type_created_user_id_fk` (`created_user_id`)',
				'KEY `et_ophtroperationnote_ana_anaesthetic_type_id_fk` (`anaesthetic_type_id`)',
				'KEY `et_ophtroperationnote_ana_anaesthetist_id_fk` (`anaesthetist_id`)',
				'KEY `et_ophtroperationnote_ana_anaesthetic_delivery_id_fk` (`anaesthetic_delivery_id`)',
				'CONSTRAINT `et_ophtroperationnote_ana_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_ana_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_ana_anaesthetic_type_id_fk` FOREIGN KEY (`anaesthetic_type_id`) REFERENCES `anaesthetic_type` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_ana_anaesthetist_id_fk` FOREIGN KEY (`anaesthetist_id`) REFERENCES `anaesthetist` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_ana_anaesthetic_delivery_id_fk` FOREIGN KEY (`anaesthetic_delivery_id`) REFERENCES `anaesthetic_delivery` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->dropForeignKey('et_ophtroperationnote_procedurelist_anaesthetist_fk','et_ophtroperationnote_procedurelist');
		$this->dropIndex('et_ophtroperationnote_procedurelist_anaesthetist_fk','et_ophtroperationnote_procedurelist');
		$this->dropColumn('et_ophtroperationnote_procedurelist','anaesthetist_id');

		$this->dropForeignKey('et_ophtroperationnote_procedurelist_anaesthetic_delivery_fk','et_ophtroperationnote_procedurelist');
		$this->dropIndex('et_ophtroperationnote_procedurelist_anaesthetic_delivery_fk','et_ophtroperationnote_procedurelist');
		$this->dropColumn('et_ophtroperationnote_procedurelist','anaesthetic_delivery_id');

		$this->dropForeignKey('et_ophtroperationnote_procedurelist_anaesthetic_type_id_fk','et_ophtroperationnote_procedurelist');
		$this->dropIndex('et_ophtroperationnote_procedurelist_anaesthetic_type_id_fk','et_ophtroperationnote_procedurelist');
		$this->dropColumn('et_ophtroperationnote_procedurelist','anaesthetic_type_id');

		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$this->insert('element_type', array('name' => 'Anaesthetic', 'class_name' => 'ElementAnaesthetic', 'event_type_id' => $event_type['id'], 'display_order' => 7, 'default' => 1));
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementAnaesthetic'))->queryRow();
		$pl_element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementProcedureList'))->queryRow();

		$this->delete('element_type_anaesthetic_type','element_type_id='.$pl_element_type['id']);
		$this->delete('element_type_anaesthetist','element_type_id='.$pl_element_type['id']);
		$this->delete('element_type_anaesthetic_delivery','element_type_id='.$pl_element_type['id']);
		$this->delete('element_type_anaesthetic_agent','element_type_id='.$pl_element_type['id']);
		$this->delete('element_type_anaesthetic_complication','element_type_id='.$pl_element_type['id']);

		$this->insert('element_type_anaesthetist',array('element_type_id'=>$element_type['id'],'anaesthetist_id'=>1,'display_order'=>1));
		$this->insert('element_type_anaesthetist',array('element_type_id'=>$element_type['id'],'anaesthetist_id'=>2,'display_order'=>2));
		$this->insert('element_type_anaesthetist',array('element_type_id'=>$element_type['id'],'anaesthetist_id'=>3,'display_order'=>3));
		$this->insert('element_type_anaesthetist',array('element_type_id'=>$element_type['id'],'anaesthetist_id'=>4,'display_order'=>4));
		$this->insert('element_type_anaesthetist',array('element_type_id'=>$element_type['id'],'anaesthetist_id'=>5,'display_order'=>5));

		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$element_type['id'],'anaesthetic_delivery_id'=>1,'display_order'=>1));
		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$element_type['id'],'anaesthetic_delivery_id'=>2,'display_order'=>2));
		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$element_type['id'],'anaesthetic_delivery_id'=>3,'display_order'=>3));
		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$element_type['id'],'anaesthetic_delivery_id'=>4,'display_order'=>4));
		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$element_type['id'],'anaesthetic_delivery_id'=>5,'display_order'=>5));
		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$element_type['id'],'anaesthetic_delivery_id'=>6,'display_order'=>6));
		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$element_type['id'],'anaesthetic_delivery_id'=>7,'display_order'=>7));

		$this->renameTable('et_ophtroperationnote_procedurelist_anaesthetic_agent','et_ophtroperationnote_anaesthetic_anaesthetic_agent');

		$this->insert('element_type_anaesthetic_agent',array('element_type_id'=>$element_type['id'],'anaesthetic_agent_id'=>1,'display_order'=>1));
		$this->insert('element_type_anaesthetic_agent',array('element_type_id'=>$element_type['id'],'anaesthetic_agent_id'=>2,'display_order'=>2));
		$this->insert('element_type_anaesthetic_agent',array('element_type_id'=>$element_type['id'],'anaesthetic_agent_id'=>3,'display_order'=>3));
		$this->insert('element_type_anaesthetic_agent',array('element_type_id'=>$element_type['id'],'anaesthetic_agent_id'=>4,'display_order'=>4));
		$this->insert('element_type_anaesthetic_agent',array('element_type_id'=>$element_type['id'],'anaesthetic_agent_id'=>5,'display_order'=>5));
		$this->insert('element_type_anaesthetic_agent',array('element_type_id'=>$element_type['id'],'anaesthetic_agent_id'=>6,'display_order'=>6));

		$this->renameTable('et_ophtroperationnote_procedurelist_anaesthetic_complication','et_ophtroperationnote_anaesthetic_anaesthetic_complication');

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

		$this->dropColumn('et_ophtroperationnote_procedurelist','anaesthetic_comment');

		$to = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name',array(':name'=>'Topical'))->queryRow();
		$lac = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name',array(':name'=>'LAC'))->queryRow();
		$la = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name',array(':name'=>'LA'))->queryRow();
		$las = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name',array(':name'=>'LAS'))->queryRow();
		$ga = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name',array(':name'=>'GA'))->queryRow();

		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$element_type['id'],'anaesthetic_type_id'=>$to['id'],'display_order'=>1));
		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$element_type['id'],'anaesthetic_type_id'=>$la['id'],'display_order'=>2));
		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$element_type['id'],'anaesthetic_type_id'=>$lac['id'],'display_order'=>3));
		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$element_type['id'],'anaesthetic_type_id'=>$las['id'],'display_order'=>4));
		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$element_type['id'],'anaesthetic_type_id'=>$ga['id'],'display_order'=>5));
	}

	public function down()
	{
		$this->dropTable('et_ophtroperationnote_anaesthetic');

		$this->addColumn('et_ophtroperationnote_procedurelist','anaesthetic_comment','varchar(1024) COLLATE utf8_bin DEFAULT NULL');

		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementAnaesthetic'))->queryRow();
		$pl_element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementProcedureList'))->queryRow();

		$this->delete('element_type_anaesthetic_type','element_type_id='.$element_type['id']);
		$this->delete('element_type_anaesthetist','element_type_id='.$element_type['id']);
		$this->delete('element_type_anaesthetic_delivery','element_type_id='.$element_type['id']);
		$this->delete('element_type_anaesthetic_agent','element_type_id='.$element_type['id']);
		$this->delete('element_type_anaesthetic_complication','element_type_id='.$element_type['id']);

		$this->addColumn('et_ophtroperationnote_procedurelist','anaesthetist_id','integer(10) unsigned NOT NULL DEFAULT 1');
		$this->createIndex('et_ophtroperationnote_procedurelist_anaesthetist_fk','et_ophtroperationnote_procedurelist','anaesthetist_id');
		$this->addForeignKey('et_ophtroperationnote_procedurelist_anaesthetist_fk','et_ophtroperationnote_procedurelist','anaesthetist_id','anaesthetist','id');

		$this->insert('element_type_anaesthetist',array('element_type_id'=>$pl_element_type['id'],'anaesthetist_id'=>1,'display_order'=>1));
		$this->insert('element_type_anaesthetist',array('element_type_id'=>$pl_element_type['id'],'anaesthetist_id'=>2,'display_order'=>2));
		$this->insert('element_type_anaesthetist',array('element_type_id'=>$pl_element_type['id'],'anaesthetist_id'=>3,'display_order'=>3));
		$this->insert('element_type_anaesthetist',array('element_type_id'=>$pl_element_type['id'],'anaesthetist_id'=>4,'display_order'=>4));
		$this->insert('element_type_anaesthetist',array('element_type_id'=>$pl_element_type['id'],'anaesthetist_id'=>5,'display_order'=>5));

		$this->addColumn('et_ophtroperationnote_procedurelist','anaesthetic_delivery_id','integer(10) unsigned NOT NULL DEFAULT 1');
		$this->createIndex('et_ophtroperationnote_procedurelist_anaesthetic_delivery_fk','et_ophtroperationnote_procedurelist','anaesthetic_delivery_id');
		$this->addForeignKey('et_ophtroperationnote_procedurelist_anaesthetic_delivery_fk','et_ophtroperationnote_procedurelist','anaesthetic_delivery_id','anaesthetic_delivery','id');

		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_delivery_id'=>1,'display_order'=>1));
		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_delivery_id'=>2,'display_order'=>2));
		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_delivery_id'=>3,'display_order'=>3));
		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_delivery_id'=>4,'display_order'=>4));
		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_delivery_id'=>5,'display_order'=>5));
		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_delivery_id'=>6,'display_order'=>6));
		$this->insert('element_type_anaesthetic_delivery',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_delivery_id'=>7,'display_order'=>7));

		$this->insert('element_type_anaesthetic_agent',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_agent_id'=>1,'display_order'=>1));
		$this->insert('element_type_anaesthetic_agent',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_agent_id'=>2,'display_order'=>2));
		$this->insert('element_type_anaesthetic_agent',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_agent_id'=>3,'display_order'=>3));
		$this->insert('element_type_anaesthetic_agent',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_agent_id'=>4,'display_order'=>4));
		$this->insert('element_type_anaesthetic_agent',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_agent_id'=>5,'display_order'=>5));
		$this->insert('element_type_anaesthetic_agent',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_agent_id'=>6,'display_order'=>6));

		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_complication_id'=>1,'display_order'=>1));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_complication_id'=>2,'display_order'=>2));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_complication_id'=>3,'display_order'=>3));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_complication_id'=>4,'display_order'=>4));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_complication_id'=>5,'display_order'=>5));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_complication_id'=>6,'display_order'=>6));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_complication_id'=>7,'display_order'=>7));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_complication_id'=>8,'display_order'=>8));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_complication_id'=>9,'display_order'=>9));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_complication_id'=>10,'display_order'=>10));
		$this->insert('element_type_anaesthetic_complication',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_complication_id'=>11,'display_order'=>11));

		$to = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name',array(':name'=>'Topical'))->queryRow();
		$lac = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name',array(':name'=>'LAC'))->queryRow();
		$la = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name',array(':name'=>'LA'))->queryRow();
		$las = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name',array(':name'=>'LAS'))->queryRow();
		$ga = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name',array(':name'=>'GA'))->queryRow();

		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_type_id'=>$to['id'],'display_order'=>1));
		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_type_id'=>$la['id'],'display_order'=>2));
		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_type_id'=>$lac['id'],'display_order'=>3));
		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_type_id'=>$las['id'],'display_order'=>4));
		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$pl_element_type['id'],'anaesthetic_type_id'=>$ga['id'],'display_order'=>5));

		$this->renameTable('et_ophtroperationnote_anaesthetic_anaesthetic_agent','et_ophtroperationnote_procedurelist_anaesthetic_agent');
		$this->renameTable('et_ophtroperationnote_anaesthetic_anaesthetic_complication','et_ophtroperationnote_procedurelist_anaesthetic_complication');

		$this->delete('element_type', 'id='.$element_type['id']);
	}
}
