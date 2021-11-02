<?php

class m211024_141549_add_fp10_code_to_site extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('site', 'fp_10_code', 'varchar(25)');
        $this->addOEColumn('site_version', 'fp_10_code', 'varchar(25)');
    }

    public function down()
    {
        $this->dropOEColumn('site', 'fp_10_code');
        $this->dropOEColumn('site_version', 'fp_10_code');
    }
}
