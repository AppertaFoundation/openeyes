<?php

class m170127_102601_add_patient_create_role extends CDbMigration
{
    public function up()
    {
            $this->insert('authitem', array('name' => 'Add patient', 'type' => 2));

            $this->insert('authitem', array('name' => 'TaskAddPatient', 'type' => 1));

            $this->insert('authitemchild', array('parent' => 'Add patient', 'child' => 'TaskAddPatient'));
    }

    public function down()
    {
            $this->delete('authitem', 'name = "Add patient"');
    }
}
