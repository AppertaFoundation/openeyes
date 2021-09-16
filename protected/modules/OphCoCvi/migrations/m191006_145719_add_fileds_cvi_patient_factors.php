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

class m191006_145719_add_fileds_cvi_patient_factors extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('ophcocvi_clericinfo_patient_factor', 'comments_only', "tinyint(1) unsigned NOT NULL DEFAULT '0'", true);
        $this->addOEColumn('ophcocvi_clericinfo_patient_factor', 'yes_no_only', "tinyint(1) unsigned NOT NULL DEFAULT '0'", true);
        $this->addOEColumn('ophcocvi_clericinfo_patient_factor', 'event_type_version', "int(4) unsigned NOT NULL DEFAULT '0'", true);
    }

    public function down()
    {
        $this->dropOEColumn('ophcocvi_clericinfo_patient_factor', 'comments_only', true);
        $this->dropOEColumn('ophcocvi_clericinfo_patient_factor_version', 'comments_only', true);
        $this->dropOEColumn('ophcocvi_clericinfo_patient_factor', 'yes_no_only', true);
        $this->dropOEColumn('ophcocvi_clericinfo_patient_factor_version', 'yes_no_only', true);
        $this->dropOEColumn('ophcocvi_clericinfo_patient_factor', 'event_type_version', true);
        $this->dropOEColumn('ophcocvi_clericinfo_patient_factor_version', 'event_type_version', true);
    }
}
