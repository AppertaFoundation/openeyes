<?php

class m200811_010042_add_pas_key_column_to_institution extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('institution', 'pas_key', 'varchar(10)', true);
    }

    public function down()
    {
        $this->dropOEColumn('institution', 'pas_key', true);
    }
}
