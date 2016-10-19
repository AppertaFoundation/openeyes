<?php

class m131111_120433_inheritance_can_be_null extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('pedigree', 'inheritance_id', 'int(10) unsigned NULL');
    }

    public function down()
    {
        $this->alterColumn('pedigree', 'inheritance_id', 'int(10) unsigned NOT NULL');
    }
}
