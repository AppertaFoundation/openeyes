<?php

class m210729_083100_add_hie_roles extends OEMigration
{
    public function safeUp()
    {
        $this->insert('authitem', array('name' => 'HIE - View', 'type' => 2));
        $this->insert('authitem', array('name' => 'HIE - Admin', 'type' => 2));
        $this->insert('authitem', array('name' => 'HIE - Summary', 'type' => 2));
        $this->insert('authitem', array('name' => 'HIE - Extended', 'type' => 2));
    }

    public function safeDown()
    {
        $this->delete('authitem', array('in', 'name', array('HIE - View', 'HIE - Admin', 'HIE - Summary', 'HIE - Extended')));
    }
}
