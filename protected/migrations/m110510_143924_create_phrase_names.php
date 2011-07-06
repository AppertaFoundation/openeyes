<?php

class m110510_143924_create_phrase_names extends CDbMigration
{
	public function up()
	{
		// create phrase_name table
		$this->createTable('phrase_name', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(255) COLLATE utf8_bin DEFAULT NULL',
			'PRIMARY KEY (`id`)'
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		// copy name values from phrase, phrase_by_firm, phrase_by_specialty into phrase_name

		$phrases = $this->dbConnection->createCommand()->select()->from('phrase')->queryAll();
		foreach ($phrases as $phrase) {
			$this->insert('phrase_name', array('name' => $phrase['name']));
		}
		$phrases = $this->dbConnection->createCommand()->select()->from('phrase_by_specialty')->queryAll();
		foreach ($phrases as $phrase) {
			$this->insert('phrase_name', array('name' => $phrase['name']));
		}
		$phrases = $this->dbConnection->createCommand()->select()->from('phrase_by_firm')->queryAll();
		foreach ($phrases as $phrase) {
			$this->insert('phrase_name', array('name' => $phrase['name']));
		}

		// create phrase_name_id column on phrase, phrase_by_firm, phrase_by_specialty
		$this->addColumn('phrase', 'phrase_name_id', 'int(10) unsigned');
		$this->addColumn('phrase_by_specialty', 'phrase_name_id', 'int(10) unsigned');
		$this->addColumn('phrase_by_firm', 'phrase_name_id', 'int(10) unsigned');

		// for phrase, phrase_by_firm, phrase_by_specialty, look up each name in phrase_name and put the id into phrase_name_id
		$phrases = $this->dbConnection->createCommand()->select()->from('phrase')->queryAll();
		foreach ($phrases as $phrase) {
			$phraseName = $this->dbConnection->createCommand()->select('id')->from('phrase_name')->where('name=:name', array(':name' => $phrase['name']))->queryRow();
			$this->update('phrase', array('phrase_name_id' => $phraseName['id']), "id=" . $phrase['id']);
		}
		$phrases = $this->dbConnection->createCommand()->select()->from('phrase_by_firm')->queryAll();
		foreach ($phrases as $phrase) {
			$phraseName = $this->dbConnection->createCommand()->select('id')->from('phrase_name')->where('name=:name', array(':name' => $phrase['name']))->queryRow();
			$this->update('phrase_by_firm', array('phrase_name_id' => $phraseName['id']), "id=" . $phrase['id']);
		}
		$phrases = $this->dbConnection->createCommand()->select()->from('phrase_by_specialty')->queryAll();
		foreach ($phrases as $phrase) {
			$phraseName = $this->dbConnection->createCommand()->select('id')->from('phrase_name')->where('name=:name', array(':name' => $phrase['name']))->queryRow();
			$this->update('phrase_by_specialty', array('phrase_name_id' => $phraseName['id']), "id=" . $phrase['id']);
		}

		// add the constraints now we've populated the new fields
		$this->alterColumn('phrase', 'phrase_name_id', 'int(10) unsigned not null');
		$this->alterColumn('phrase_by_firm', 'phrase_name_id', 'int(10) unsigned not null');
		$this->alterColumn('phrase_by_specialty', 'phrase_name_id', 'int(10) unsigned not null');

		$this->addForeignKey('phrase_phrase_name_id_fk', 'phrase', 'phrase_name_id', 'phrase_name', 'id');
		$this->addForeignKey('phrase_by_specialty_phrase_name_id_fk', 'phrase_by_specialty', 'phrase_name_id', 'phrase_name', 'id');
		$this->addForeignKey('phrase_by_firm_phrase_name_id_fk', 'phrase_by_firm', 'phrase_name_id', 'phrase_name', 'id');

		// nuke the name columns on phrase, phrase_by_firm, phrase_by_specialty
		$this->dropColumn('phrase', 'name');
		$this->dropColumn('phrase_by_firm', 'name');
		$this->dropColumn('phrase_by_specialty', 'name');
	}

	public function down()
	{
		$this->addColumn('phrase', 'name', 'varchar(255) COLLATE utf8_bin DEFAULT NULL');
		$this->addColumn('phrase_by_firm', 'name', 'varchar(255) COLLATE utf8_bin DEFAULT NULL');
		$this->addColumn('phrase_by_specialty', 'name', 'varchar(255) COLLATE utf8_bin DEFAULT NULL');

		$this->dropForeignKey('phrase_phrase_name_id_fk', 'phrase');
		$this->dropForeignKey('phrase_by_specialty_phrase_name_id_fk', 'phrase_by_specialty');
		$this->dropForeignKey('phrase_by_firm_phrase_name_id_fk', 'phrase_by_firm');

		$this->dropColumn('phrase', 'phrase_name_id');
		$this->dropColumn('phrase_by_specialty', 'phrase_name_id');
		$this->dropColumn('phrase_by_firm', 'phrase_name_id');

		$this->dropTable('phrase_name');
	}
}
