<?php

class m161020_150809_genetic_patient_rbac extends CDbMigration
{
    public function up()
    {
        $this->insert('authitem', array('name' => 'TaskEditGeneticPatient', 'type' => 1));
        $this->insert('authitem', array('name' => 'TaskViewGeneticPatient', 'type' => 1));

        $this->insert('authitemchild', array('parent' => 'Genetics Admin', 'child' => 'TaskEditGeneticPatient'));
        $this->insert('authitemchild', array('parent' => 'Genetics Admin', 'child' => 'TaskViewGeneticPatient'));
        $this->insert('authitemchild', array('parent' => 'Genetics User', 'child' => 'TaskViewGeneticPatient'));

        $this->insert('authitem', array('name' => 'OprnViewGeneticPatient', 'type' => 0));
        $this->insert('authitem', array('name' => 'OprnEditGeneticPatient', 'type' => 0));

        $this->insert('authitemchild', array('parent' => 'TaskEditGeneticPatient', 'child' => 'OprnEditGeneticPatient'));
        $this->insert('authitemchild', array('parent' => 'TaskViewGeneticPatient', 'child' => 'OprnViewGeneticPatient'));
    }

    public function down()
    {
        $this->delete('authitemchild', 'parent = ? and child = ?', array('TaskViewGeneticPatient', 'OprnViewGeneticPatient'));
        $this->delete('authitemchild', 'parent = ? and child = ?', array('TaskEditGeneticPatient', 'OprnEditGeneticPatient'));

        $this->delete('authitem', 'name = ? and type = ?', array('OprnViewGeneticPatient', 0));
        $this->delete('authitem', 'name = ? and type = ?', array('OprnEditGeneticPatient', 0));

        $this->delete('authitemchild', 'parent = ? and child = ?', array('Genetics Admin', 'TaskEditGeneticPatient'));
        $this->delete('authitemchild', 'parent = ? and child = ?', array('Genetics Admin', 'TaskViewGeneticPatient'));
        $this->delete('authitemchild', 'parent = ? and child = ?', array('Genetics User', 'TaskViewGeneticPatient'));

        $this->delete('authitem', 'name = ? and type = ?', array('TaskEditGeneticPatient', 1));
        $this->delete('authitem', 'name = ? and type = ?', array('TaskViewGeneticPatient', 1));
    }
}
