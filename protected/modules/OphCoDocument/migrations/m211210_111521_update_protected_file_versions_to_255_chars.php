<?php

class m211210_111521_update_protected_file_versions_to_255_chars extends OEMigration
{
    public function up()
    {
        $this->alterOEColumn('protected_file_version', 'name', 'VARCHAR(255) NOT NULL', false);
    }

    public function down()
    {
        $this->alterOEColumn('protected_file_version', 'name', 'VARCHAR(64) NOT NULL', false);
    }
}
