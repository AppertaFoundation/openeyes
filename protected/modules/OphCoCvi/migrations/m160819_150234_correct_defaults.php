<?php

class m160819_150234_correct_defaults extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('et_ophcocvi_eventinfo', 'is_draft', 'tinyint(1) unsigned DEFAULT 1 NOT NULL');
        $this->alterColumn('et_ophcocvi_clinicinfo', 'is_considered_blind', 'tinyint(1) unsigned');
        $this->alterColumn('et_ophcocvi_clinicinfo', 'sight_varies_by_light_levels', 'tinyint(1) unsigned');
    }

    public function down()
    {
        $this->alterColumn('et_ophcocvi_eventinfo', 'is_draft', 'tinyint(1) unsigned DEFAULT 0 NOT NULL');
        $this->alterColumn('et_ophcocvi_clinicinfo', 'is_considered_blind', 'tinyint(1) unsigned DEFAULT 0 NOT NULL');
        $this->alterColumn('et_ophcocvi_clinicinfo', 'sight_varies_by_light_levels', 'tinyint(1) unsigned DEFAULT 0 NOT NULL');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
