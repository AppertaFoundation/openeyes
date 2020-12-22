<?php

class m170117_105413_study_view_rbac extends CDbMigration
{
    public function up()
    {
        $this->insert('authitem', array('name' => 'TaskViewGeneticStudy', 'type' => 1));
        $this->insert('authitemchild', array('parent' => 'Genetics User', 'child' => 'TaskViewGeneticStudy'));
        $this->insert('authitem', array('name' => 'OprnViewGeneticStudy', 'type' => 0, 'bizrule' => 'Genetics.canViewStudy'));
        $this->insert('authitemchild', array('parent' => 'TaskViewGeneticStudy', 'child' => 'OprnViewGeneticStudy'));
    }

    public function down()
    {
        $this->delete('authitemchild', 'parent = "TaskViewGeneticStudy" and child = "OprnViewGeneticStudy"');
        $this->delete('authitem', 'name = "OprnViewGeneticStudy" and type = 0');
        $this->delete('authitemchild', 'parent = "Genetics User" and child = "TaskViewGeneticStudy"');
        $this->delete('authitem', 'name = "TaskViewGeneticStudy" and type = 1');
    }
}
