<?php

class m140620_165450_driving_status_update extends CDbMigration
{
	public function up()
	{
		Yii::app()->db->createCommand('update socialhistory_driving_status set name =\'HGV\' where name=\'HGV, Taxi, Train\'')->query();
	}

	public function down()
	{
		Yii::app()->db->createCommand('update socialhistory_driving_status set name =\'HGV, Taxi, Train\' where name=\'HGV\'')->query();
	}
}