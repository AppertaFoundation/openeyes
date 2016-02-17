<?php

class m140609_120012_add_ORA_instrument_to_IOP extends OEMigration
{
    public function up()
    {
        $migrations_path = dirname(__FILE__);
        $this->initialiseData($migrations_path);
    }

    public function down()
    {
        $this->dbConnection->createCommand("delete from ophciexamination_instrument where name = 'ORA IOPcc'")->Execute();
    }
}
