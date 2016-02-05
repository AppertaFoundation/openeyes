<?php

class m140702_112030_overall_period_active_col extends OEMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_overallperiod', 'active', 'tinyint(1) unsigned DEFAULT 1');
        $this->addColumn('ophciexamination_overallperiod_version', 'active', 'tinyint(1) unsigned DEFAULT 1');
        $this->addColumn('ophciexamination_visitinterval', 'active', 'tinyint(1) unsigned DEFAULT 1');
        $this->addColumn('ophciexamination_visitinterval_version', 'active', 'tinyint(1) unsigned DEFAULT 1');
    }

    public function down()
    {
        $this->dropColumn('ophciexamination_overallperiod', 'active');
        $this->dropColumn('ophciexamination_overallperiod_version', 'active');
        $this->dropColumn('ophciexamination_visitinterval', 'active');
        $this->dropColumn('ophciexamination_visitinterval_version', 'active');
    }
}
