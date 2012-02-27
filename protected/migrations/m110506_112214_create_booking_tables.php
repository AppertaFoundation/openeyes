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

class m110506_112214_create_booking_tables extends CDbMigration
{
	public function up()
	{
		$this->createTable('site', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(255) COLLATE utf8_bin NOT NULL',
			'PRIMARY KEY (`id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$this->insert('site', array(
			'name' => 'Moorfields City Road'
		));
		
		$this->createTable('ward', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'site_id' => 'int(10) unsigned NOT NULL',
			'name' => 'varchar(255) COLLATE utf8_bin NOT NULL',
			'restriction' => 'tinyint(1) DEFAULT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `site_id` (`site_id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('theatre', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(255) COLLATE utf8_bin DEFAULT NULL',
			'site_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `site_id` (`site_id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$this->insert('theatre', array(
			'name' => 'Theatre 7',
			'site_id' => 1
		));
		
		$this->createTable('sequence', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'theatre_id' => 'int(10) unsigned NOT NULL',
			'start_date' => 'DATE NOT NULL',
			'start_time' => 'TIME NOT NULL',
			'end_time' => 'TIME NOT NULL',
			'end_date' => 'DATE DEFAULT NULL',
			'frequency' => 'tinyint(1) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `theatre_id` (`theatre_id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$this->createTable('sequence_firm_assignment', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'sequence_id' => 'int(10) unsigned NOT NULL',
			'firm_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `firm_id` (`firm_id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$this->createTable('session', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'sequence_id' => 'int(10) unsigned NOT NULL',
			'date' => 'DATE NOT NULL',
			'start_time' => 'TIME NOT NULL',
			'end_time' => 'TIME NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `sequence_id` (`sequence_id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$this->createTable('appointment', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'element_operation_id' => 'int(10) unsigned NOT NULL',
			'session_id' => 'int(10) unsigned NOT NULL',
			'display_order' => 'int(10) NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `element_operation_id` (`element_operation_id`)',
			'KEY `session_id` (`session_id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$elementType = $this->dbConnection->createCommand()
			->select('id')
			->from('element_type')
			->where('name=:name', array(':name'=>'Appointment'))
			->queryRow();
		
		if (!empty($elementType)) {
			$possibleElementTypes = $this->dbConnection->createCommand()
				->select('id')
				->from('possible_element_type')
				->where('element_type_id=:id', array(':id'=>$elementType['id']))
				->queryAll();
			
			foreach ($possibleElementTypes as $possible) {
				$this->delete('site_element_type', 'possible_element_type_id=:id', 
					array(':id' => $possible['id']));
				$this->delete('possible_element_type', 'id=:id', 
					array(':id' => $possible['id']));
			}

			$this->delete('element_type', 'id=:id', array(':id' => $elementType['id']));		

			$this->dropForeignKey('element_appointment_ibfk_1','element_appointment');
			$this->dropTable('element_appointment');
		}
		
		$this->addForeignKey('ward_1','ward','site_id','site','id');
		
		$this->addForeignKey('theatre_1','theatre','site_id','site','id');
		
		$this->addForeignKey('sequence_1','sequence','theatre_id','theatre','id');
		
		$this->addForeignKey('session_1','session','sequence_id','sequence','id');
		
		$this->addForeignKey('sequence_firm_assignment_1','sequence_firm_assignment','sequence_id','sequence','id');
		$this->addForeignKey('sequence_firm_assignment_2','sequence_firm_assignment','firm_id','firm','id');
		
		$this->addForeignKey('appointment_1','appointment','element_operation_id','element_operation','id');
		$this->addForeignKey('appointment_2','appointment','session_id','session','id');
	}

	public function down()
	{
		if ($this->dbConnection->schema->getTable('{{appointment}}')) {
			$this->dropForeignKey('appointment_1','appointment');
			$this->dropForeignKey('appointment_2','appointment');
		}
		
		$this->dropForeignKey('sequence_firm_assignment_1','sequence_firm_assignment');
		$this->dropForeignKey('sequence_firm_assignment_2','sequence_firm_assignment');
		
		$this->dropForeignKey('session_1','session');
		
		$this->dropForeignKey('sequence_1','sequence');
		
		$this->dropForeignKey('theatre_1','theatre');
		
		$this->dropForeignKey('ward_1','ward');
		
		$this->dropTable('appointment');
		$this->dropTable('session');
		$this->dropTable('sequence_firm_assignment');
		$this->dropTable('sequence');
		$this->dropTable('theatre');
		$this->dropTable('ward');
		$this->dropTable('site');
	}
}
