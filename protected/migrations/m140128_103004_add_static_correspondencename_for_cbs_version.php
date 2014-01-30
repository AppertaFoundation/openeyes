<?php

class m140128_103004_add_static_correspondencename_for_cbs_version extends CDbMigration
{
	public function up()
	{
		$this->addColumn('commissioning_body_service_type_version', 'correspondence_name', 'varchar(255)');
	}

	public function down()
	{
		$this->dropColumn('commissioning_body_service_type_version', 'correspondence_name');
	}
}
