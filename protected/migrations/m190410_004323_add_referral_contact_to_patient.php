<?php

class m190410_004323_add_referral_contact_to_patient extends OEMigration
{

    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->addColumn('patient', 'patient_referral_id', 'int(10) unsigned');
        $this->addForeignKey('patient_referral_fk', 'patient', 'patient_referral_id', 'gp', 'id');
        $this->addColumn('patient_version', 'patient_referral_id', 'int(10) unsigned');
    }

    public function safeDown()
    {
        $this->dropForeignKey('patient_referral_fk', 'patient');
        $this->dropColumn('patient', 'patient_referral_id');
        $this->dropColumn('patient_version', 'patient_referral_id');
    }

}