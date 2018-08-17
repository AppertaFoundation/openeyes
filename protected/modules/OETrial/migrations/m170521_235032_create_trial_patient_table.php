<?php

class m170521_235032_create_trial_patient_table extends OEMigration
{
    const VERSIONED = true;

    public function up()
    {
        $this->createOETable('trial_patient', array(
            'id' => 'pk',
            'external_trial_identifier' => 'varchar(100) collate utf8_bin',
            'trial_id' => 'int(11) NOT NULL',
            'patient_id' => 'int(10) unsigned NOT NULL',
            'patient_status' => 'int(10) unsigned NOT NULL',
            'treatment_type' => 'int(10) unsigned NOT NULL',
        ), self::VERSIONED
        );

        $this->addForeignKey('trial_patient_trial_fk', 'trial_patient', 'trial_id', 'trial', 'id');
        $this->addForeignKey('trial_patient_patient_fk', 'trial_patient', 'patient_id', 'patient', 'id');
    }

    public function down()
    {
        $this->dropOETable('trial_patient', self::VERSIONED);
    }
}
