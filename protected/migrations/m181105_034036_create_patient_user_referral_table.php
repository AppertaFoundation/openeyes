<?php

class m181105_034036_create_patient_user_referral_table extends OEMigration
{
  // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->createOETable('patient_user_referral', array(
        'id' => 'pk',
        'patient_id' => 'int(10) unsigned NOT NULL',
        'user_id' => 'int(10) unsigned NOT NULL',
        ), true
        );

        $this->addForeignKey('patient_user_referral_patient', 'patient_user_referral', 'patient_id', 'patient', 'id');
        $this->addForeignKey('patient_user_referral_user', 'patient_user_referral', 'user_id', 'user', 'id');
    }

    public function safeDown()
    {
        $this->dropOETable('patient_user_referral', true);
    }

}