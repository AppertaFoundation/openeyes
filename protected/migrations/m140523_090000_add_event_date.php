<?php

class m140523_090000_add_event_date extends OEMigration
{
	public function up()
	{
		$this->addColumn('event', 'event_date', 'datetime not null AFTER created_date');
		$this->addColumn('event_version', 'event_date', 'datetime not null AFTER created_date');
		$this->update('event', array('event_date' =>  new CDbExpression('created_date')));
		$this->update('event_version', array('event_date' =>  new CDbExpression('created_date')));
	}

	public function down()
	{
		$this->dropColumn('event', 'event_date');
		$this->dropColumn('event_version', 'event_date');
	}
}