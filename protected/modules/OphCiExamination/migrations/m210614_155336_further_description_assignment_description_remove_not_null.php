<?php

class m210614_155336_further_description_assignment_description_remove_not_null extends CDbMigration
{
    public function safeUp()
    {
        $this->alterColumn('ophciexamination_further_findings_assignment', 'description', 'varchar(4096)');
    }

    public function safeDown()
    {
        $this->alterColumn('ophciexamination_further_findings_assignment', 'description', 'varchar(4096) NOT NULL');
    }
}
