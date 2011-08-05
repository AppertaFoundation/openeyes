<?php

class m110413_114850_create_phrase_tables extends CDbMigration
{
	public function up()
	{
		// section and section_type tables
		$this->createTable('section', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(255) COLLATE utf8_bin DEFAULT NULL',
			'section_type_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)'
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->createTable('section_type', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(255) COLLATE utf8_bin DEFAULT NULL',
			'PRIMARY KEY (`id`)'
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->insert('section_type', array('name' => 'Letter'));
		$this->insert('section_type', array('name' => 'Exam'));

		// phrase_by_specialty
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
		$this->addForeignKey('phrase_by_specialty_section_fk', 'phrase_by_specialty', 'section_id', 'section', 'id');
		$this->addForeignKey('phrase_by_specialty_specialty_fk', 'phrase_by_specialty', 'specialty_id', 'specialty', 'id');

		// phrase_by_firm
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

		// phrase
		$this->createTable('phrase', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(255) COLLATE utf8_bin DEFAULT NULL',
			'phrase' => 'text COLLATE utf8_bin DEFAULT NULL',
			'section_id' => 'int(10) unsigned NOT NULL',
			'display_order' => 'int(10) unsigned',
			'PRIMARY KEY (`id`)'
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
	}

	public function down()
	{
		$this->dropTable('phrase_by_firm');
		$this->dropTable('phrase_by_specialty');
		$this->dropTable('phrase');
		$this->dropTable('section_type');
		$this->dropTable('section');
	}
}
