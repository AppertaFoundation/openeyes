<?php

class m140804_095224_genetics_rbac_migration extends OEMigration
{
    public function up()
    {
        $this->insert('authitem', array('name' => 'Genetics Admin', 'type' => 2));
        $this->insert('authitem', array('name' => 'Genetics User', 'type' => 2));

        $this->insert('authitem', array('name' => 'TaskEditPedigreeData', 'type' => 1));
        $this->insert('authitem', array('name' => 'TaskViewPedigreeData', 'type' => 1));

        $this->insert('authitemchild', array('parent' => 'Genetics Admin', 'child' => 'TaskEditPedigreeData'));
        $this->insert('authitemchild', array('parent' => 'Genetics Admin', 'child' => 'TaskViewPedigreeData'));
        $this->insert('authitemchild', array('parent' => 'Genetics User', 'child' => 'TaskViewPedigreeData'));

        $this->insert('authitem', array('name' => 'OprnSearchPedigree', 'type' => 0));
        $this->insert('authitem', array('name' => 'OprnEditPedigree', 'type' => 0));

        $this->insert('authitemchild', array('parent' => 'TaskEditPedigreeData', 'child' => 'OprnEditPedigree'));
        $this->insert('authitemchild', array('parent' => 'TaskViewPedigreeData', 'child' => 'OprnSearchPedigree'));
    }

    public function down()
    {
    }
}
