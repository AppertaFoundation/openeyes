<?php

class m180731_094933_change_protected_file_name_length extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('protected_file', 'name', 'varchar(255)');
    }
    public function down()
    {
        $this->alterColumn('protected_file', 'name', 'varchar(64)');
    }
}
