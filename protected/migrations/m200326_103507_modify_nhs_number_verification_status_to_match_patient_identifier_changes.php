<?php

class m200326_103507_modify_nhs_number_verification_status_to_match_patient_identifier_changes extends OEMigration
{
    public function safeUp()
    {
        $this->renameTable('nhs_number_verification_status', 'patient_identifier_status');
        $this->addColumn('patient_identifier_status', 'patient_identifier_type_id', 'int(11)');
        $this->addForeignKey(
            'fk_identifier_status_identifier_type',
            'patient_identifier_status',
            'patient_identifier_type_id',
            'patient_identifier_type',
            'id'
        );

        $global_patient_identifier_id = $this->dbConnection->createCommand('SELECT id FROM patient_identifier_type WHERE usage_type=\'GLOBAL\'')->queryScalar();

        $this->addOEColumn('patient_identifier', 'patient_identifier_status_id', 'int(11)', true);

        $this->update('patient_identifier_status', ['patient_identifier_type_id' => $global_patient_identifier_id]);
        $this->alterColumn('patient_identifier_status', 'patient_identifier_type_id', 'int(11) NOT NULL');

        $patientsDataReader = $this->dbConnection->createCommand('SELECT id, nhs_num_status_id FROM patient')->query();

        foreach ($patientsDataReader as $patient) {
            $this->update(
                'patient_identifier',
                ['patient_identifier_status_id' => $patient['nhs_num_status_id']],
                'patient_id = :patient_id AND patient_identifier_type_id = :patient_identifier_type_id',
                [':patient_id' => $patient['id'] , ':patient_identifier_type_id' => $global_patient_identifier_id]
            );
        }

        $this->dropForeignKey('patient_nhs_num_status_fk', 'patient');

        $this->createIndex('uk_patient_status_patient_identifier_type', 'patient_identifier_status', 'id,patient_identifier_type_id', true);
        $this->addForeignKey(
            'fk_patient_identifier_patient_identifier_status',
            'patient_identifier',
            'patient_identifier_status_id, patient_identifier_type_id',
            'patient_identifier_status',
            'id, patient_identifier_type_id'
        );
    }

    public function safeDown()
    {

        $this->dropForeignKey('fk_patient_identifier_patient_identifier_status', 'patient_identifier');
        $this->dropIndex('uk_patient_status_patient_identifier_type', 'patient_identifier_status');
        $this->dropForeignKey('fk_identifier_status_identifier_type', 'patient_identifier_status');
        $this->addForeignKey('patient_nhs_num_status_fk', 'patient', 'nhs_num_status_id', 'patient_identifier_status', 'id');
        $this->dropColumn('patient_identifier_status', 'patient_identifier_type_id');

        $this->dropOEColumn('patient_identifier', 'patient_identifier_status_id', true);
        $this->renameTable('patient_identifier_status', 'nhs_number_verification_status');
    }
}
