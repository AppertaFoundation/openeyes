<?php

class m111009_094023_add_admission_time_to_booking extends CDbMigration
{
	public function up()
	{
		$this->addColumn('booking', 'admission_time', 'time NOT NULL');
	}

	public function down()
	{
		$this->dropColumn('booking', 'admission_time');
	}
}