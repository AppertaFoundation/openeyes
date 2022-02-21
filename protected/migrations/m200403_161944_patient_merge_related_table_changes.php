<?php

class m200403_161944_patient_merge_related_table_changes extends OEMigration
{
    public function safeUp()
    {
        $this->renameOEColumn('patient_merge_request', 'primary_hos_num', 'primary_local_identifier_value', true);
        $this->renameOEColumn('patient_merge_request', 'primary_nhsnum', 'primary_global_identifier_value');
        $this->renameOEColumn('patient_merge_request', 'secondary_hos_num', 'secondary_local_identifier_value');
        $this->renameOEColumn('patient_merge_request', 'secondary_nhsnum', 'secondary_global_identifier_value');
    }

    public function safeDown()
    {
        $this->renameOEColumn('patient_merge_request', 'primary_local_identifier_value', 'primary_hos_num', true);
        $this->renameOEColumn('patient_merge_request', 'primary_global_identifier_value', 'primary_nhsnum', true);
        $this->renameOEColumn('patient_merge_request', 'secondary_local_identifier_value', 'secondary_hos_num', true);
        $this->renameOEColumn('patient_merge_request', 'secondary_global_identifier_value', 'secondary_nhsnum', true);
    }
}
