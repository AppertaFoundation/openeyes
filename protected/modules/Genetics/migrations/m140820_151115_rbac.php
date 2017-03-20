<?php

class m140820_151115_rbac extends CDbMigration
{
    public function up()
    {
        $this->insert('authitem', array('name' => 'TaskEditDNAExtraction', 'type' => 1));
        $this->insert('authitem', array('name' => 'TaskViewDNAExtraction', 'type' => 1));

        $this->insert('authitemchild', array('parent' => 'Genetics Admin', 'child' => 'TaskEditDNAExtraction'));
        $this->insert('authitemchild', array('parent' => 'Genetics Admin', 'child' => 'TaskViewDNAExtraction'));
        $this->insert('authitemchild', array('parent' => 'Genetics User', 'child' => 'TaskViewDNAExtraction'));

        $this->insert('authitem', array('name' => 'OprnViewDNAExtraction', 'type' => 0));
        $this->insert('authitem', array('name' => 'OprnEditDNAExtraction', 'type' => 0));

        $this->insert('authitemchild', array('parent' => 'TaskEditDNAExtraction', 'child' => 'OprnEditDNAExtraction'));
        $this->insert('authitemchild', array('parent' => 'TaskViewDNAExtraction', 'child' => 'OprnViewDNAExtraction'));
    }

    public function down()
    {
    }
}
