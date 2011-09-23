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

class m110707_144059_modify_letter_template extends CDbMigration
{
	public function up()
	{
		$this->dropForeignKey('letter_template_ibfk_2', 'letter_template');

		$this->dropColumn('letter_template', 'to');
		$this->addColumn('letter_template', 'send_to', 'INT(10) UNSIGNED NOT NULL');

		$this->addForeignKey('letter_template_ibfk_2','letter_template','send_to','contact_type','id');

		// Add new fields to 'user' needed for 'from' in element_letter_out
		$this->addColumn('user', 'title', 'VARCHAR(40) NOT NULL');
		$this->addColumn('user', 'qualifications', 'VARCHAR(255) NOT NULL');
		$this->addColumn('user', 'role', 'VARCHAR(255) NOT NULL');

		$this->dropForeignKey('patient_fk','diagnosis');
		$this->dropForeignKey('user_fk','diagnosis');
		$this->dropForeignKey('disorder_fk','diagnosis');

		$this->dropTable('diagnosis');

		$this->createTable('element_diagnosis', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'disorder_id' => 'int(10) unsigned NOT NULL',
			'eye' => "tinyint(1) unsigned DEFAULT '0'",
			'PRIMARY KEY (`id`)',
			'KEY `event_id` (`event_id`)',
			'KEY `disorder_id` (`disorder_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->addForeignKey(
			'element_diagnosis_fk_1','element_diagnosis','event_id','event','id');

		$this->addForeignKey(
			'element_diagnosis_fk_2','element_diagnosis','disorder_id','disorder','id');

		$this->insert('element_type',
			array(
				'name' => 'Diagnosis',
				'class_name' => 'ElementDiagnosis'
			)
		);

		// extract element type
		$elementType = $this->dbConnection->createCommand()
			->select('id')
			->from('element_type')
			->where("name='Diagnosis'")
			->queryRow();

		$eventType = $this->dbConnection->createCommand()
			->select('id')
			->from('event_type')
			->where("name='operation'")
			->queryRow();

		// create possible element type
		$this->insert('possible_element_type',
			array(
				'event_type_id' => $eventType['id'],
				'element_type_id' => $elementType['id'],
				'num_views' => 1,
				'display_order' => 1
			)
		);

		// extract possible element type
		$possibleElementType = $this->dbConnection->createCommand()
			->select('id')
			->from('possible_element_type')
			->where('event_type_id=:event_type_id and element_type_id=:element_type_id',
				array(':event_type_id'=>$eventType['id'],':element_type_id'=>$elementType['id']))
			->queryRow();

		$specialties = $this->dbConnection->createCommand()
			->select('id')
			->from('specialty')
			->queryAll();

		// create site element type entries
		foreach ($specialties as $specialty) {
			$this->insert('site_element_type', array(
				'possible_element_type_id' => $possibleElementType['id'],
				'specialty_id' => $specialty['id'],
				'view_number' => 1,
				'required' => 1,
				'first_in_episode' => 0
			));

			$this->insert('site_element_type', array(
				'possible_element_type_id' => $possibleElementType['id'],
				'specialty_id' => $specialty['id'],
				'view_number' => 1,
				'required' => 1,
				'first_in_episode' => 1
			));
		}

		$this->addColumn('element_operation', 'decision_date', 'DATE NOT NULL');

		$this->update('user', array('qualifications' => 'admin qualification', 'role' => 'admin role', 'title' => 'Mr'),
			'id = :id', array(':id' => 1));

		$this->addColumn('consultant', 'pas_code', 'char(4)');

		$this->update('phrase_by_firm', array('phrase' => 'His principal diagnosis is [epd] in eye [eps]'),
			'id = :id', array(':id' => 3));
	}

	public function down()
	{
		$this->dropForeignKey('letter_template_ibfk_2', 'letter_template');

		$this->dropColumn('letter_template', 'send_to');
		$this->addColumn('letter_template', 'to', 'INT(10) UNSIGNED NOT NULL');

		$this->addForeignKey('letter_template_ibfk_2','letter_template','to','contact_type','id');

		$this->dropColumn('user', 'title');
		$this->dropColumn('user', 'qualifications');
		$this->dropColumn('user', 'role');

		$this->dropForeignKey('element_diagnosis_fk_1', 'element_diagnosis');
		$this->dropForeignKey('element_diagnosis_fk_2', 'element_diagnosis');

		$this->dropTable('element_diagnosis');

		$this->createTable('diagnosis', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'patient_id' => 'int(10) unsigned NOT NULL',
			'user_id' => 'int(10) unsigned NOT NULL',
			'disorder_id' => 'int(10) unsigned NOT NULL',
			'datetime' => 'datetime NOT NULL',
			'site' => "tinyint(1) unsigned DEFAULT '0'",
			'PRIMARY KEY (`id`)',
			'KEY `patient_id` (`patient_id`)',
			'KEY `user_id` (`user_id`)',
			'KEY `disorder_id` (`disorder_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->addForeignKey(
			'patient_fk','diagnosis','patient_id','patient','id');
		$this->addForeignKey(
			'user_fk','diagnosis','user_id','user','id');
		$this->addForeignKey(
			'disorder_fk','diagnosis','disorder_id','disorder','id');

		// extract element type
		$elementType = $this->dbConnection->createCommand()
			->select('id')
			->from('element_type')
			->where('name=\'Diagnosis\'')
			->queryRow();

		$eventType = $this->dbConnection->createCommand()
			->select('id')
			->from('event_type')
			->where('name=\'operation\'')
			->queryRow();

		$possibleElementType = $this->dbConnection->createCommand()
			->select('id')
			->from('possible_element_type')
			->where('event_type_id=:event_type_id and element_type_id=:element_type_id',
				array(':event_type_id'=>$eventType['id'],':element_type_id'=>$elementType['id']))
			->queryRow();

		// remove site_element_type entries
		$this->delete('site_element_type', 'possible_element_type_id = :possible_element_type_id',
			array(':possible_element_type_id' => $possibleElementType['id'])
		);

		// remove possible_element_type entries
		$this->delete('possible_element_type', 'id = :id',
			array(':id' => $possibleElementType['id'])
		);

		$this->delete('element_type', 'name=\'Diagnosis\'');

		$this->dropColumn('element_operation', 'decision_date');

		$this->dropColumn('consultant', 'pas_code');
	}
}
