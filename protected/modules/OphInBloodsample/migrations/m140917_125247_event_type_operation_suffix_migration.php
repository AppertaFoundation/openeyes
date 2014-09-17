<?php

class m140917_125247_event_type_operation_suffix_migration extends OEMigration
{
	public function up()
	{
		$this->setEventTypeRBACSuffix('OphInBloodsample','Bloodsample');
	}

	public function down()
	{
		$this->setEventTypeRBACSuffix('OphInBloodsample',NULL);
	}
}