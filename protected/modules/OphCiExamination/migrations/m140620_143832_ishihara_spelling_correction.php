<?php

class m140620_143832_ishihara_spelling_correction extends CDbMigration
{
    public function up()
    {
        $this->dbConnection->createCommand('update ophciexamination_colourvision_method set name =\'Ishihara /15\' where name=\'Isihara /15\'')->query();
        $this->dbConnection->createCommand('update ophciexamination_colourvision_method set name =\'Ishihara /21\' where name=\'Isihara /21\'')->query();
    }

    public function down()
    {
        $this->dbConnection->createCommand('update ophciexamination_colourvision_method set name =\'Isihara /15\' where name=\'Ishihara /15\'')->query();
        $this->dbConnection->createCommand('update ophciexamination_colourvision_method set name =\'Isihara /21\' where name=\'Ishihara /21\'')->query();
    }
}
