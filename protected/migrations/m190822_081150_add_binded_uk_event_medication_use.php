<?php

class m190822_081150_add_binded_uk_event_medication_use extends CDbMigration
{
	public function up()
	{
		$this->addColumn('event_medication_use' , 'binded_key' , 'VARCHAR(10) NULL');
		$this->addColumn('event_medication_use_version' , 'binded_key' , 'VARCHAR(10) NULL');
	}

	public function down()
	{
		$this->dropColumn('event_medication_use', 'binded_key');
		$this->dropColumn('event_medication_use_version', 'binded_key');
	}
}