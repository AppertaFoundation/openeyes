<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m190925_085623_add_element_patient_signature extends OEMigration
{
    public function up()
    {

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
        $this->dropOETable("et_ophcocvi_patient_signature_consent_consignee_assignment");
        $this->dropForeignKey("fk_et_ophcocvi_patient_signature_event", "et_ophcocvi_patient_signature");
        $this->dropForeignKey("fk_et_ophcocvi_patient_signature_pf", "et_ophcocvi_patient_signature");
        $this->dropOETable("ophcocvi_consent_consignee", true);
    }
}
