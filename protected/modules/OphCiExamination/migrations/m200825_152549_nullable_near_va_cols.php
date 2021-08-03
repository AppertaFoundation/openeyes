<?php

/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m200825_152549_nullable_near_va_cols extends OEMigration
{
    // these columns were changed for VA in m200213_111658_fix_columns_with_no_defaults_that_arent_set
    // but not applied to near va, which uses the same model approach
    public function up()
    {
        $this->alterOEColumn('et_ophciexamination_nearvisualacuity', 'left_unable_to_assess', 'TINYINT(1) UNSIGNED DEFAULT NULL', true);
        $this->alterOEColumn('et_ophciexamination_nearvisualacuity', 'right_unable_to_assess', 'TINYINT(1) UNSIGNED DEFAULT NULL', true);
        $this->alterOEColumn('et_ophciexamination_nearvisualacuity', 'left_eye_missing', 'TINYINT(1) UNSIGNED DEFAULT NULL', true);
        $this->alterOEColumn('et_ophciexamination_nearvisualacuity', 'right_eye_missing', 'TINYINT(1) UNSIGNED DEFAULT NULL', true);
        $this->alterOEColumn('et_ophciexamination_nearvisualacuity', 'left_notes', 'TEXT DEFAULT NULL', true);
        $this->alterOEColumn('et_ophciexamination_nearvisualacuity', 'right_notes', 'TEXT DEFAULT NULL', true);
    }

    public function down()
    {
        // no need to revert here.
    }
}
