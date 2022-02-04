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

class m191105_134501_update_clinicinfo_columns extends CDbMigration
{
    public function up()
    {
        $this->alterColumn("et_ophcocvi_clinicinfo", "eclo", "int(1) DEFAULT NULL");
        $this->alterColumn("et_ophcocvi_clinicinfo", "field_of_vision", "int(1) DEFAULT NULL");
        $this->alterColumn("et_ophcocvi_clinicinfo", "low_vision_service", "int(1) DEFAULT NULL");
        $this->alterColumn("et_ophcocvi_clinicinfo", "best_corrected_right_va_list", "int(1) DEFAULT NULL");
        $this->alterColumn("et_ophcocvi_clinicinfo", "best_recorded_left_va", "int(1) DEFAULT NULL");
        $this->alterColumn("et_ophcocvi_clinicinfo", "best_recorded_right_va", "int(1) DEFAULT NULL");
        $this->alterColumn("et_ophcocvi_clinicinfo", "best_recorded_binocular_va", "int(1) DEFAULT NULL");
    }

    public function down()
    {
        return true;
    }
}
