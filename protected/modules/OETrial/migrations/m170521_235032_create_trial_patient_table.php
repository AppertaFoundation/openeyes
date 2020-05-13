<?php

class m170521_235032_create_trial_patient_table extends OEMigration
{
    const VERSIONED = true;

    public function up()
    {
        $this->createOETable('trial_patient_status', array(
            'id' => 'pk',
            'name' => 'varchar(64) NOT NULL',
            'code' => 'varchar(64) NOT NULL',
            'display_order' => 'integer NOT NULL',
        ));

        $this->insert('trial_patient_status', array(
            'name' => 'Accepted',
            'code' => 'ACCEPTED',
            'display_order' => 10,
        ));
        $this->insert('trial_patient_status', array(
            'name' => 'Shortlisted',
            'code' => 'SHORTLISTED',
            'display_order' => 20,
        ));
        $this->insert('trial_patient_status', array(
            'name' => 'Rejected',
            'code' => 'REJECTED',
            'display_order' => 30,
        ));

        $this->createOETable('treatment_type', array(
            'id' => 'pk',
            'name' => 'varchar(64) NOT NULL',
            'code' => 'varchar(64) NOT NULL',
        ));

        $this->insert('treatment_type', array(
            'name' => 'Unknown',
            'code' => 'UNKNOWN',
        ));
        $this->insert('treatment_type', array(
            'name' => 'Intervention',
            'code' => 'INTERVENTION',
        ));
        $this->insert('treatment_type', array(
            'name' => 'Placebo',
            'code' => 'PLACEBO',
        ));

        $this->createOETable('trial_patient', array(
            'id' => 'pk',
            'external_trial_identifier' => 'varchar(100) collate utf8_bin',
            'trial_id' => 'int(11) NOT NULL',
            'patient_id' => 'int(10) unsigned NOT NULL',
            'status_id' => 'int(10) NOT NULL',
            'treatment_type_id' => 'int(10) NOT NULL',
        ), self::VERSIONED);

        $this->addForeignKey('trial_patient_trial_fk', 'trial_patient', 'trial_id', 'trial', 'id');
        $this->addForeignKey(
            'trial_patient_trial_patient_status_fk',
            'trial_patient',
            'status_id',
            'trial_patient_status',
            'id'
        );
        $this->addForeignKey('trial_patient_patient_fk', 'trial_patient', 'patient_id', 'patient', 'id');
        $this->addForeignKey(
            'treatment_type_fk',
            'trial_patient',
            'treatment_type_id',
            'treatment_type',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable('trial_patient', self::VERSIONED);
        $this->dropOETable('trial_patient_status');
        $this->dropOETable('treatment_type');
    }
}
