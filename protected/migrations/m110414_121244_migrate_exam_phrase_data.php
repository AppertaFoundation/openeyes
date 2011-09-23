<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

class m110414_121244_migrate_exam_phrase_data extends CDbMigration
{
	public function up()
	{
		$parts = Array(
			'History', 'PMH', 'POH', 'Medication', 'Allergies', 'Anterior segment', 'Posterior segment', 'Conclusion', 'Treatment',
			'Social history', 'HPC', 'FOH', 'Outcome', 'Timing', 'Severity', 'Onset', 'Duration', 'Site'
		);

		$section_type_letter = $this->dbConnection->createCommand()->select()->from('section_type')->where('name=:name', array(':name' => 'Letter'))->queryRow();
		$section_type_exam = $this->dbConnection->createCommand()->select()->from('section_type')->where('name=:name', array(':name' => 'Exam'))->queryRow();

		// create section_by_specialty entries
		$partsAndIds = Array();
		foreach ($parts as $part) {
			$this->insert('section', array('name' => $part, 'section_type_id' => $section_type_exam['id']));

			$pullback = $this->dbConnection->createCommand()->select()->from('section')->where('name=:name', array(':name' => $part))->queryRow();
			$partsAndIds[$pullback['id']] = $pullback['name'];
		}

		// select * from exam_phrase
		$examPhrases = $this->dbConnection->createCommand()->select()->from('exam_phrase')->queryAll();

		// insert into phrase_by_specialty
		foreach ($examPhrases as $examPhrase) {
			// old exam_phrase table: id, specialty_id, part, phrase, display_order
			// new phrase_by_specialty table: id, name, phrase, section_by_specialty_id, display_order, specialty_id
			// extract the part name from the number - we need to do this gymnastics so we don't have to rely on the table being empty
			$partName = $parts[$examPhrase['part']];

			// extract the part id from the part name
			$partId = array_search($partName, $partsAndIds);

			// insert the phrase_by_specialty entry
			$this->insert('phrase_by_specialty', array(
				'name' => $examPhrase['phrase'],
				'phrase' => $examPhrase['phrase'],
				'section_id' => $partId,
				'display_order' => $examPhrase['display_order'],
				'specialty_id' => $examPhrase['specialty_id']
			));
		}

		// drop the exam_phrase table
		$this->dropTable('exam_phrase');

		// drop the letter_phrase table
		$this->dropTable('letter_phrase');
	}

	public function down()
	{
		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 0;');
		$command->execute();

		$this->truncateTable('phrase_by_specialty');
		$this->truncateTable('phrase_by_firm');
		$this->truncateTable('section');

		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 1;');
		$command->execute();

		$this->createTable('letter_phrase', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'firm_id' => 'int(10) unsigned NOT NULL',
			'name' => 'varchar(64) COLLATE utf8_bin DEFAULT NULL',
			'phrase' => 'varchar(255) COLLATE utf8_bin DEFAULT NULL',
			'display_order' => "int(10) unsigned DEFAULT '0'",
			'section' => 'int(10) DEFAULT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `firm_id` (`firm_id`)'
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->addForeignKey('letter_phrase_ibfk_1', 'letter_phrase', 'firm_id', 'firm', 'id');

		$this->createTable('exam_phrase', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'specialty_id' => 'int(10) unsigned NOT NULL',
			'part' => 'int(10) unsigned NOT NULL',
			'phrase' => 'varchar(80)',
			'display_order' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)'
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->addForeignKey('specialty_id', 'exam_phrase', 'specialty_id', 'specialty', 'id');

		$phrases = array(
			// Specialty ID, Part, Phraes, Order
			"(8, 2, 'Congenital Cataract', 0)",
			"(8, 2, 'unnatural cataract', 1)",
			"(8, 11, 'Congenital Cataract', 0)",
			"(8, 11, 'unnatural cataract', 1)",
			"(8, 1, 'Stroke', 0)",
			"(8, 1, 'diabetes', 1)",
			"(8, 3, 'ibuprofen', 0)",
			"(8, 4, 'pollen', 0)",
			"(8, 9, 'drug use', 0)",
			"(8, 9, 'alcoholism', 0)",
			"(8, 0, 'Loss of vision', 0)",
			"(8, 0, 'Peripheral field loss', 1)",
			"(8, 0, 'Distortion of vision', 2)",
			"(8, 0, 'Central vision disturbance', 3)",
			"(8, 14, 'Mild', 0)",
			"(8, 14, 'Moderate', 1)",
			"(8, 14, 'Severe', 2)",
			"(8, 15, 'Gradual onset', 0)",
			"(8, 15, 'Sudden onset', 1)",
			"(8, 17, 'Right eye', 0)",
			"(8, 17, 'Left eye', 1)",
			"(8, 17, 'Both eyes', 2)",
			"(8, 16, '1 day', 0)",
			"(8, 16, '2-3 days', 1)",
			"(8, 16, '1 week', 2)",
			"(8, 16, '2 weeks', 3)",
			"(8, 16, '1 month', 4)",
			"(8, 16, '6 months', 5)",
			"(8, 16, '1 year or more', 6)",
			"(8, 7, 'Topical treatment', 0)",
			"(8, 7, 'Discharge', 1)",
			"(8, 8, 'Some treatment', 0)",
			"(8, 12, 'Decision to admit', 0)",
			"(8, 12, 'Discharge', 1)",
			"(8, 12, 'Review', 2)",
			"(8, 12, 'Refer to Cataract service', 3)",
			"(8, 12, 'Refer to Glaucoma service', 3)",
			"(8, 13, '1 day', 0)",
			"(8, 13, '2-3 days', 1)",
			"(8, 13, '1 week', 2)",
			"(8, 13, '2 weeks', 3)",
			"(8, 13, '1 month', 4)",
			"(8, 13, '6 months', 5)",
			"(8, 13, '1 year or more', 6)",
		);

		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 0;');
		$command->execute();

		$sql = "INSERT INTO `exam_phrase` (`specialty_id`, `part`, `phrase`, `display_order`) VALUES\n";
		foreach ($phrases as $values) {
			$sql .= $values;
			if ($values != end($phrases)) {
				$sql .= ", ";
			}
			$sql .= "\n";
		}
		$command = $this->dbConnection->createCommand($sql);
		echo "   > inserting into exam_phrase\n";
		$command->execute();


		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 1;');
		$command->execute();
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
