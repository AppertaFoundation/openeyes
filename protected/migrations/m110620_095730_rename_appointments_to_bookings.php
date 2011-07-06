<?php

class m110620_095730_rename_appointments_to_bookings extends CDbMigration
{
	public function up()
	{
		$this->renameTable('appointment', 'booking');
	}

	public function down()
	{
		$this->renameTable('booking', 'appointment');
	}
}