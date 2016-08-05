<?php

class m150608_144157_increase_commisioning_body_name_limit extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('commissioning_body', 'name', 'VARCHAR(100) NOT NULL');
        $this->alterColumn('commissioning_body_version', 'name', 'VARCHAR(100) NOT NULL');
    }

    public function down()
    {
        $this->alterColumn('commissioning_body', 'name', 'VARCHAR(64) NOT NULL');
        $this->alterColumn('commissioning_body_version', 'name', 'VARCHAR(64) NOT NULL');
    }
}
