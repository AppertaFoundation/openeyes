<?php

class m200223_190310_fix_columns_with_no_defaults_that_arent_set extends OEMigration
{
    public function up()
    {
        $this->alterOEColumn('cat_prom5_questions', 'mandatory', 'TINYINT(1) NOT NULL DEFAULT 1', false);
    }

    public function down()
    {
        $this->alterOEColumn('cat_prom5_questions', 'mandatory', 'TINYINT(1) NOT NULL', false);
    }
}
