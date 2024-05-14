<?php

class m210408_162600_allow_null_location_for_sites extends OEMigration
{
    public function up()
    {
        $this->alterOEColumn('site', 'location', 'varchar(64) NULL', true);
    }

    public function down()
    {
        $this->alterOEColumn('site', 'location', 'varchar(64) NOT NULL', true);
    }
}
