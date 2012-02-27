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

class m110413_130143_insert_procedure_info extends CDbMigration
{
	public function up()
	{
		$service = $this->dbConnection->createCommand()
			->select('id')
			->from('service')
			->where('name=:name', array(':name'=>'Adnexal Service'))
			->queryRow();
		$this->insert('service_subsection', array(
			'service_id' => $service['id'],
			'name' => 'Lacrimal'
		));
		$this->insert('service_subsection', array(
			'service_id' => $service['id'],
			'name' => 'Lid'
		));
		$this->insert('service_subsection', array(
			'service_id' => $service['id'],
			'name' => 'Orbit'
		));
		$this->insert('service_subsection', array(
			'service_id' => $service['id'],
			'name' => 'Other'
		));
		$subsection = $this->dbConnection->createCommand()
			->select('id')
			->from('service_subsection')
			->where('name=:name', array(':name'=>'Lacrimal'))
			->queryRow();
		
		$this->insert('opcs_code', array(
			'name' => 'C25.4',
			'description' => 'Dacryocystorhinostomy NEC'
		));
		$opcs = $this->dbConnection->createCommand()
			->select('id')
			->from('opcs_code')
			->where('name=:name', array(':name'=>'C25.4'))
			->queryRow();
		
		$this->insert('procedure', array(
			'term' => 'Dacryocystorhinostomy',
			'short_format' => 'DCR',
			'default_duration' => 90,
			'service_subsection_id' => $subsection['id']
		));
		$procedure = $this->dbConnection->createCommand()
			->select('id')
			->from('procedure')
			->where('term=:term', array(':term'=>'Dacryocystorhinostomy'))
			->queryRow();
		
		$this->insert('procedure_opcs_assignment', array(
			'procedure_id' => $procedure['id'],
			'opcs_code_id' => $opcs['id']
		));
		
		$this->insert('opcs_code', array(
			'name' => 'C25.1',
			'description' => 'Canaliculodacryocystorhinostomy'
		));
		$opcs = $this->dbConnection->createCommand()
			->select('id')
			->from('opcs_code')
			->where('name=:name', array(':name'=>'C25.1'))
			->queryRow();
		
		$this->insert('procedure', array(
			'term' => 'DCR & retrotubes',
			'short_format' => 'CanaliculoDCR',
			'default_duration' => 120,
			'service_subsection_id' => $subsection['id']
		));
		$procedure = $this->dbConnection->createCommand()
			->select('id')
			->from('procedure')
			->where('term=:term', array(':term'=>'Dacryocystorhinostomy'))
			->queryRow();
		
		$this->insert('procedure_opcs_assignment', array(
			'procedure_id' => $procedure['id'],
			'opcs_code_id' => $opcs['id']
		));
	}

	public function down()
	{
		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 0;');
		$command->execute();
		
		$this->truncateTable('opcs_code');
		
		$this->truncateTable('procedure');
		
		$this->truncateTable('procedure_opcs_assignment');
		
		$service = $this->dbConnection->createCommand()
			->select('id')
			->from('service')
			->where('name=:name', array(':name'=>'Adnexal Service'))
			->queryRow();
		
		$this->delete('service_subsection', 'service_id=:id', 
			array(':id' => $service['id']));
		
		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 1;');
		$command->execute();
	}
}
