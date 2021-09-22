<?php

class m161216_153049_dna_rbac_operation extends CDbMigration
{
    public function up()
    {
        $this->update('event_type', array('rbac_operation_suffix' => 'DnaSample'), "rbac_operation_suffix = 'BloodSample'");

        $this->delete('authitemchild', "parent = 'Genetics User' AND child = 'TaskCreateBloodSample'");
        $this->delete('authitemchild', "parent = 'TaskCreateBloodSample' AND child = 'OprnCreateBloodSample'");

        $this->update('authitem', array('name' => 'TaskCreateDnaSample', 'type' => 1), "name = 'TaskCreateBloodSample'");
        $this->update('authitem', array('name' => 'OprnCreateDnaSample', 'type' => 0), "name = 'OprnCreateBloodSample'");

        $this->insert('authitemchild', array('parent' => 'Genetics User' ,'child' => 'TaskCreateDnaSample'));
        $this->insert('authitemchild', array('parent' => 'TaskCreateDnaSample','child' => 'OprnCreateDnaSample'));
    }

    public function down()
    {
        $this->update('event_type', array('rbac_operation_suffix' => 'BloodSample'), "rbac_operation_suffix = 'DnaSample'");

        $this->delete('authitemchild', "parent = 'Genetics User' AND child = 'TaskCreateDnaSample'");
        $this->delete('authitemchild', "parent = 'TaskCreateDnaSample' AND child = 'OprnCreateDnaSample'");

        $this->update('authitem', array('name' => 'TaskCreateBloodSample', 'type' => 1), "name = 'TaskCreateDnaSample'");
        $this->update('authitem', array('name' => 'OprnCreateBloodSample', 'type' => 0), "name = 'OprnCreateDnaSample'");

        $this->insert('authitemchild', array('parent' => 'Genetics User' ,'child' => 'TaskCreateBloodSample'));
        $this->insert('authitemchild', array('parent' => 'TaskCreateBloodSample','child' => 'OprnCreateBloodSample'));
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
