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

class m210826_110243_import_legacy_consultant_signatures extends OEMigration
{
    private const LEGACY_ET_TABLE = "et_consultant_signature";
    private const LEGACY_ET_PATIENT_SIGNATURE =  "et_ophcocvi_consentsig";
    private const ET_TABLE = "et_ophcocvi_esign";
    private const ITEM_TABLE = "ophcocvi_signature";

    public function safeUp()
    {
        if ($this->dbConnection->schema->getTable(self::LEGACY_ET_TABLE)) {
            $evt_type_id = $this->dbConnection
                ->createCommand("SELECT `id` FROM `event_type` WHERE `class_name` = 'OphCoCvi';")
                ->queryScalar();
            $this->execute("INSERT INTO ".self::ET_TABLE."
                    (event_id, last_modified_user_id, last_modified_date, created_user_id, created_date)
                    SELECT
                    a.event_id, a.last_modified_user_id, a.last_modified_date, a.created_user_id, a.created_date
                    FROM ".self::LEGACY_ET_TABLE." AS a
                    LEFT JOIN event ON event.id = a.event_id
                    WHERE event.event_type_id = $evt_type_id
                    AND a.protected_file_id IS NOT NULL
                    AND a.event_id NOT IN (SELECT x.event_id FROM ".self::ET_TABLE." AS x)
                ");
            $this->execute("INSERT INTO ".self::ITEM_TABLE."
                    (element_id, type, signature_file_id, signatory_role, signatory_name, `timestamp`,
                    last_modified_user_id, last_modified_date, created_user_id, created_date)
                    SELECT
                    e.id, 1, le.protected_file_id, 'Consultant',
                    CONCAT(user.first_name, ' ', user.last_name),
                    UNIX_TIMESTAMP(le.signature_date),
                    le.last_modified_user_id, le.last_modified_date, le.created_user_id, le.created_date
                    FROM ".self::LEGACY_ET_TABLE." AS le
                    LEFT JOIN ".self::ET_TABLE." AS e ON e.event_id = le.event_id
                    LEFT JOIN `event` ON `event`.id = le.event_id
                    LEFT JOIN `user` ON `user`.id = le.signed_by_user_id
                    WHERE event.event_type_id = $evt_type_id
                    AND le.protected_file_id IS NOT NULL;"
            );
        }
    }

    public function safeDown()
    {
        $this->execute("DELETE FROM ".self::ITEM_TABLE." WHERE 1 = 1");
        $this->execute("DELETE FROM ".self::ET_TABLE." WHERE 1 = 1");
    }
}
