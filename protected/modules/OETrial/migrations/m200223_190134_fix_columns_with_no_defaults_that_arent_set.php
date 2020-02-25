<?php

class m200223_190134_fix_columns_with_no_defaults_that_arent_set extends OEMigration
{
    public function up()
    {
        $this->alterOEColumn('trial', 'is_open', 'INT(1) NOT NULL DEFAULT 1', true);
    }

    public function down()
    {
        $this->alterOEColumn('trial', 'is_open', 'INT(1) NOT NULL', true);
    }
}
