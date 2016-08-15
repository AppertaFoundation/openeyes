<?php

class m160209_104243_change_pas_code_length extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('firm', 'pas_code', 'varchar(20)');
    }

    public function down()
    {
        $this->alterColumn('firm', 'pas_code', 'varchar(4)');
    }
}
