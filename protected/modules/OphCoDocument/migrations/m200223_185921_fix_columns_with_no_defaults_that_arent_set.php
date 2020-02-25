<?php

class m200223_185921_fix_columns_with_no_defaults_that_arent_set extends OEMigration
{
    public function up()
    {
        $this->alterOEColumn('protected_file', 'description', 'VARCHAR(64) NOT NULL DEFAULT ""', true);
    }

    public function down()
    {
        $this->alterOEColumn('protected_file', 'description', 'VARCHAR(64) NOT NULL', true);
    }
}
