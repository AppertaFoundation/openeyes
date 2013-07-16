<?php

class m130716_103017_drop_datetime_field extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('event','datetime');
	}

	public function down()
	{
		$this->addColumn('event','datetime','datetime NOT NULL');
	}
}
