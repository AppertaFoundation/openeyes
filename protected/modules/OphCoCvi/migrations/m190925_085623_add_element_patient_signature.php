<?php

class m190925_085623_add_element_patient_signature extends OEMigration
{
    public function up()
    {

// Can we delete this file ? Sabi 09/09/2021 // NOPE // SABI-TODO

        $this->createOETable("ophcocvi_consent_consignee", [
            "id" => "pk",
            "name" => "VARCHAR(255)"
        ], true);

        $this->insert("ophcocvi_consent_consignee", ["name" => "GP"]);
        $this->insert("ophcocvi_consent_consignee", ["name" => "Local authority"]);
        $this->insert("ophcocvi_consent_consignee", ["name" => "Royal College of Ophthalmologists"]);

        $this->createOETable("et_ophcocvi_patient_signature", [
            'id' => 'pk',
            'event_id' => 'INT(10) UNSIGNED',
            'protected_file_id' => 'INT(10) UNSIGNED',
            'signature_date' => 'DATE NULL',
            'signatory_person' => 'TINYINT UNSIGNED',
            'signatory_name' => 'VARCHAR(255)',
            'signatory_required' => 'BOOLEAN NOT NULL DEFAULT 0',
            'relationship_status' => 'VARCHAR(255)',
        ], true);

        $this->addForeignKey("fk_et_ophcocvi_patient_signature_event", "et_ophcocvi_patient_signature", "event_id", "event", "id");
        $this->addForeignKey("fk_et_ophcocvi_patient_signature_pf", "et_ophcocvi_patient_signature", "protected_file_id", "protected_file", "id");

        $this->createOETable("et_ophcocvi_patient_signature_consent_consignee_assignment", [
            "id" => "pk",
            "element_id" => "INT(11) NOT NULL",
            "ophcocvi_consent_consignee_id" => "INT(11) NOT NULL"
        ]);

        $this->addForeignKey("fk_et_ophcocvi_pscca_element_id", "et_ophcocvi_patient_signature_consent_consignee_assignment", "element_id", "et_ophcocvi_patient_signature", "id");
        $this->addForeignKey("fk_et_ophcocvi_pscca_consignee_id", "et_ophcocvi_patient_signature_consent_consignee_assignment", "ophcocvi_consent_consignee_id", "ophcocvi_consent_consignee", "id");

        $this->createElementType("OphCoCvi", "Consent Signature", [
            'class_name' => 'OEModule\\OphCoCvi\\models\\Element_OphCoCvi_PatientSignature',
            'default' => true,
            'required' => true,
            'display_order' => 20
        ]);

        // Deprecate old Consent signature

        $this->execute("DELETE FROM element_type WHERE class_name='OEModule\\OphCoCvi\\models\\Element_OphCoCvi_ConsentSignature' AND event_type_id = (SELECT id FROM event_type WHERE event_type.class_name = 'OphCoCvi')");
    }

    public function down()
    {
        $this->execute("DELETE FROM element_type WHERE class_name='OEModule\\OphCoCvi\\models\\Element_OphCoCvi_PatientSignature' AND event_type_id = (SELECT id FROM event_type WHERE event_type.class_name = 'OphCoCvi')");
        $this->dropForeignKey("fk_et_ophcocvi_pscca_element_id", "et_ophcocvi_patient_signature_consent_consignee_assignment");
        $this->dropForeignKey("fk_et_ophcocvi_pscca_consignee_id", "et_ophcocvi_patient_signature_consent_consignee_assignment");
        $this->dropOETable("et_ophcocvi_patient_signature_consent_consignee_assignment", true);
        $this->dropForeignKey("fk_et_ophcocvi_patient_signature_event", "et_ophcocvi_patient_signature");
        $this->dropForeignKey("fk_et_ophcocvi_patient_signature_pf", "et_ophcocvi_patient_signature");
        $this->dropOETable("ophcocvi_consent_consignee", true);
    }
}
