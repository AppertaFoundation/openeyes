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

class m110329_130059_create_element_registered_blind extends CDbMigration
{
    public function up()
    {
		$this->createTable('element_registered_blind', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'status' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$this->insert('element_type', array(
			'name' => 'Registered blind',
			'class_name' => 'ElementRegisteredBlind'
		));
		$elementType = $this->dbConnection->createCommand()
			->select('id')
			->from('element_type')
			->where('name=:name AND class_name=:class', 
				array(':name'=>'Registered blind', ':class'=>'ElementRegisteredBlind'))
			->queryRow();

		$this->insert('possible_element_type', array(
			'event_type_id' => 1,
			'element_type_id' => $elementType['id'],
			'num_views' => 1,
			'order' => 11
		));

		$possibleElement = $this->dbConnection->createCommand()
			->select('id')
			->from('possible_element_type')
			->where('event_type_id=:eventType AND 
				element_type_id=:elementType AND num_views=:num AND 
				`order`=:order',
				array(':eventType'=>1,':elementType'=>$elementType['id'],
					':num'=>1,':order'=>11))
			->queryRow();		

		$this->insert('site_element_type', array(
			'possible_element_type_id' => $possibleElement['id'],
			'specialty_id' => 8, // Medical retina
			'view_number' => 1,
			'required' => 1,
			'first_in_episode' => 1
		));
		$this->insert('site_element_type', array(
			'possible_element_type_id' => $possibleElement['id'],
			'specialty_id' => 8, // Medical retina
			'view_number' => 1,
			'required' => 1,
			'first_in_episode' => 0
		));
    }

    public function down()
    {
		$elementType = $this->dbConnection->createCommand()
			->select('id')
			->from('element_type')
			->where('name=:name AND class_name=:class', 
				array(':name'=>'Registered blind', ':class'=>'ElementRegisteredBlind'))
			->queryRow();

		if ($elementType) {
			$possibleElement = $this->dbConnection->createCommand()
				->select('id')
				->from('possible_element_type')
				->where('event_type_id=:eventType AND 
					element_type_id=:elementType AND num_views=:num AND 
					`order`=:order',
					array(':eventType'=>1,':elementType'=>$elementType['id'],
						':num'=>1,':order'=>11))
				->queryRow();
			$this->delete('site_element_type', 'possible_element_type_id = :id',
				array(':id' => $possibleElement['id'])
			);

			$this->delete('possible_element_type', 'element_type_id = :id',
				array(':id' => $elementType['id'])
			);
		}
		
		$this->delete('element_type', 'class_name = :class', 
			array(':class' => 'ElementRegisteredBlind')
		);

		$this->dropTable('element_registered_blind');
    }
}
