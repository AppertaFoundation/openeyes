<?php

class m170516_101300_CXL_dataset_RBAC extends CDbMigration
{
    public function up()
    {
        $this->insert('authitem', array('name' => 'CXL Dataset', 'type' => 2));
    }

    public function down()
    {
        $this->delete('authitem', 'name = "CXL Dataset"');
    }
}
