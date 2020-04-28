<?php

class m181105_014350_add_patient_referral_table extends OEMigration
{

  // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->createOETable('patient_referral', array(
        'patient_id' => 'int unsigned NOT NULL PRIMARY KEY',
        'file_content' => 'mediumblob NOT NULL',
        'file_type' => 'varchar(30) NOT NULL',
        'file_size' => 'int unsigned NOT NULL',
        'file_name' => 'varchar(255) NOT NULL',
        'constraint patient_referral_id_fk foreign key (patient_id) references patient (id)',
        ));
    }

    public function safeDown()
    {
        $this->dropOETable('patient_referral');
    }
}
