<?php

class m210727_145407_add_secretary_role extends OEMigration
{
    public function safeUp()
    {
        $this->addRole('Secretary');
        $this->addTask('SignEvent');
        $this->addTaskToRole('SignEvent', 'Secretary');
    }

    public function safeDown()
    {
        $this->removeTaskFromRole('SignEvent', 'Secretary');
        $this->removeTask('SignEvent');
        $this->removeRole('Secretary');
    }
}