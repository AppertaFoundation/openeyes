<?php

class m210621_143827_fix_further_finding_version_table extends CDbMigration
{
    public function safeUp()
    {
        $this->alterColumn('ophciexamination_further_findings_assignment_version', 'description', 'varchar(4096)');
    }

    public function safeDown()
    {
        $this->alterColumn('ophciexamination_further_findings_assignment_version', 'description', 'varchar(4096) NOT NULL');
    }
}
