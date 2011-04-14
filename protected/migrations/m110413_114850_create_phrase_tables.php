<?php

class m110413_114850_create_phrase_tables extends CDbMigration
{
	public function up()
	{
		$this->createTable('section', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(255) COLLATE utf8_bin DEFAULT NULL',
			'PRIMARY KEY (`id`)'
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('phrase_by_specialty', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(255) COLLATE utf8_bin DEFAULT NULL',
			'phrase' => 'text COLLATE utf8_bin DEFAULT NULL',
			'section_id' => 'int(10) unsigned NOT NULL',
			'display_order' => 'int(10) unsigned',
			'specialty_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)'
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->addForeignKey('section_fk', 'phrase_by_specialty', 'section_id', 'section', 'id');
		$this->addForeignKey('specialty_fk', 'phrase_by_specialty', 'specialty_id', 'specialty', 'id');

		$this->createTable('phrase_by_firm', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(255) COLLATE utf8_bin DEFAULT NULL',
			'phrase' => 'text COLLATE utf8_bin DEFAULT NULL',
			'section_id' => 'int(10) unsigned NOT NULL',
			'display_order' => 'int(10) unsigned',
			'firm_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)'
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->addForeignKey('phrase_by_firm_section_fk', 'phrase_by_firm', 'section_id', 'section', 'id');
		$this->addForeignKey('phrase_by_firm_firm_fk', 'phrase_by_firm', 'firm_id', 'firm', 'id');

		// FIXME
		// $this->dropTable('exam_phrase');
		// $this->dropTable('letter_phrase');
	}

	public function down()
	{
		$this->dropTable('phrase_by_firm');
		$this->dropTable('phrase_by_specialty');
		$this->dropTable('section');
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
