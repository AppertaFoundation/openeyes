<?php

class m140917_142317_super_scheduling_role extends CDbMigration
{
    public function up()
    {
        $this->insert('authitem', array('name' => 'Super schedule operation', 'type' => 2));
    }

    public function down()
    {
        $this->delete('authitem', "name = 'Super schedule operation'");
    }
}
