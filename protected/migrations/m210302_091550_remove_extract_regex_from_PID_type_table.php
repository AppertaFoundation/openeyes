<?php

class m210302_091550_remove_extract_regex_from_PID_type_table extends OEMigration
{
    public function safeUp()
    {
        $this->dropOEColumn('patient_identifier_type', 'extract_regex', true);
    }

    public function safeDown()
    {
        $this->addOEColumn('patient_identifier_type', 'extract_regex', 'varchar(255) NULL', true);
    }
}
