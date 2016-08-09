<?php

class m160311_105813_NOD_export_RBAC extends CDbMigration
{
    public function up()
    {
        $this->insert('authitem', array('name' => 'NOD Export', 'type' => 2));
    }

    public function down()
    {
        $this->delete('authitem', 'name = "NOD Export"');
    }
}
