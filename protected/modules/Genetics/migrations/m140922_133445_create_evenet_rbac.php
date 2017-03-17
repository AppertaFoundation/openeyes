<?php

class m140922_133445_create_evenet_rbac extends CDbMigration
{
    public function up()
    {
        $this->insert('authitem', array('name' => 'TaskCreateDnaExtraction', 'type' => 1));
        $this->insert('authitemchild', array('parent' => 'Genetics User', 'child' => 'TaskCreateDnaExtraction'));

        $this->insert('authitem', array('name' => 'OprnCreateDnaExtraction', 'type' => 0));
        $this->insert('authitemchild', array('parent' => 'TaskCreateDnaExtraction', 'child' => 'OprnCreateDnaExtraction'));
    }

    public function down()
    {
    }
}
