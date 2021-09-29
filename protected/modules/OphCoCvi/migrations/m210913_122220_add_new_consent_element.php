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

class m210913_122220_add_new_consent_element extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable("et_ophcocvi_consent", [
            'id' => 'pk',
            'event_id' => 'INT(10) UNSIGNED',
            "consented_to_gp" => "BOOLEAN NOT NULL",
            "consented_to_la" => "BOOLEAN NOT NULL",
            "consented_to_rcop" => "BOOLEAN NOT NULL"
        ], true);

        $this->createElementType("OphCoCvi", "Consent", [
            'class_name' => 'OEModule\\OphCoCvi\\models\\Element_OphCoCvi_Consent',
            'default' => true,
            'required' => true,
            'display_order' => 20
        ]);

        $this->migrateData();
    }

    public function migrateData()
    {
        $query = <<<EOT
INSERT INTO et_ophcocvi_consent (event_id, consented_to_gp, consented_to_la, consented_to_rcop)
SELECT event_id, consented_to_gp, consented_to_la, consented_to_rcop
FROM `et_ophcocvi_patient_signature`
EOT;
        $this->execute($query);
    }

    public function safeDown()
    {
        $this->dropTable('et_ophcocvi_consent');
        $this->deleteElementType('OphCoCvi','OEModule\\OphCoCvi\\models\\Element_OphCoCvi_Consent');
    }
}
