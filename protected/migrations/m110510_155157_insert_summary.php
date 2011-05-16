<?php

class m110510_155157_insert_summary extends CDbMigration
{
	public function up()
	{
		$this->insert('summary', array(
			'name'=>'exampleSummary'
		));

		$summary = $this->dbConnection->createCommand()
			->select('id')
			->from('summary')
			->where('name = :name', array(':name'=>'exampleSummary'))
			->queryRow();

		$specialties = $this->dbConnection->createCommand()
			->select('id, name')
			->from('specialty')
			->queryAll();

		foreach ($specialties as $specialty) {
			$this->insert('summary_specialty_assignment', array(
				'specialty_id' => $specialty['id'],
				'summary_id' => $summary['id']
			));
		}
	}

	public function down()
	{
		$summary = $this->dbConnection->createCommand()
			->select('id')
			->from('summary')
			->where('name = :name', array(':name'=>'exampleSummary'))
			->queryRow();

		$this->delete('summary_specialty_assignment', 'summary_id=:summary_id',
				array(':summary_id' => $summary['id']));

		$this->delete('summary', 'id=:id',
				array(':id' => $summary['id']));
	}
}