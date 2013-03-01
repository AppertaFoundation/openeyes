<?php

class m130301_113502_drug_frequency_display_order extends OEMigration
{
	public function up()
	{
		$this->addColumn('drug_frequency','display_order','int(10) unsigned NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('drug_frequency','display_order');
	}
}
