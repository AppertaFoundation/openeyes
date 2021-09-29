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

class m190930_121855_add_clinical_info_version2_fields extends OEMigration
{
    public function up()
    {
        $this->addColumn('et_ophcocvi_clinicinfo', 'information_booklet', 'tinyint(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'information_booklet', 'tinyint(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo', 'eclo', 'int(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'eclo', 'int(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo', 'field_of_vision', 'int(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'field_of_vision', 'int(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo', 'low_vision_service', 'int(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'low_vision_service', 'int(1) unsigned');

        $this->addColumn('et_ophcocvi_clinicinfo', 'best_recorded_right_va', 'tinyint(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'best_recorded_right_va', 'tinyint(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo', 'best_corrected_right_va_list', 'int(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'best_corrected_right_va_list', 'int(1) unsigned');

        $this->addColumn('et_ophcocvi_clinicinfo', 'best_recorded_left_va', 'tinyint(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'best_recorded_left_va', 'tinyint(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo', 'best_corrected_left_va_list', 'int(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'best_corrected_left_va_list', 'int(1) unsigned');

        $this->addColumn('et_ophcocvi_clinicinfo', 'best_recorded_binocular_va', 'tinyint(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'best_recorded_binocular_va', 'tinyint(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo', 'best_corrected_binocular_va_list', 'int(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'best_corrected_binocular_va_list', 'int(1) unsigned');
    }

    public function down()
    {
        $this->dropColumn('et_ophcocvi_clinicinfo', 'information_booklet');
        $this->dropColumn('et_ophcocvi_clinicinfo_version', 'information_booklet');
        $this->dropColumn('et_ophcocvi_clinicinfo', 'eclo');
        $this->dropColumn('et_ophcocvi_clinicinfo_version', 'eclo');

        $this->dropColumn('et_ophcocvi_clinicinfo', 'best_recorded_right_va');
        $this->dropColumn('et_ophcocvi_clinicinfo_version', 'best_recorded_right_va');
        $this->dropColumn('et_ophcocvi_clinicinfo', 'best_corrected_right_va_list');
        $this->dropColumn('et_ophcocvi_clinicinfo_version', 'best_corrected_right_va_list');

        $this->dropColumn('et_ophcocvi_clinicinfo', 'best_recorded_left_va');
        $this->dropColumn('et_ophcocvi_clinicinfo_version', 'best_recorded_left_va');
        $this->dropColumn('et_ophcocvi_clinicinfo', 'best_corrected_left_va_list');
        $this->dropColumn('et_ophcocvi_clinicinfo_version', 'best_corrected_left_va_list');

        $this->dropColumn('et_ophcocvi_clinicinfo', 'best_recorded_binocular_va');
        $this->dropColumn('et_ophcocvi_clinicinfo_version', 'best_recorded_binocular_va');
        $this->dropColumn('et_ophcocvi_clinicinfo', 'best_corrected_binocular_va_list');
        $this->dropColumn('et_ophcocvi_clinicinfo_version', 'best_corrected_binocular_va_list');
    }
}
