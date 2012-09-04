<?php

class m120904_074035_add_event_type_id_column_to_audit_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('audit','event_type_id','int(10) unsigned NULL');
	}

	public function down()
	{
		$this->dropColumn('audit','event_type_id');
	}
}
