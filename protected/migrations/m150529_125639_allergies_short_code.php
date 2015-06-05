<?php

class m150529_125639_allergies_short_code extends CDbMigration
{
	public function up()
	{
		$this->insert('patient_shortcode', array(
			'event_type_id' => $this->dbConnection->createCommand()
				->select('id')
				->from('event_type')
				->where('name=:name', array(':name' => 'examination'))
				->queryScalar(),
			'default_code' => 'aka',
			'code' => 'aka',
			'method' => 'getAllergies',
			'description' => 'List of patients allergies'
		));
	}

	public function down()
	{
		$this->delete('patient_shortcode', 'default_code = "aka"');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}