<?php

class m181228_044926_add_ethics_number_to_trial extends OEMigration
{
    public function safeUp()
    {
        $this->addColumn('trial', 'ethics_number', 'text');
        $this->addColumn('trial_version', 'ethics_number', 'text');
    }

    public function safeDown()
    {
        $this->dropColumn('trial','ethics_number');
        $this->dropColumn('trial_version','ethics_number');
    }
}