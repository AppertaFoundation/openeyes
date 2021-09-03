<?php

class m210608_050813_add_index_statistics_to_patient_tables extends OEMigration
{
    public function up()
    {
        $this->dbConnection->createCommand("ALTER TABLE patient stats_persistent=1")->execute();
        $this->dbConnection->createCommand("ALTER TABLE contact stats_persistent=1")->execute();
        $this->dbConnection->createCommand("ALTER TABLE pasapi_assignment stats_persistent=1")->execute();
        $this->dbConnection->createCommand("ALTER TABLE address stats_persistent=1")->execute();
        $this->execute('ANALYZE TABLE patient');
        $this->execute('ANALYZE TABLE contact');
        $this->execute('ANALYZE TABLE pasapi_assignment');
        $this->execute('ANALYZE TABLE address');

        $this->createIndex('patient_nhs_num_idx', 'patient', 'nhs_num');
        $this->createIndex('patient_hos_num_idx', 'patient', 'hos_num');
    }

    public function down()
    {
        $this->dropIndex('patient_nhs_num_idx', 'patient');
        $this->dropIndex('patient_hos_num_idx', 'patient');
    }
}
