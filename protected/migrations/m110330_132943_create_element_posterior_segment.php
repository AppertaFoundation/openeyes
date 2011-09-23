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

class m110330_132943_create_element_posterior_segment extends CDbMigration
{
	public function up()
	{
		$this->addColumn('element_posterior_segment', 'description_left', 'text');
		$this->addColumn('element_posterior_segment', 'description_right', 'text');
		$this->addColumn('element_posterior_segment', 'image_string_left', 'text');
		$this->addColumn('element_posterior_segment', 'image_string_right', 'text');
		// extract the relevant entries
		$eventType = $this->dbConnection->createCommand()
			->select('id')
			->from('event_type')
			->where('name=:name', array(':name'=>'examination'))
			->queryRow();
		$specialties = $this->dbConnection->createCommand()
			->select('id')
			->from('specialty')
			->where('name != :name', array(':name'=>'Adnexal'))
			->queryAll();

		// extract element type
		$elementType = $this->dbConnection->createCommand()
			->select('id')
			->from('element_type')
			->where('name=:name',array(':name'=>'Posterior segment'))
			->queryRow();

		// create possible element type
		$this->insert('possible_element_type',
			array(
				'event_type_id' => $eventType['id'],
				'element_type_id' => $elementType['id'],
				'num_views' => 1,
				'order' => 1
			)
		);

		// extract possible element type
		$possibleElementType = $this->dbConnection->createCommand()
			->select('id')
			->from('possible_element_type')
			->where('event_type_id=:event_type_id and element_type_id=:element_type_id',
				array(':event_type_id'=>$eventType['id'],':element_type_id'=>$elementType['id']))
			->queryRow();

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
	}

	public function down()
	{
		$this->dropColumn('element_posterior_segment', 'description_left');
		$this->dropColumn('element_posterior_segment', 'description_right');
		$this->dropColumn('element_posterior_segment', 'image_string_left');
		$this->dropColumn('element_posterior_segment', 'image_string_right');

		// extract the relevant entries
		$eventType = $this->dbConnection->createCommand()
			->select('id')
			->from('event_type')
			->where('name=:name', array(':name'=>'examination'))
			->queryRow();
		$specialties = $this->dbConnection->createCommand()
			->select('id')
			->from('specialty')
			->where('name != :name', array(':name'=>'Adnexal'))
			->queryAll();
		$elementType = $this->dbConnection->createCommand()
			->select('id')
			->from('element_type')
			->where('name=:name',array(':name'=>'Posterior segment'))
			->queryRow();

		$possibleElementType = $this->dbConnection->createCommand()
			->select('id')
			->from('possible_element_type')
			->where('event_type_id=:event_type_id and element_type_id=:element_type_id',
				array(':event_type_id'=>$eventType['id'],':element_type_id'=>$elementType['id']))
			->queryRow();

		// remove site_element_type entries
		foreach ($specialties as $specialty) {
			$this->delete('site_element_type', 'possible_element_type_id = :possible_element_type_id and specialty_id = :specialty_id',
				array(':possible_element_type_id' => $possibleElementType['id'], ':specialty_id' => $specialty['id'])
			);
		}

		// remove possible_element_type entries
		$this->delete('possible_element_type', 'id = :id',
			array(':id' => $possibleElementType['id'])
		);
	}
}
