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

class m210924_105520_add_is_va_to_VAScale extends \OEMigration
{
    protected const UNIT_NAME = '4 card logMAR';

    public function safeUp()
    {
        $this->addOEColumn('ophciexamination_visual_acuity_unit', 'is_va', 'TINYINT(1) NOT NULL DEFAULT 0 AFTER information ',true);

        // if is_near true, than the unit is not present on VA element, to keep it as it is
        // we update the is_va based on the is_near value
        $this->update('ophciexamination_visual_acuity_unit', ['is_va' => 0], 'is_near = 1');
        $this->update('ophciexamination_visual_acuity_unit', ['is_va' => 1], 'is_near = 0');

        // as per requirement add the "4 card logMAR", "logMAR single-letter" to both VA and Near VA
        $this->update('ophciexamination_visual_acuity_unit', [
            'is_va' => 1,
            'is_near' => 1,
        ], 'name IN ("4 card logMAR", "logMAR single-letter")');

        $this->addOEColumn('ophciexamination_visual_acuity_source', 'is_va', 'TINYINT(1) NOT NULL DEFAULT 0 AFTER is_near ',true);

        $this->update('ophciexamination_visual_acuity_source', ['is_va' => 0], 'is_near = 1');
        $this->update('ophciexamination_visual_acuity_source', ['is_va' => 1], 'is_near = 0');

        return true;
    }

    public function safeDown()
    {
        $this->dropOEColumn('ophciexamination_visual_acuity_unit', 'is_va');

        $this->update('ophciexamination_visual_acuity_unit', [
            'is_near' => 0,
        ], 'name IN ("4 card logMAR", "logMAR single-letter")');

        return true;
    }
}
