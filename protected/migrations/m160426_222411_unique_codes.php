<?php

class m160426_222411_unique_codes extends OEMigration
{
    public function up()
    {
        $this->createOETable('unique_codes', array(
                'id' => 'pk',
                'code' => 'varchar(6) NOT NULL',
                'active' => 'int(1) unsigned NOT NULL default 1',
            ), true);
    }

    public function down()
    {
        $this->dropOETable('unique_codes', true);
    }
}
