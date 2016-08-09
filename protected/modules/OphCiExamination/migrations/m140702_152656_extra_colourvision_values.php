<?php

class m140702_152656_extra_colourvision_values extends OEMigration
{
    public function up()
    {
        $migrations_path = dirname(__FILE__);
        $this->initialiseData($migrations_path);
    }

    public function down()
    {
        $values = array('16/21', '17/21', '18/21', '19/21', '20/21', '21/21');
        foreach ($values as $value) {
            $this->dbConnection->createCommand('delete from ophciexamination_colourvision_value where name=\''.$value.'\'')->query();
        }
    }
}
