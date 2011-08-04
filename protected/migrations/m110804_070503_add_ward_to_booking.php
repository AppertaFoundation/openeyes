<?php

class m110804_070503_add_ward_to_booking extends CDbMigration
{
	public function up()
	{
		$this->addColumn('booking', 'ward_id', 'integer(11) unsigned DEFAULT "0"');

		$this->addForeignKey('ward_id', 'booking', 'ward_id', 'ward', 'id');
	}

	public function down()
	{
		$this->dropForeignKey('ward_id', 'booking');

		$this->dropColumn('booking', 'ward_id');
	}
}