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

class m110616_130154_create_element_letterout extends CDbMigration
{
	public function up()
	{
		$eventType = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'letterout'))->queryRow();

		$this->createTable('element_letterout', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'from_address' => 'text',
			'date' => 'varchar(255)',
			'dear' => 'varchar(255)',
			're' => 'varchar(255)',
			'value' => 'text',
			'to_address' => 'text',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->insert('element_type', array(
			'name' => 'Letter out',
			'class_name' => 'ElementLetterOut'
		));
		$elementType = $this->dbConnection->createCommand()
			->select('id')
			->from('element_type')
			->where('name=:name AND class_name=:class',
				array(':name'=>'Letter out', ':class'=>'ElementLetterOut'))
			->queryRow();

		$this->insert('possible_element_type', array(
			'event_type_id' => $eventType['id'],
			'element_type_id' => $elementType['id'],
			'num_views' => 1,
			'display_order' => 13
		));

		$possibleElement = $this->dbConnection->createCommand()
			->select('id')
			->from('possible_element_type')
			->where('event_type_id=:eventType AND 
				element_type_id=:elementType AND num_views=:num AND 
				`display_order`=:order',
				array(':eventType'=>$eventType['id'],':elementType'=>$elementType['id'],
					':num'=>1,':order'=>13))
			->queryRow();

		$specialties = $this->dbConnection->createCommand()->select()->from('specialty')->queryAll();
		foreach ($specialties as $specialty) {
			/*
			$this->insert('site_element_type', array(
				'possible_element_type_id' => $possibleElement['id'],
				'specialty_id' => $phrase['id'], // Medical retina
				'view_number' => 1,
				'required' => 1,
				'first_in_episode' => 1
			));
			*/
			$this->insert('site_element_type', array(
				'possible_element_type_id' => $possibleElement['id'],
				'specialty_id' => $specialty['id'], // Medical retina
				'view_number' => 1,
				'required' => 1,
				'first_in_episode' => 0
			));
		}
	}
	public function down()
	{
		$eventType = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'letterout'))->queryRow();
		$elementType = $this->dbConnection->createCommand()
			->select('id')
			->from('element_type')
			->where('name=:name AND class_name=:class',
				array(':name'=>'Letter out', ':class'=>'ElementLetterOut'))
			->queryRow();

		if ($elementType) {
			$possibleElement = $this->dbConnection->createCommand()
				->select('id')
				->from('possible_element_type')
				->where('event_type_id=:eventType AND 
					element_type_id=:elementType AND num_views=:num AND 
					`display_order`=:order',
					array(':eventType'=>$eventType['id'],':elementType'=>$elementType['id'],
						':num'=>1,':order'=>13))
				->queryRow();
			$this->delete('site_element_type', 'possible_element_type_id = :id',
				array(':id' => $possibleElement['id'])
			);

			$this->delete('possible_element_type', 'element_type_id = :id',
				array(':id' => $elementType['id'])
			);
		}

		$this->delete('element_type', 'class_name = :class',
			array(':class' => 'ElementLetterOut')
		);

		$this->dropTable('element_letterout');
	}
}
