<?php

class m190404_033510_add_patient_status_updated_date_in_trial extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->addColumn('trial_patient', 'status_update_date', 'datetime');
        $this->addColumn('trial_patient_version', 'status_update_date', 'datetime');

    }

    public function safeDown()
    {
        $this->dropColumn('trial_patient', 'status_update_date');
        $this->dropColumn('trial_patient_version', 'status_update_date');
    }
}