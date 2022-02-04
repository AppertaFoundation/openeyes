<?php

class m201110_105728_remove_default_for_snomed_term_in_proc extends OEMigration
{
    public function safeUp()
    {
        $this->alterColumn('proc', 'snomed_term', 'varchar(255) NOT NULL');
    }

    public function safeDown()
    {
        $this->alterColumn('proc', 'snomed_term', 'varchar(255) NOT NULL DEFAULT 0');
    }
}
