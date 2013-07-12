<?php

class m130409_175500_drug_table_changes extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('drug','default_route_id','int(10) unsigned DEFAULT NULL');
		$this->alterColumn('drug','default_frequency_id','int(10) unsigned DEFAULT NULL');
		$this->alterColumn('drug','default_duration_id','int(10) unsigned DEFAULT NULL');
		$this->addColumn('drug_route','display_order','int(10) unsigned DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('drug_route','display_order');
		$this->alterColumn('drug','default_route_id','int(10) unsigned NOT NULL');
		$this->alterColumn('drug','default_frequency_id','int(10) unsigned NOT NULL');
		$this->alterColumn('drug','default_duration_id','int(10) unsigned NOT NULL');
	}

}
