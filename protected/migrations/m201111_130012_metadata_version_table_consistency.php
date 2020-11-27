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

/**
 * Class m201111_130012_metadata_version_table_consistency
 *
 * an old migration m191001_024153_set_default_country did not adjust the version table
 * for setting metadata. This migration rectifies this oversight.
 */
class m201111_130012_metadata_version_table_consistency extends OEMigration
{
    public function up()
    {
        $this->alterColumn('setting_metadata_version', 'data', 'varchar(16384)');
    }

    public function down()
    {
        $this->alterColumn('setting_metadata_version', 'data', ' varchar(4096) NOT NULL');
    }
}
