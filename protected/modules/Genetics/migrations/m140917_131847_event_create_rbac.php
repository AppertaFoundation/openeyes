<?php

class m140917_131847_event_create_rbac extends CDbMigration
{
    public function up()
    {
        $this->insert('authitem', array('name' => 'TaskCreateBloodSample', 'type' => 1));
        $this->insert('authitemchild', array('parent' => 'Genetics User', 'child' => 'TaskCreateBloodSample'));

        $this->insert('authitem', array('name' => 'OprnCreateBloodSample', 'type' => 0));
        $this->insert('authitemchild', array('parent' => 'TaskCreateBloodSample', 'child' => 'OprnCreateBloodSample'));
    }

    public function down()
    {
    }
}
