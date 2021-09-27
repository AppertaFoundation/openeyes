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

class m160810_101951_clinical_info_diagnosis extends OEMigration
{
    public function up()
    {
        $this->addColumn('et_ophcocvi_clinicinfo_disorder_assignment', 'eye_id', 'int(10) unsigned NOT NULL');
        $this->addForeignKey(
            'et_ophcocvi_clinicinfo_disorder_assignment_eye_fk',
            'et_ophcocvi_clinicinfo_disorder_assignment',
            'eye_id',
            'eye',
            'id'
        );

        $this->addColumn('et_ophcocvi_clinicinfo_disorder_assignment', 'affected', 'tinyint(1) unsigned NOT NULL DEFAULT 0');

    }

    public function down()
    {
        $this->dropForeignKey('et_ophcocvi_clinicinfo_disorder_assignment_eye_fk', 'et_ophcocvi_clinicinfo_disorder_assignment');
        $this->dropColumn('et_ophcocvi_clinicinfo_disorder_assignment', 'eye_id');
        $this->dropColumn('et_ophcocvi_clinicinfo_disorder_assignment', 'affected');
    }

}
