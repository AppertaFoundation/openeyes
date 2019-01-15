<?php

class m190115_004629_add_service_manager_role extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->insert('authitem', array('name' => 'Service Manager', 'type' => 2));
    }

    public function safeDown()
    {
        $this->delete('authitem', 'name = "Service Manager"');
    }
}