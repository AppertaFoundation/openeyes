<?php

class m200223_190226_fix_columns_with_no_defaults_that_arent_set extends OEMigration
{
    public function up()
    {
        $this->alterOEColumn('ophcotherapya_email', 'last_modified_date', 'DATETIME NOT NULL DEFAULT "1901-01-01 00:00:00"', true);
        $this->alterOEColumn('ophcotherapya_email', 'created_date', 'DATETIME NOT NULL DEFAULT "1901-01-01 00:00:00"', true);
    }

    public function down()
    {
        $this->alterOEColumn('ophcotherapya_email', 'last_modified_date', 'DATETIME NOT NULL', true);
        $this->alterOEColumn('ophcotherapya_email', 'created_date', 'DATETIME NOT NULL', true);
    }
}
