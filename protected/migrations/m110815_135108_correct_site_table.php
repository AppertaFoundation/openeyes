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

class m110815_135108_correct_site_table extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('site', 'telephopne');
		$this->addColumn('site', 'telephone', 'VARCHAR(255) NOT NULL');

		$this->update('site', array(
			'address1' => '1 road street',
			'postcode' => 'A1 2BC',
			'telephone' => '020 7123 4567',
			'fax' => '020 7987 6543'
		), 'id = :id', array(':id' => 1));

		$this->insert('section', array(
			'name' => 'LetterOut',
			'section_type_id' => 1
		));

		$section = $this->dbConnection->createCommand()->select('id')->from('section')->where('name=:name', array(':name' => 'LetterOut'))->queryRow();

		$this->insert('phrase_name', array(
			'name' => 'Consultant'
		));
		$this->insert('phrase_name', array(
			'name' => 'Contact Number'
		));
		$this->insert('phrase_name', array(
			'name' => 'Time Limit'
		));

		$phrase1 = $this->dbConnection->createCommand()->select('id')->from('phrase_name')->where('name=:name', array(':name' => 'Consultant'))->queryRow();
		$phrase2 = $this->dbConnection->createCommand()->select('id')->from('phrase_name')->where('name=:name', array(':name' => 'Contact Number'))->queryRow();
		$phrase3 = $this->dbConnection->createCommand()->select('id')->from('phrase_name')->where('name=:name', array(':name' => 'Time Limit'))->queryRow();
		$firms = $this->dbConnection->createCommand()
			->select('id, name')
			->from('firm')
			->queryAll();

		foreach ($firms as $firm) {
			$this->insert('phrase_by_firm', array(
				'firm_id' => $firm['id'],
				'phrase' => 'Consultant for firm ' . $firm['name'],
				'section_id' => $section['id'],
				'phrase_name_id' => $phrase1['id']
			));
			$this->insert('phrase_by_firm', array(
				'firm_id' => $firm['id'],
				'phrase' => 'Contact Number for firm ' . $firm['name'],
				'section_id' => $section['id'],
				'phrase_name_id' => $phrase2['id']
			));
			$this->insert('phrase_by_firm', array(
				'firm_id' => $firm['id'],
				'phrase' => 'Time Limit for firm ' . $firm['name'],
				'section_id' => $section['id'],
				'phrase_name_id' => $phrase3['id']
			));
		}
	}

	public function down()
	{
		$this->dropColumn('site', 'telephone');
		$this->addColumn('site', 'telephopne', 'VARCHAR(255) NOT NULL');

                $section = $this->dbConnection->createCommand()->select('id')->from('section')->where('name=:name', array(':name' => 'LetterOut'))->queryRow();

                $this->delete('phrase_by_firm', 'section_id = :section_id',
                        array(':section_id' => $section['id'])
                );
		$this->delete('section', 'id = :id',
			array(':id' => $section['id'])
		);
                $this->delete('phrase_name', 'name = :name', array(
                        ':name' => 'Consultant'
                ));
                $this->delete('phrase_name', 'name = :name', array(
                        ':name' => 'Contact Number'
                ));
                $this->delete('phrase_name', 'name = :name', array(
                        ':name' => 'Time Limit'
                ));
	}
}
