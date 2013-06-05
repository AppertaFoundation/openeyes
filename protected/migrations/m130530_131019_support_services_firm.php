<?php

class m130530_131019_support_services_firm extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('firm','service_subspecialty_assignment_id','int(10) unsigned NULL');
		$this->alterColumn('firm','consultant_id','int(10) unsigned NULL');
		$this->addColumn('episode','support_services','tinyint(1) unsigned NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('episode','support_services');
		$this->alterColumn('firm','service_subspecialty_assignment_id','int(10) unsigned NOT NULL');
		$this->alterColumn('firm','consultant_id','int(10) unsigned NOT NULL');
	}
}
