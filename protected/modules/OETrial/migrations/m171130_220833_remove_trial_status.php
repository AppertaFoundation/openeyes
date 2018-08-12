<?php

class m171130_220833_remove_trial_status extends OEMigration
{
    public function safeUp()
    {
        $this->upgradeTable('trial');
        $this->upgradeTable('trial_version');
    }

    private function upgradeTable($tableName)
    {
        $this->addColumn($tableName, 'is_open', 'int(1) NOT NULL');
        $this->execute("UPDATE $tableName SET is_open = status IN ('In_Progress', 'Open')");
        $this->dropColumn($tableName, 'status');
    }

    public function safeDown()
    {
        $this->downgradeTable('trial');
        $this->downgradeTable('trial_version');
    }

    private function downgradeTable($tableName)
    {
        $this->addColumn($tableName, 'status', 'VARCHAR(20)');
        $this->execute("UPDATE $tableName SET status = CASE WHEN is_open THEN 'In_Progress' ELSE 'Closed' END");
        $this->dropColumn($tableName, 'is_open');
    }
}