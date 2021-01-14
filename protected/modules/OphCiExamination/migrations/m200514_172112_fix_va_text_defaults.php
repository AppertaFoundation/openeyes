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

class m200514_172112_fix_va_text_defaults extends OEMigration
{

    public function up()
    {
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'left_notes', "TEXT");
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'right_notes', "TEXT");
    }

    public function down()
    {
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'left_notes', 'TEXT NOT NULL DEFAULT ""');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'right_notes', 'TEXT NOT NULL DEFAULT ""');
    }
}
