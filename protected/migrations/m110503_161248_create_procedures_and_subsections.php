<?php

class m110503_161248_create_procedures_and_subsections extends CDbMigration
{
	public function up()
	{
		$services = $this->dbConnection->createCommand()
			->select('id, name')
			->from('service')
			->where('name!=:name', array(':name'=>'Adnexal Service'))
			->queryAll();
		
		foreach ($services as $service) {
			$this->insert('service_subsection', array(
				'service_id'=>$service['id'],
				'name'=>'All'
			));
			$subsection = $this->dbConnection->createCommand()
				->select('id')
				->from('service_subsection')
				->where('service_id=:id', array(':id'=>$service['id']))
				->queryRow();
			
			$this->insert('procedure', array(
				'term' => $service['name'] . ' Procedure 1',
				'short_format' => 'P1' . $subsection['id'],
				'default_duration' => 30,
				'service_subsection_id' => $subsection['id'],
			));
			$this->insert('procedure', array(
				'term' => $service['name'] . ' Procedure 2',
				'short_format' => 'P2' . $subsection['id'],
				'default_duration' => 30,
				'service_subsection_id' => $subsection['id'],
			));
			$this->insert('procedure', array(
				'term' => $service['name'] . ' Procedure 3',
				'short_format' => 'P3' . $subsection['id'],
				'default_duration' => 30,
				'service_subsection_id' => $subsection['id'],
			));
			$this->insert('procedure', array(
				'term' => $service['name'] . ' Procedure 4',
				'short_format' => 'P4' . $subsection['id'],
				'default_duration' => 30,
				'service_subsection_id' => $subsection['id'],
			));
			$this->insert('procedure', array(
				'term' => $service['name'] . ' Procedure 5',
				'short_format' => 'P5' . $subsection['id'],
				'default_duration' => 30,
				'service_subsection_id' => $subsection['id'],
			));
		}
	}

	public function down()
	{
		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 0;');
		$command->execute();
		
		$service = $this->dbConnection->createCommand()
			->select('id, name')
			->from('service')
			->where('name=:name', array(':name'=>'Adnexal Service'))
			->queryRow();
		
		$subsections = $this->dbConnection->createCommand()
			->select('id')
			->from('service_subsection')
			->where('service_id!=:id', array(':id'=>$service['id']))
			->queryAll();
		
		foreach ($subsections as $section) {
			$this->delete('procedure', 'service_subsection_id=:id', 
				array(':id' => $section['id']));
			
			$this->delete('service_subsection', 'id=:id', 
				array(':id' => $section['id']));
		}
		
		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 1;');
		$command->execute();
	}
}