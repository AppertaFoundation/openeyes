<?php

class m210803_142840_correct_defaults extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('et_ophcocvi_eventinfo_version', 'is_draft', 'tinyint(1) unsigned DEFAULT 1 NOT NULL');
		$this->alterColumn('et_ophcocvi_clinicinfo_version', 'is_considered_blind', 'tinyint(1) unsigned');
		$this->alterColumn('et_ophcocvi_clinicinfo_version', 'sight_varies_by_light_levels', 'tinyint(1) unsigned');
	}

	public function down()
	{
		$this->alterColumn('et_ophcocvi_eventinfo_version', 'is_draft', 'tinyint(1) unsigned DEFAULT 0 NOT NULL');
		$this->alterColumn('et_ophcocvi_clinicinfo_version', 'is_considered_blind', 'tinyint(1) unsigned DEFAULT 0 NOT NULL');
		$this->alterColumn('et_ophcocvi_clinicinfo_version', 'sight_varies_by_light_levels', 'tinyint(1) unsigned DEFAULT 0 NOT NULL');
	}
}
