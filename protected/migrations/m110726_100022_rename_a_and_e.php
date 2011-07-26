<?php

class m110726_100022_rename_a_and_e extends CDbMigration
{
	public function up()
	{
		$this->update('specialty', array('name' => 'A & E'), 'id = :id', 
			array(':id' => 1));
	}

	public function down()
	{
		$this->update('specialty', array('name' => 'Accident & Emergency'), 
			'id = :id', array(':id' => 1));
	}
}