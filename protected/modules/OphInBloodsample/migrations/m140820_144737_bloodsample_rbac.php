<?php

class m140820_144737_bloodsample_rbac extends CDbMigration
{
    public function up()
    {
        $this->insert('authitem', array('name' => 'TaskEditBloodSample', 'type' => 1));
        $this->insert('authitem', array('name' => 'TaskViewBloodSample', 'type' => 1));

        $this->insert('authitemchild', array('parent' => 'Genetics Admin', 'child' => 'TaskEditBloodSample'));
        $this->insert('authitemchild', array('parent' => 'Genetics Admin', 'child' => 'TaskViewBloodSample'));
        $this->insert('authitemchild', array('parent' => 'Genetics User', 'child' => 'TaskViewBloodSample'));

        $this->insert('authitem', array('name' => 'OprnViewBloodSample', 'type' => 0));
        $this->insert('authitem', array('name' => 'OprnEditBloodSample', 'type' => 0));

        $this->insert('authitemchild', array('parent' => 'TaskEditBloodSample', 'child' => 'OprnEditBloodSample'));
        $this->insert('authitemchild', array('parent' => 'TaskViewBloodSample', 'child' => 'OprnViewBloodSample'));
    }

    public function down()
    {
    }
}
