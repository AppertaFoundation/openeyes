<?php

class m211210_111521_update_protected_file_to_255_chars extends OEMigration
{
    public function up()
    {
        $this->alterOEColumn('protected_file', 'title', 'VARCHAR(255)', true);
    }

    public function down()
    {
        $this->alterOEColumn('protected_file', 'title', 'VARCHAR(64)', true);
    }
}
