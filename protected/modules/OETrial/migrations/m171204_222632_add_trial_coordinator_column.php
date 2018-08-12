<?php

class m171204_222632_add_trial_coordinator_column extends OEMigration
{
    public function safeUp()
    {
        $this->upgradeTable(false);
        $this->upgradeTable(true);

    }

    private function upgradeTable($versioned)
    {
        $this->addColumn('trial' . ($versioned ? '_version' : null), 'coordinator_user_id', 'int(10) unsigned');
    }

    public function safeDown()
    {
        $this->downgradeTable(false);
        $this->downgradeTable(true);
    }

    private function downgradeTable($versioned)
    {
        $this->dropColumn('trial' . ($versioned ? '_version' : null), 'coordinator_user_id');
    }

}