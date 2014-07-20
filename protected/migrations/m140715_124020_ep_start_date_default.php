<?php

class m140715_124020_ep_start_date_default extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('episode', 'start_date', "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'");
	}

	public function down()
	{
		$this->alterColumn('episode', 'start_date', "datetime NOT NULL");
	}

}