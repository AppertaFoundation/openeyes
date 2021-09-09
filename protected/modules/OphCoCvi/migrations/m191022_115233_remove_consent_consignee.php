<?php

class m191022_115233_remove_consent_consignee extends OEMigration
{
    public function up()
    {

// Can we delete this file ? Sabi 09/09/2021

        $this->dropForeignKey("fk_et_ophcocvi_pscca_element_id", "et_ophcocvi_patient_signature_consent_consignee_assignment");
        $this->dropForeignKey("fk_et_ophcocvi_pscca_consignee_id", "et_ophcocvi_patient_signature_consent_consignee_assignment");

        $this->dropOETable("et_ophcocvi_patient_signature_consent_consignee_assignment", false);
        $this->dropOETable("ophcocvi_consent_consignee", true);

        $this->addColumn("et_ophcocvi_patient_signature", "consented_to_gp", "BOOLEAN NOT NULL");
        $this->addColumn("et_ophcocvi_patient_signature_version", "consented_to_gp", "BOOLEAN NOT NULL");
        $this->addColumn("et_ophcocvi_patient_signature", "consented_to_la", "BOOLEAN NOT NULL");
        $this->addColumn("et_ophcocvi_patient_signature_version", "consented_to_la", "BOOLEAN NOT NULL");
        $this->addColumn("et_ophcocvi_patient_signature", "consented_to_rcop", "BOOLEAN NOT NULL");
        $this->addColumn("et_ophcocvi_patient_signature_version", "consented_to_rcop", "BOOLEAN NOT NULL");
    }

    public function down()
    {
        $this->dropColumn("et_ophcocvi_patient_signature", "consented_to_gp");
        $this->dropColumn("et_ophcocvi_patient_signature_version", "consented_to_gp");
        $this->dropColumn("et_ophcocvi_patient_signature", "consented_to_la");
        $this->dropColumn("et_ophcocvi_patient_signature_version", "consented_to_la");
        $this->dropColumn("et_ophcocvi_patient_signature", "consented_to_rcop");
        $this->dropColumn("et_ophcocvi_patient_signature_version", "consented_to_rcop");

        $this->createOETable("ophcocvi_consent_consignee", [
            "id" => "pk",
            "name" => "VARCHAR(255)"
        ], true);

        $this->insert("ophcocvi_consent_consignee", ["name" => "GP"]);
        $this->insert("ophcocvi_consent_consignee", ["name" => "Local authority"]);
        $this->insert("ophcocvi_consent_consignee", ["name" => "Royal College of Ophthalmologists"]);

        $this->addForeignKey("fk_et_ophcocvi_patient_signature_event", "et_ophcocvi_patient_signature", "event_id", "event", "id");
        $this->addForeignKey("fk_et_ophcocvi_patient_signature_pf", "et_ophcocvi_patient_signature", "protected_file_id", "protected_file", "id");

        $this->createOETable("et_ophcocvi_patient_signature_consent_consignee_assignment", [
            "id" => "pk",
            "element_id" => "INT(11) NOT NULL",
            "ophcocvi_consent_consignee_id" => "INT(11) NOT NULL"
        ]);

        $this->addForeignKey("fk_et_ophcocvi_pscca_element_id", "et_ophcocvi_patient_signature_consent_consignee_assignment", "element_id", "et_ophcocvi_patient_signature", "id");
        $this->addForeignKey("fk_et_ophcocvi_pscca_consignee_id", "et_ophcocvi_patient_signature_consent_consignee_assignment", "ophcocvi_consent_consignee_id", "ophcocvi_consent_consignee", "id");
    }
}
