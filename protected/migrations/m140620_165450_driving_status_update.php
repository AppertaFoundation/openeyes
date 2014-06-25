<?php

class m140620_165450_driving_status_update extends CDbMigration
{
	public function up()
	{
		$this->dbConnection->createCommand('update socialhistory_driving_status set name =\'HGV\' where name=\'HGV, Taxi, Train\'')->query();
	}

	public function down()
	{
		$this->dbConnection->createCommand('update socialhistory_driving_status set name =\'HGV, Taxi, Train\' where name=\'HGV\'')->query();
	}
}