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

class m210830_111957_import_legacy_patient_signatures extends OEMigration
{
    private const LEGACY_ET_TABLE =  "et_ophcocvi_patient_signature";
    private const ET_TABLE = "et_ophcocvi_esign";
    private const ITEM_TABLE = "ophcocvi_signature";

    public function safeUp()
    {
        if ($this->dbConnection->schema->getTable(self::LEGACY_ET_TABLE)) {
            $this->execute("
                UPDATE ".self::LEGACY_ET_TABLE." oc
                    LEFT JOIN `protected_file` pf ON pf.id = oc.protected_file_id
                SET oc.signature_date = pf.last_modified_date 
                WHERE 
                    UNIX_TIMESTAMP(oc.signature_date) IS NULL 
                    AND oc.protected_file_id IS NOT NULL
                ;
            ");

            $this->execute("INSERT INTO ".self::ET_TABLE."
                    (event_id, last_modified_user_id, last_modified_date, created_user_id, created_date)
                    SELECT
                    event_id, last_modified_user_id, last_modified_date, created_user_id, created_date
                    FROM ".self::LEGACY_ET_TABLE."
                    WHERE protected_file_id IS NOT NULL
                    AND event_id NOT IN (SELECT x.event_id FROM ".self::ET_TABLE." AS x)
                ");
            $this->execute("INSERT INTO ".self::ITEM_TABLE."
                    (element_id, type, signature_file_id, signatory_role, signatory_name, `timestamp`,
                    last_modified_user_id, last_modified_date, created_user_id, created_date)
                    SELECT
                    e.id, 3, le.protected_file_id,
                    CASE 
                        WHEN le.signatory_person = 1 THEN 'Patient'
                        WHEN le.signatory_person = 2 THEN 'Patient\'s representative'
                        WHEN le.signatory_person = 5 THEN 'Parent/Guardian'
                    END,
                    le.signatory_name,
                    UNIX_TIMESTAMP(le.signature_date),
                    le.last_modified_user_id, le.last_modified_date, le.created_user_id, le.created_date
                    FROM ".self::LEGACY_ET_TABLE." AS le
                    LEFT JOIN ".self::ET_TABLE." AS e ON e.event_id = le.event_id
                    WHERE le.protected_file_id IS NOT NULL;"
            );
        }
    }

    public function safeDown()
    {
        $this->execute("DELETE FROM ".self::ITEM_TABLE." WHERE 1 = 1");
        $this->execute("DELETE FROM ".self::ET_TABLE." WHERE 1 = 1");
    }
}
