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

class m191119_115805_create_table_delivery_log extends CDbMigration
{
    public function up()
    {
        $this->createTable("ophcocvi_delivery_log", array(
            "id" => "pk",
            "event_id" => "INT(10) UNSIGNED NOT NULL",
            "delivery_to" => "VARCHAR(8)",
            "attempted_at" => "DATETIME",
            "status" => "VARCHAR(16) NULL",
            "error_report" => "TEXT NULL"
        ));

        $this->addForeignKey("fk_ophcocvi_delivery_log_eid", "ophcocvi_delivery_log", "event_id", "event", "id");
    }

    public function down()
    {
        $this->dropForeignKey("fk_ophcocvi_delivery_log_eid", "ophcocvi_delivery_log");
        $this->dropTable("ophcocvi_delivery_log");
    }
}
