<?php

class m140508_140000_add_accomplish_date extends OEMigration
{
	public function up()
	{
		$this->addColumn('event','accomplished_date', 'datetime null default null AFTER created_date');
	}

	public function down()
	{
		$this->dropColumn('event','accomplished_date');
	}
}