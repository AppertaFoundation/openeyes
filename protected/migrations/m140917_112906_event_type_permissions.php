<?php

class m140917_112906_event_type_permissions extends OEMigration
{
	public function up()
	{
		$this->addColumn('event_type','rbac_operation_suffix','varchar(100) COLLATE utf8_bin');
	}

	public function down()
	{
		$this->dropColumn('event_type', 'rbac_operation_suffix');
	}
}
