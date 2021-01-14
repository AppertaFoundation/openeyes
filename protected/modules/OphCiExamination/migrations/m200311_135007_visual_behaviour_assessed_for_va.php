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

class m200311_135007_visual_behaviour_assessed_for_va extends OEMigration
{
    protected const VA_TABLES = ['et_ophciexamination_visualacuity', 'et_ophciexamination_nearvisualacuity'];

    public function up()
    {
        foreach (self::VA_TABLES as $table) {
            foreach (['beo', 'right', 'left'] as $side) {
                $this->addOEColumn($table, "{$side}_behaviour_assessed", "boolean", true);
            }

        }
    }

    public function down()
    {
        foreach (self::VA_TABLES as $table) {
            foreach (['beo', 'right', 'left'] as $side) {
                $this->dropOEColumn($table, "{$side}_behaviour_assessed", true);
            }
        }
    }
}
