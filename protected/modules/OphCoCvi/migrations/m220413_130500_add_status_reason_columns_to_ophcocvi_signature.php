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

class m220413_130500_add_status_reason_columns_to_ophcocvi_signature extends \OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('ophcocvi_signature', "status", "BOOLEAN NOT NULL DEFAULT 1", true);
        $this->addOEColumn('ophcocvi_signature', "delete_reason", "VARCHAR(200) NULL", true);
    }

    public function safeDown()
    {
        $this->dropOEColumn('ophcocvi_signature', 'status', true);
        $this->dropOEColumn('ophcocvi_signature', 'delete_reason', true);
    }
}
