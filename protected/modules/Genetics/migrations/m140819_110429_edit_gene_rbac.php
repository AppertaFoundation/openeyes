<?php

class m140819_110429_edit_gene_rbac extends CDbMigration
{
    public function up()
    {
        $this->insert('authitem', array('name' => 'TaskEditGeneData', 'type' => 1));
        $this->insert('authitem', array('name' => 'TaskViewGeneData', 'type' => 1));

        $this->insert('authitemchild', array('parent' => 'Genetics Admin', 'child' => 'TaskEditGeneData'));
        $this->insert('authitemchild', array('parent' => 'Genetics Admin', 'child' => 'TaskViewGeneData'));
        $this->insert('authitemchild', array('parent' => 'Genetics User', 'child' => 'TaskViewGeneData'));

        $this->insert('authitem', array('name' => 'OprnViewGene', 'type' => 0));
        $this->insert('authitem', array('name' => 'OprnEditGene', 'type' => 0));

        $this->insert('authitemchild', array('parent' => 'TaskEditGeneData', 'child' => 'OprnEditGene'));
        $this->insert('authitemchild', array('parent' => 'TaskViewGeneData', 'child' => 'OprnViewGene'));
    }

    public function down()
    {
    }
}
