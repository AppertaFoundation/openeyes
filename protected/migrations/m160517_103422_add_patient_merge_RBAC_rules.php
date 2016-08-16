<?php

class m160517_103422_add_patient_merge_RBAC_rules extends CDbMigration
{
    public function up()
    {
        $this->insert('authitem', array('name' => 'Patient Merge Request', 'type' => 2));
        $this->insert('authitem', array('name' => 'Patient Merge', 'type' => 2));
    }

    public function down()
    {
        $this->delete('authitem', 'name = "Patient Merge Request"');
        $this->delete('authitem', 'name = "Patient Merge"');
    }
}
