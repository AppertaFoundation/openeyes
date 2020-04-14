<?php

class m200223_185336_fix_columns_with_no_defaults_that_arent_set extends OEMigration
{
    public function up()
    {
        $this->alterOEColumn('et_ophdrprescription_details', 'print', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0', true);
    }

    public function down()
    {
        $this->alterOEColumn('et_ophdrprescription_details', 'print', 'TINYINT(1) UNSIGNED NOT NULL', true);
    }
}
