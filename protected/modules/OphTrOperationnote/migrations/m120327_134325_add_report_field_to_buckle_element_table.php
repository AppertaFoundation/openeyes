<?php

class m120327_134325_add_report_field_to_buckle_element_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('et_ophtroperationnote_buckle','report','varchar(4096) COLLATE utf8_bin NOT NULL');
	}

	public function down()
	{
		$this->dropColumn('et_ophtroperationnote_buckle','report');
	}
}
