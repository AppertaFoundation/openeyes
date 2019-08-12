<?php

class m190613_004744_add_multiple_contacts_to_patient extends OEMigration
{

    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->createOETable('patient_contact_associate', array(
            'id' => 'pk',
            'patient_id' => 'int(10) unsigned NOT NULL',
            'gp_id' => 'int(10) unsigned NOT NULL',
        ), true);

        $this->addForeignKey('patient_contact_associate_patient_fk', 'patient_contact_associate', 'patient_id', 'patient', 'id');
        $this->addForeignKey('patient_contact_associate_gp_fk', 'patient_contact_associate', 'gp_id', 'gp', 'id');

        $this->createOETable('contact_practice_associate', array(
            'id' => 'pk',
            'gp_id' => 'int(10) unsigned NOT NULL',
            'practice_id' => 'int(10) unsigned NOT NULL',
        ), true);

        $this->addForeignKey('contact_practice_associate_practice_fk', 'contact_practice_associate', 'practice_id', 'practice', 'id');
        $this->addForeignKey('contact_practice_associate_gp_fk', 'contact_practice_associate', 'gp_id', 'gp', 'id');

    }

    public function safeDown()
    {
        $this->dropForeignKey('patient_contact_associate_patient_fk', 'patient_contact_associate');
        $this->dropForeignKey('patient_contact_associate_gp_fk', 'patient_contact_associate');

        $this->dropForeignKey('contact_practice_associate_practice_fk', 'contact_practice_associate');
        $this->dropForeignKey('contact_practice_associate_gp_fk', 'contact_practice_associate');


        $this->dropOETable('patient_contact_associate', true);
        $this->dropOETable('contact_practice_associate', true);

    }
}