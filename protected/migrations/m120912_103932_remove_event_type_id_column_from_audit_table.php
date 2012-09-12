<?php

class m120912_103932_remove_event_type_id_column_from_audit_table extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('audit','event_type_id');
	}

	public function down()
	{
		$this->addColumn('audit','event_type_id','int(10) unsigned NULL');
	}
}
