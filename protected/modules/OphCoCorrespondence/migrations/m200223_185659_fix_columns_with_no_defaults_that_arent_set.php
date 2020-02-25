<?php

class m200223_185659_fix_columns_with_no_defaults_that_arent_set extends OEMigration
{
    public function up()
    {
        $this->alterOEColumn('et_ophcocorrespondence_letter', 'fax', 'VARCHAR(64) NOT NULL DEFAULT ""', true);
        $this->alterOEColumn('document_instance_data', 'start_datetime', 'DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00"', true);
        $this->alterOEColumn('document_instance_data', 'date', 'DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00"', true);
    }

    public function down()
    {
        $this->alterOEColumn('et_ophcocorrespondence_letter', 'fax', 'VARCHAR(64) NOT NULL', true);
        $this->alterOEColumn('document_instance_data', 'start_datetime', 'DATETIME NOT NULL', true);
        $this->alterOEColumn('document_instance_data', 'date', 'DATETIME NOT NULL', true);
    }
}
