<?php

class m160610_130617_nhs_num_verification extends OEMigration
{
    public function up()
    {
        $this->createOETable('nhs_number_verification_status', array(
            'id' => 'pk',
            'code' => 'varchar(2) not null',
            'description' => 'tinytext not null',
        ));
        $this->createIndex('code_unique', 'nhs_number_verification_status', 'code');

        $codes = array(
            '01' => 'Number present and verified',
            '02' => 'Number present but not traced',
            '03' => 'Trace required',
            '04' => 'Trace attempted - No match or multiple match found',
            '05' => 'Trace needs to be resolved - (NHS Number or PATIENT detail conflict)',
            '06' => 'Trace in progress',
            '07' => 'Number not present and trace not required',
            '08' => 'Trace postponed (baby under six weeks old)',
        );

        foreach ($codes as $code => $description) {
            $this->insert('nhs_number_verification_status', array('code' => $code, 'description' => $description));
        }

        $this->addColumn('patient', 'nhs_num_status_id', 'int(11)');
        $this->addColumn('patient_version', 'nhs_num_status_id', 'int(11)');
        $this->addForeignKey('patient_nhs_num_status_fk', 'patient', 'nhs_num_status_id', 'nhs_number_verification_status', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('patient_nhs_num_status_fk', 'patient');
        $this->dropColumn('patient_version', 'nhs_num_status_id');
        $this->dropColumn('patient', 'nhs_num_status_id');
        $this->dropOETable('nhs_number_verification_status');
    }
}
