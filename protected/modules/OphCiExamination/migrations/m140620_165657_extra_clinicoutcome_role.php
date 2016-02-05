<?php

class m140620_165657_extra_clinicoutcome_role extends CDbMigration
{
    public function up()
    {
        $this->dbConnection->createCommand('insert into ophciexamination_clinicoutcome_role (name,display_order,requires_comment,active) values (\'Any Clinician\',50,0,1)')->query();
    }

    public function down()
    {
        $this->dbConnection->createCommand('delete from ophciexamination_clinicoutcome_role where name= \'Any Clinician\'')->query();
    }
}
