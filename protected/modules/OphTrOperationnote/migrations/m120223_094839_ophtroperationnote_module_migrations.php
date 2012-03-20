<?php

class m120223_094839_ophtroperationnote_module_migrations extends CDbMigration
{
	public function up()
	{
		// create et_ophtroperationnote_procedurelist
		$this->createTable('et_ophtroperationnote_procedurelist', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'surgeon_id' => 'int(10) unsigned',
			'assistant_id' => 'int(10) unsigned',
			'anaesthetic_type' => 'varchar(255)',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		$this->addForeignKey('et_ophtroperationnote_procedurelist_last_modified_user_id_fk','et_ophtroperationnote_procedurelist','last_modified_user_id','user','id');
		$this->addForeignKey('et_ophtroperationnote_procedurelist_created_user_id_fk','et_ophtroperationnote_procedurelist','created_user_id','user','id');
		$this->addForeignKey('et_ophtroperationnote_procedurelist_surgeon_id_fk','et_ophtroperationnote_procedurelist','surgeon_id','consultant','id');
		$this->addForeignKey('et_ophtroperationnote_procedurelist_assistant_id_fk','et_ophtroperationnote_procedurelist','assistant_id','contact','id');

		# (many to many relationship with procedures)


		// create an event_type for 'operationnote' if one doesn't already exist
		if (!$this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow()) {
			$group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name',array(':name'=>'Treatment events'))->queryRow();
			$this->insert('event_type', array('name' => 'Operation note','event_group_id' => $group['id']));
		}

		// select the event_type id for 'operationnote'
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();

		// create an element_type for 'Procedure list' if one doesn't already exist
		if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name', array(':name'=>'Procedure list'))->queryRow()) {
			$this->insert('element_type', array('name' => 'Procedure list','class_name' => 'ElementProcedureList', 'event_type_id' => $event_type['id'], 'display_order' => 1));
		}

		// select the element_type_id for 'Procedure list'
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name', array(':name'=>'Procedure list'))->queryRow();
	}

	public function down()
	{
		echo "m120223_094829_ophtroperationnote_eventtype_relationships does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}
