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

class m110517_104543_create_sequence_data extends CDbMigration
{
	public function up()
	{
		$theatre = $this->dbConnection->createCommand()
			->select('id')
			->from('theatre')
			->where('name=:name', array(':name'=>'Theatre 7'))
			->queryRow();
		
		$firm = $this->dbConnection->createCommand()
			->select('id')
			->from('firm')
			->where('name LIKE :name', array(':name'=>'Aylward%'))
			->queryRow();
		
		if (empty($theatre)) {
			$this->insert('theatre', array(
				'name'=>'Theatre 7',
				'site_id'=>1
			));
			
			$theatre = $this->dbConnection->createCommand()
				->select('id')
				->from('theatre')
				->where('name=:name', array(':name'=>'Theatre 7'))
				->queryRow();
		}
		
		$startDate = '2011-01-05';
		$startTime = '08:30';
		$endTime = '13:00';
		$this->insert('sequence', array(
			'theatre_id' => $theatre['id'],
			'start_date' => $startDate,
			'start_time' => $startTime,
			'end_time' => $endTime,
			'end_date' => null,
			'frequency' => Sequence::FREQUENCY_1WEEK
		));
		$sequence = $this->dbConnection->createCommand()
			->select('id')
			->from('sequence')
			->where('theatre_id=:theatre AND start_date=:date AND 
				start_time=:start AND end_time=:end', 
				array(':theatre'=>$theatre['id'],':date'=>$startDate,
					':start'=>$startTime,':end'=>$endTime))
			->queryRow();
		
		$this->insert('sequence_firm_assignment', array(
			'sequence_id'=>$sequence['id'],
			'firm_id'=>$firm['id'],
		));
		
		$startTime = '13:30';
		$endTime = '18:00';
		$this->insert('sequence', array(
			'theatre_id' => $theatre['id'],
			'start_date' => $startDate,
			'start_time' => $startTime,
			'end_time' => $endTime,
			'end_date' => null,
			'frequency' => Sequence::FREQUENCY_1WEEK
		));
		$sequence = $this->dbConnection->createCommand()
			->select('id')
			->from('sequence')
			->where('theatre_id=:theatre AND start_date=:date AND 
				start_time=:start AND end_time=:end', 
				array(':theatre'=>$theatre['id'],':date'=>$startDate,
					':start'=>$startTime,':end'=>$endTime))
			->queryRow();
		
		$this->insert('sequence_firm_assignment', array(
			'sequence_id'=>$sequence['id'],
			'firm_id'=>$firm['id'],
		));
		
		$this->insert('theatre', array(
			'name'=>'Theatre 1',
			'site_id'=>1
		));

		$theatre = $this->dbConnection->createCommand()
			->select('id')
			->from('theatre')
			->where('name=:name', array(':name'=>'Theatre 1'))
			->queryRow();
		
		$firm = $this->dbConnection->createCommand()
			->select('id')
			->from('firm')
			->where('name LIKE :name', array(':name'=>'Egan%'))
			->queryRow();
		
		if (empty($firm)) {
			$this->insert('firm', array(
				'service_specialty_assignment_id' => 4,
				'pas_code' => 'MREC',
				'name' => 'Egan Firm'
			));
			
			$firm = $this->dbConnection->createCommand()
				->select('id')
				->from('firm')
				->where('name LIKE :name', array(':name'=>'Egan%'))
				->queryRow();
		}
		
		$startDate = '2011-01-04';
		$startTime = '08:30';
		$endTime = '13:00';
		$this->insert('sequence', array(
			'theatre_id' => $theatre['id'],
			'start_date' => $startDate,
			'start_time' => $startTime,
			'end_time' => $endTime,
			'end_date' => null,
			'frequency' => Sequence::FREQUENCY_1WEEK
		));
		$sequence = $this->dbConnection->createCommand()
			->select('id')
			->from('sequence')
			->where('theatre_id=:theatre AND start_date=:date AND 
				start_time=:start AND end_time=:end', 
				array(':theatre'=>$theatre['id'],':date'=>$startDate,
					':start'=>$startTime,':end'=>$endTime))
			->queryRow();
		
		$this->insert('sequence_firm_assignment', array(
			'sequence_id'=>$sequence['id'],
			'firm_id'=>$firm['id'],
		));
		
		$this->insert('theatre', array(
			'name'=>'Theatre 8',
			'site_id'=>1
		));

		$theatre = $this->dbConnection->createCommand()
			->select('id')
			->from('theatre')
			->where('name=:name', array(':name'=>'Theatre 8'))
			->queryRow();
		
		$startTime = '08:30';
		$endTime = '13:00';
		$this->insert('sequence', array(
			'theatre_id' => $theatre['id'],
			'start_date' => $startDate,
			'start_time' => $startTime,
			'end_time' => $endTime,
			'end_date' => null,
			'frequency' => Sequence::FREQUENCY_2WEEKS
		));
		$sequence = $this->dbConnection->createCommand()
			->select('id')
			->from('sequence')
			->where('theatre_id=:theatre AND start_date=:date AND 
				start_time=:start AND end_time=:end', 
				array(':theatre'=>$theatre['id'],':date'=>$startDate,
					':start'=>$startTime,':end'=>$endTime))
			->queryRow();
		
		$this->insert('sequence_firm_assignment', array(
			'sequence_id'=>$sequence['id'],
			'firm_id'=>$firm['id'],
		));
	}

	public function down()
	{
		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 0;');
		$command->execute();
		
		$this->truncateTable('sequence_firm_assignment');
		$this->truncateTable('sequence');
		
		$this->delete('theatre', 'name!=:name', array(':name'=>'Theatre 7'));
		
		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 1;');
		$command->execute();
	}
}
