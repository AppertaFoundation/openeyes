<?php

class m140917_133025_event_create_rbac extends CDbMigration
{
    public function up()
    {
        $this->insert('authitem', array('name' => 'TaskCreateGeneticTest', 'type' => 1));
        $this->insert('authitemchild', array('parent' => 'Genetics User', 'child' => 'TaskCreateGeneticTest'));

        $this->insert('authitem', array('name' => 'OprnCreateGeneticTest', 'type' => 0));
        $this->insert('authitemchild', array('parent' => 'TaskCreateGeneticTest', 'child' => 'OprnCreateGeneticTest'));
    }

    public function down()
    {
    }
}
