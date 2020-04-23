<?php

class m200223_190001_fix_columns_with_no_defaults_that_arent_set extends OEMigration
{
    public function up()
    {
        $this->alterOEColumn('et_ophtrlaser_anteriorseg', 'right_eyedraw', 'TEXT NULL', true);
        $this->alterOEColumn('et_ophtrlaser_anteriorseg', 'left_eyedraw', 'TEXT NULL', true);
    }

    public function down()
    {
        $this->alterOEColumn('et_ophtrlaser_anteriorseg', 'right_eyedraw', 'TEXT NOT NULL', true);
        $this->alterOEColumn('et_ophtrlaser_anteriorseg', 'left_eyedraw', 'TEXT NOT NULL', true);
    }
}
