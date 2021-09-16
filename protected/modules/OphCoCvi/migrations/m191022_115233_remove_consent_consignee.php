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

class m191022_115233_remove_consent_consignee extends OEMigration
{
    public function up()
    {
        $this->dropForeignKey("fk_et_ophcocvi_pscca_element_id", "et_ophcocvi_patient_signature_consent_consignee_assignment");
        $this->dropForeignKey("fk_et_ophcocvi_pscca_consignee_id", "et_ophcocvi_patient_signature_consent_consignee_assignment");

        $this->dropOETable("et_ophcocvi_patient_signature_consent_consignee_assignment", false);
        $this->dropOETable("ophcocvi_consent_consignee", true);

        $this->addOEColumn("et_ophcocvi_patient_signature", "consented_to_gp", "BOOLEAN NOT NULL", true);
        $this->addOEColumn("et_ophcocvi_patient_signature", "consented_to_la", "BOOLEAN NOT NULL", true);
        $this->addOEColumn("et_ophcocvi_patient_signature", "consented_to_rcop", "BOOLEAN NOT NULL", true);
    }

    public function down()
    {
        $this->dropOEColumn("et_ophcocvi_patient_signature", "consented_to_gp", true);
        $this->dropOEColumn("et_ophcocvi_patient_signature", "consented_to_la", true);
        $this->dropOEColumn("et_ophcocvi_patient_signature", "consented_to_rcop", true);

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
