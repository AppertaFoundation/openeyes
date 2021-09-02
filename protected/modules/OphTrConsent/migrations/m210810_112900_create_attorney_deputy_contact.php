<?php

class m210810_112900_create_attorney_deputy_contact extends OEMigration
{
    public function up()
    {
        if (!$this->dbConnection->schema->getTable('ophtrconsent_patient_attorney_deputy_contact', true)) {
            $this->createOETable("ophtrconsent_patient_attorney_deputy_contact", array(
                'id' => 'pk',
                'patient_id' => 'INT(10) UNSIGNED NOT NULL',
                'location_id' => 'INT(10) UNSIGNED DEFAULT NULL',
                'contact_id' => 'INT(10) UNSIGNED DEFAULT NULL',
                'event_id' => 'INT(10) UNSIGNED',
                'authorised_decision_id' => 'INT(11) NOT NULL',
                'considered_decision_id' => 'INT(11) NOT NULL'
            ), true);


            $this->addForeignKey("attorney_deputy_contact_assignment_patient_id_fk", "ophtrconsent_patient_attorney_deputy_contact", "patient_id", "patient", "id");
            $this->addForeignKey("attorney_deputy_contact_assignment_contact_id_fk", "ophtrconsent_patient_attorney_deputy_contact", "contact_id", "contact", "id");
            $this->addForeignKey("attorney_deputy_contact_assignment_location_id_fk", "ophtrconsent_patient_attorney_deputy_contact", "location_id", "contact_location", "id");
            $this->addForeignKey("attorney_deputy_contact_assignment_event_id_fk", "ophtrconsent_patient_attorney_deputy_contact", "event_id", "event", "id");
            $this->addForeignKey("attorney_deputy_contact_assignment_authorised_decision_fk", "ophtrconsent_patient_attorney_deputy_contact", "authorised_decision_id", "ophtrconsent_authorised_decision", "id");
            $this->addForeignKey("attorney_deputy_contact_assignment_considered_decision_fk", "ophtrconsent_patient_attorney_deputy_contact", "considered_decision_id", "ophtrconsent_considered_decision", "id");
        }

        $this->insert('contact_label', array(
            'name' => 'Power of Attorney',
            'is_private' => 1
        ));
    }

    public function down()
    {
        if ($this->dbConnection->schema->getTable('ophtrconsent_patient_attorney_deputy_contact', true)) {
            $this->dropForeignKey("attorney_deputy_contact_assignment_patient_id_fk", "ophtrconsent_patient_attorney_deputy_contact");
            $this->dropForeignKey("attorney_deputy_contact_assignment_contact_id_fk", "ophtrconsent_patient_attorney_deputy_contact");
            $this->dropForeignKey("attorney_deputy_contact_assignment_location_id_fk", "ophtrconsent_patient_attorney_deputy_contact");
            $this->dropForeignKey("attorney_deputy_contact_assignment_event_id_fk", "ophtrconsent_patient_attorney_deputy_contact");
            $this->dropForeignKey("attorney_deputy_contact_assignment_authorised_decision_fk", "ophtrconsent_patient_attorney_deputy_contact");
            $this->dropForeignKey("attorney_deputy_contact_assignment_considered_decision_fk", "ophtrconsent_patient_attorney_deputy_contact");
            $this->dropOETable("ophtrconsent_patient_attorney_deputy_contact", true);
        }

        $this->delete('contact_label', "name='Power of Attorney'");
    }
}
