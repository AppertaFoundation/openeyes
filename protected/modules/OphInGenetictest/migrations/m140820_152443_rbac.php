<?php

class m140820_152443_rbac extends CDbMigration
{
    public function up()
    {
        $this->insert('authitem', array('name' => 'TaskEditGeneticTest', 'type' => 1));
        $this->insert('authitem', array('name' => 'TaskViewGeneticTest', 'type' => 1));

        $this->insert('authitemchild', array('parent' => 'Genetics Admin', 'child' => 'TaskEditGeneticTest'));
        $this->insert('authitemchild', array('parent' => 'Genetics Admin', 'child' => 'TaskViewGeneticTest'));
        $this->insert('authitemchild', array('parent' => 'Genetics User', 'child' => 'TaskViewGeneticTest'));

        $this->insert('authitem', array('name' => 'OprnViewGeneticTest', 'type' => 0));
        $this->insert('authitem', array('name' => 'OprnEditGeneticTest', 'type' => 0));

        $this->insert('authitemchild', array('parent' => 'TaskEditGeneticTest', 'child' => 'OprnEditGeneticTest'));
        $this->insert('authitemchild', array('parent' => 'TaskViewGeneticTest', 'child' => 'OprnViewGeneticTest'));
    }

    public function down()
    {
    }
}
