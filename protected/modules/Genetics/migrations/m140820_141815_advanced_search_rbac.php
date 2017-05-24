<?php

class m140820_141815_advanced_search_rbac extends CDbMigration
{
    public function up()
    {
        $this->insert('authitem', array('name' => 'TaskSearchGeneticsData', 'type' => 1));

        $this->insert('authitemchild', array('parent' => 'Genetics Admin', 'child' => 'TaskSearchGeneticsData'));
        $this->insert('authitemchild', array('parent' => 'Genetics User', 'child' => 'TaskSearchGeneticsData'));

        $this->insert('authitemchild', array('parent' => 'TaskSearchGeneticsData', 'child' => 'OprnAdvancedSearch'));
    }

    public function down()
    {
    }
}
