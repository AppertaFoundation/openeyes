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

class m110329_144911_create_site_element_types_for_element_iop extends CDbMigration
{
	public function up()
	{
		// extract relevant entries
		$specialty = $this->dbConnection->createCommand()
			->select('id')
			->from('specialty')
			->where('name=:name', 
				array(':name'=>'Medical Retinal'))
			->queryRow();
		$possibleElementType = $this->dbConnection->createCommand()
			->select('id')
			->from('possible_element_type')
			->where('event_type_id=:eventType AND 
				element_type_id=:elementType',
				array(':eventType'=>1,':elementType'=>13))
			->queryRow();		

		// create site element type entries
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

	public function down()
	{
		$specialty = $this->dbConnection->createCommand()
			->select('id')
			->from('specialty')
			->where('name=:name', 
				array(':name'=>'Medical Retinal'))
			->queryRow();
		$possibleElementType = $this->dbConnection->createCommand()
			->select('id')
			->from('possible_element_type')
			->where('event_type_id=:eventType AND 
				element_type_id=:elementType',
				array(':eventType'=>1,':elementType'=>13))
			->queryRow();

		// remove site_element_type entries
		$this->delete('site_element_type', 'possible_element_type_id = :possible_element_type_id and specialty_id = :specialty_id',
			array(':possible_element_type_id' => $possibleElementType['id'], ':specialty_id' => $specialty['id'])
		);

	}
}
