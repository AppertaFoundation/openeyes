<?php

class m111125_153919_add_booking_confirmation extends CDbMigration
{
	public function up()
	{
		$this->addColumn('booking', 'confirmed', 'tinyint(1) unsigned NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('booking', 'confirmed');
	}
}
