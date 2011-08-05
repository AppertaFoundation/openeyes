<?php

class m110802_081502_add_extra_sequences extends CDbMigration
{
	public function up()
	{
		$firm = $this->dbConnection->createCommand()
			->select('id')
			->from('firm')
			->where('name LIKE :name', array(':name'=>'Aylward%'))
			->queryRow();
		
		$this->insert('theatre', array(
			'name'=>'Example theatre 2',
			'site_id'=>1
		));

		$theatre = $this->dbConnection->createCommand()
			->select('id')
			->from('theatre')
			->where('name=:name', array(':name'=>'Example theatre 2'))
			->queryRow();
		
		for ($i = 3; $i <= 7; $i++) {
			$startDate = "2011-01-0{$i}";
			$startTime = '08:30';
			$endTime = '13:00';
			$this->insert('sequence', array(
				'theatre_id' => $theatre['id'],
				'start_date' => $startDate,
				'start_time' => $startTime,
				'end_time' => $endTime,
				'end_date' => null,
				'repeat_interval' => Sequence::FREQUENCY_1WEEK
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
				'repeat_interval' => Sequence::FREQUENCY_1WEEK
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
		
		$this->insert('theatre', array(
			'name'=>'Example theatre 3',
			'site_id'=>1
		));

		$theatre = $this->dbConnection->createCommand()
			->select('id')
			->from('theatre')
			->where('name=:name', array(':name'=>'Example theatre 3'))
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
		
		$startDate = '2011-01-06';
		$startTime = '08:30';
		$endTime = '13:00';
		$this->insert('sequence', array(
			'theatre_id' => $theatre['id'],
			'start_date' => $startDate,
			'start_time' => $startTime,
			'end_time' => $endTime,
			'end_date' => null,
			'repeat_interval' => Sequence::FREQUENCY_2WEEKS
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
		
		$this->delete('theatre', 'name!=:name', array(':name'=>'Example theatre'));
		
		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 1;');
		$command->execute();
	}
}