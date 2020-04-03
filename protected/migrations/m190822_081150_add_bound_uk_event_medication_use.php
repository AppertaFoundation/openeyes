<?php

class m190822_081150_add_bound_uk_event_medication_use extends CDbMigration
{
	public function up()
	{
		$this->addColumn('event_medication_use' , 'bound_key' , 'VARCHAR(20) NULL');
		$this->addColumn('event_medication_use_version' , 'bound_key' , 'VARCHAR(20) NULL');
	}

	public function down()
	{
		$this->dropColumn('event_medication_use', 'bound_key');
		$this->dropColumn('event_medication_use_version', 'bound_key');
	}
}