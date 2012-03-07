<?php

class m120223_094838_ophtroperationnote_procedurelist extends CDbMigration
{
	public function up()
	{
		// create element_procedurelist
		$this->createTable('element_procedurelist', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'surgeon_id' => int(10) unsigned,
			'assistant_id' => int(10) unsigned,
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

		// (many to many relationship with procedures)
	}

	public function down()
	{
		$this->dropTable('element_procedurelist');
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
