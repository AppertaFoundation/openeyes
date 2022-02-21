<?php

class m200914_094722_drop_hos_num_and_nhs_num_and_nhs_num_status_id_from_patient_table extends OEMigration
{
    public function safeUp()
    {
        $this->dropOEColumn('patient', 'hos_num', true);
        $this->dropOEColumn('patient', 'nhs_num', true);
        $this->dropOEColumn('patient', 'nhs_num_status_id', true);
    }

    public function safeDown()
    {
        echo "m200908_094722_drop_hos_num_and_nhs_num_and_nhs_num_status_id_from_patient_table does not support migration down.\n";
        return false;
    }
}
