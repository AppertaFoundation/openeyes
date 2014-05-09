<?php

class m140508_140000_add_accomplish_date extends OEMigration
{
	public function up()
	{
		$this->addColumn('event', 'accomplished_date', 'datetime not null AFTER created_date');
		$this->addColumn('event_version', 'accomplished_date', 'datetime not null AFTER created_date');
		$this->update('event', array('accomplished_date' =>  new CDbExpression('created_date')));
		$this->update('event_version', array('accomplished_date' =>  new CDbExpression('created_date')));
	}

	public function down()
	{
		$this->dropColumn('event', 'accomplished_date');
		$this->dropColumn('event_version', 'accomplished_date');
	}
}