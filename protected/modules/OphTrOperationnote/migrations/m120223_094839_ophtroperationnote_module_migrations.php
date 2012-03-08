<?php

class m120223_094839_ophtroperationnote_module_migrations extends CDbMigration
{
	public function up()
	{
		// create element_procedurelist
		$this->createTable('element_procedurelist', array(
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
		$this->addForeignKey('element_procedurelist_last_modified_user_id_fk','element_procedurelist','last_modified_user_id','user','id');
		$this->addForeignKey('element_procedurelist_created_user_id_fk','element_procedurelist','created_user_id','user','id');
		$this->addForeignKey('element_procedurelist_surgeon_id_fk','element_procedurelist','surgeon_id','consultant','id');
		$this->addForeignKey('element_procedurelist_assistant_id_fk','element_procedurelist','assistant_id','contact','id');

		# (many to many relationship with procedures)


		// create an event_type for 'operationnote' if one doesn't already exist
		if (!$this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'operationnote'))->queryRow()) {
			$this->insert('event_type', array('name' => 'operationnote'));
		}

		// select the event_type id for 'operationnote'
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'operationnote'))->queryRow();

		// create an element_type for 'procedurelist' if one doesn't already exist
		if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name', array(':name'=>'procedurelist'))->queryRow()) {
			$this->insert('element_type', array('name' => 'procedurelist','class_name' => 'ElementProcedureList', 'event_type_id' => $event_type['id'], 'display_order' => 1));
		}

		// select the element_type_id for 'procedurelist'
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name', array(':name'=>'procedurelist'))->queryRow();
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
