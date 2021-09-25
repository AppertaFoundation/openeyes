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

class m191119_101121_add_eventinfo_fields extends CDbMigration
{
    public function up()
    {
        foreach (["et_ophcocvi_eventinfo", "et_ophcocvi_eventinfo_version"] as $table) {
            $this->addColumn($table, "gp_delivery", "BOOLEAN NOT NULL");
            $this->addColumn($table, "gp_delivery_status", "VARCHAR(16) NULL");

            $this->addColumn($table, "la_delivery", "BOOLEAN NOT NULL");
            $this->addColumn($table, "la_delivery_status", "VARCHAR(16) NULL");

            $this->addColumn($table, "rco_delivery", "BOOLEAN NOT NULL");
            $this->addColumn($table, "rco_delivery_status", "VARCHAR(16) NULL");
        }
    }

    public function down()
    {
        foreach (["et_ophcocvi_eventinfo", "et_ophcocvi_eventinfo_version"] as $table) {
            $this->dropColumn($table, "gp_delivery");
            $this->dropColumn($table, "gp_delivery_status");

            $this->dropColumn($table, "la_delivery");
            $this->dropColumn($table, "la_delivery_status");

            $this->dropColumn($table, "rco_delivery");
            $this->dropColumn($table, "rco_delivery_status");
        }
    }
}
