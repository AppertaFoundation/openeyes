<?php

class m210803_144132_correct_defaults_version_table extends CDbMigration
{
    public function safeUp()
    {
        $this->alterColumn('et_ophcocvi_eventinfo_version', 'is_draft', 'tinyint(1) unsigned DEFAULT 1 NOT NULL');
        $this->alterColumn('et_ophcocvi_clinicinfo_version', 'is_considered_blind', 'tinyint(1) unsigned');
        $this->alterColumn('et_ophcocvi_clinicinfo_version', 'sight_varies_by_light_levels', 'tinyint(1) unsigned');
    }

    public function safeDown()
    {
        $this->alterColumn('et_ophcocvi_eventinfo_version', 'is_draft', 'tinyint(1) unsigned DEFAULT 0 NOT NULL');
        $this->alterColumn('et_ophcocvi_clinicinfo_version', 'is_considered_blind', 'tinyint(1) unsigned DEFAULT 0 NOT NULL');
        $this->alterColumn('et_ophcocvi_clinicinfo_version', 'sight_varies_by_light_levels', 'tinyint(1) unsigned DEFAULT 0 NOT NULL');
    }
}
