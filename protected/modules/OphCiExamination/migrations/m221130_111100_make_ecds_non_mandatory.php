<?php

class m221130_111100_make_ecds_non_mandatory extends OEMigration
{
    public function safeUp()
    {
        $this->alterOEColumn('et_ophciexamination_investigation_codes', 'ecds_code', 'varchar(20) NULL', true);
    }

    public function safeDown()
    {
        $this->alterOEColumn('et_ophciexamination_investigation_codes', 'ecds_code', 'varchar(20) NOT NULL', true);
    }
}
