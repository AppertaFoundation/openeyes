<?php

class m200223_225225_fix_columns_with_no_defaults_that_arent_set extends OEMigration
{
    public function up()
    {
        $this->alterOEColumn('ophtroperationnote_attribute', 'display_order', 'INT(8) NULL DEFAULT NULL', true);
    }

    public function down()
    {
        $this->alterOEColumn('ophtroperationnote_attribute', 'display_order', 'INT(8) NOT NULL DEFAULT 0', true);
    }
}
