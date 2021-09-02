<?php

class m210808_152600_element_patient_attorney_deputy extends OEMigration
{
    public function up()
    {
        if (!$this->dbConnection->schema->getTable('et_ophtrconsent_patient_attorney_deputy', true)) {
            $this->createOETable("et_ophtrconsent_patient_attorney_deputy", array(
                'id' => 'pk',
                'event_id' => 'INT(10) UNSIGNED',
                'comments' => 'TEXT'
            ), true);


            $this->addForeignKey("fk_et_ophtrconsent_pad_event_id", "et_ophtrconsent_patient_attorney_deputy", "event_id", "event", "id");

            $this->createElementType("OphTrConsent", 'Patient\'s attorney or deputy', array(
                'class_name' => 'OEModule\\OphTrConsent\\models\\Element_OphTrConsent_PatientAttorneyDeputy',
                'default' => true,
                'required' => true,
                'display_order' => 80
            ));
        }
    }

    public function down()
    {
        if ($this->dbConnection->schema->getTable('et_ophtrconsent_patient_attorney_deputy', true)) {
            $this->execute("DELETE FROM element_type WHERE name = \"Patient\'s attorney or deputy\" AND event_type_id = (SELECT id FROM event_type WHERE event_type.class_name = \"OphTrConsent\");");
            $this->dropForeignKey("fk_et_ophtrconsent_pad_event_id", "et_ophtrconsent_patient_attorney_deputy");
            $this->dropOETable("et_ophtrconsent_patient_attorney_deputy", true);
        }
    }
}
