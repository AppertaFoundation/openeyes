<?php

class m170320_104441_add_patient_role_to_genetics extends OEMigration
{
    public function up()
    {
        $this->addTaskToRole('TaskAddPatient', 'Genetics Admin');
    }

    public function down()
    {
        $this->removeTaskFromRole('TaskAddPatient', 'Genetics Admin');
    }
}