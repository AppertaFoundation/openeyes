<?php

class m210904_111000_add_et_others_involved_in_the_decision_making_process extends OEMigration
{
    public function safeUp()
    {
        $event_type_id = $this->getIdOfEventTypeByClassName('OphTrConsent');

        $this->insertOEElementType(array('Element_OphTrConsent_OthersInvolvedDecisionMakingProcess' => array(
            'name' => 'Others involved in the decision making process',
            'required' => 1,
            'default' => 1,
            'display_order' => 100,
        )), $event_type_id);

        if ($this->dbConnection->schema->getTable('et_ophtrconsent_others_involved_decision_making_process', true) === null) {
            $this->createOETable(
                'et_ophtrconsent_others_involved_decision_making_process',
                array(
                    'id' => 'pk',
                    'event_id' => 'int(10) unsigned NOT NULL',
                    'CONSTRAINT `ophtrconsent_inv_decision_making_proc_event_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
                ),
                true
            );
        }

        if ($this->dbConnection->schema->getTable('ophtrconsent_others_involved_decision_making_process_contact', true) === null) {
            $this->createOETable(
                'ophtrconsent_others_involved_decision_making_process_contact',
                array(
                    'id' => 'pk',
                    'element_id' => 'int(11) NOT NULL',
                    'contact_type_id' => 'tinyint NOT NULL',
                    'contact_user_id' => 'int(10) unsigned',
                    'first_name' => 'varchar(200) DEFAULT NULL',
                    'last_name' => 'varchar(200) DEFAULT NULL',
                    'email' => 'varchar(255) DEFAULT NULL',
                    'phone_number' => 'varchar(50) DEFAULT NULL',
                    'mobile_number' => 'varchar(50) DEFAULT NULL',
                    'address_line1' => 'varchar(255) DEFAULT NULL',
                    'address_line2' => 'varchar(255) DEFAULT NULL',
                    'city' => 'varchar(100) DEFAULT NULL',
                    'country_id' => 'int(10) unsigned DEFAULT NULL',
                    'postcode' => 'varchar(20) DEFAULT NULL',
                    'consent_patient_relationship_id' => 'int(11) NOT NULL',
                    'consent_patient_contact_method_id' => 'int(11) NOT NULL',
                    'other_relationship' => 'varchar(200) DEFAULT NULL',
                    'other_contact_method' => 'varchar(200) DEFAULT NULL',
                    'comment' => 'text DEFAULT NULL',
                    'contact_signature_id' => 'INT(11) NULL',
                    'signature_required' => 'tinyint NOT NULL'
                ),
                false
            );
        }

        $this->addForeignKey('fk_ophtrconsent_contact_user', 'ophtrconsent_others_involved_decision_making_process_contact', 'contact_user_id', 'user', 'id');
        $this->addForeignKey('fk_ophtrconsent_others_inv_dec_making_proc_contact_et', 'ophtrconsent_others_involved_decision_making_process_contact', 'element_id', 'et_ophtrconsent_others_involved_decision_making_process', 'id');
        $this->addForeignKey('fk_ophtrconsent_others_inv_decision_making_proc_contact_method', 'ophtrconsent_others_involved_decision_making_process_contact', 'consent_patient_contact_method_id', 'ophtrconsent_patient_contact_method', 'id');
        $this->addForeignKey('fk_ophtrconsent_others_inv_decision_making_proc_contact_country', 'ophtrconsent_others_involved_decision_making_process_contact', 'country_id', 'country', 'id');
        $this->addForeignKey('fk_ophtrconsent_others_inv_decision_making_proc_contact_rship', 'ophtrconsent_others_involved_decision_making_process_contact', 'consent_patient_relationship_id', 'ophtrconsent_patient_relationship', 'id');
        $this->addForeignKey('fk_et_ophtrconsent_contact_sign_id', 'ophtrconsent_others_involved_decision_making_process_contact', 'contact_signature_id', 'ophtrconsent_signature', 'id');
    }

    public function safeDown()
    {
        $this->dropOETable('ophtrconsent_others_involved_decision_making_process_contact');
        $this->dropOETable('et_ophtrconsent_others_involved_decision_making_process', true);
        $this->deleteElementType('OphTrConsent', 'Element_OphTrConsent_OthersInvolvedDecisionMakingProcess');
    }
}
