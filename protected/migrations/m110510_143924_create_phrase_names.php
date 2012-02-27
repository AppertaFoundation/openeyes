<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

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
