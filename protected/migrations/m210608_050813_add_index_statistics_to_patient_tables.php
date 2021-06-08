<?php

class m210608_050813_add_index_statistics_to_patient_tables extends OEMigration
{
    public function up()
    {
        $this->execute('ANALYZE TABLE patient PERSISTENT FOR ALL');
        $this->execute('ANALYZE TABLE contact PERSISTENT FOR ALL');
        $this->execute('ANALYZE TABLE pasapi_assignment PERSISTENT FOR ALL');
        $this->execute('ANALYZE TABLE address PERSISTENT FOR ALL');

        $this->createIndex('patient_nhs_num_idx', 'patient', 'nhs_num');
        $this->createIndex('patient_hos_num_idx', 'patient', 'hos_num');
    }

    public function down()
    {
        $this->dropIndex('patient_nhs_num_idx', 'patient');
        $this->dropIndex('patient_hos_num_idx', 'patient');
    }
}
