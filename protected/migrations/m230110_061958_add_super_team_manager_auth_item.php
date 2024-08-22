<?php

class m230110_061958_add_super_team_manager_auth_item extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->addRole('Super Team Manager', 'Super manager for all teams');
    }

    public function safeDown()
    {
        echo "m230110_061958_add_super_team_manager_auth_item does not support migration down.\n";
        return false;
    }
}
