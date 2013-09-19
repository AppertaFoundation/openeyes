<?php

class m120809_070713_northwick_park_changes extends CDbMigration
{
	public function up()
	{
		$this->update('theatre',array('name'=>'Northwick Park Theatre'),'id=14');
		$this->update('ward',array('name'=>'Northwick Park Day Care, Ground floor'),'id=7');
	}

	public function down()
	{
		$this->update('theatre',array('name'=>'Theatre Seven'),'id=14');
		$this->update('ward',array('name'=>'VanGuard Mobile Operating Theatre'),'id=7');
	}
}
