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

class m220505_114520_migrate_disorders_and_sections_remove_versions_column extends \OEMigration
{
    public function safeUp()
    {
        $this->dropOEColumn('ophcocvi_clinicinfo_disorder_section_version', 'event_type_version');
        $this->dropOEColumn('ophcocvi_clinicinfo_disorder_version', 'event_type_version');

        $this->execute("ALTER TABLE ophcocvi_clinicinfo_disorder_section_version MODIFY comments_label VARCHAR(128) DEFAULT NULL");
    }

    public function safeDown()
    {
        // At this point, tables and their version tables are different, no point migrating back to a broken state
        echo "The m220505_114520_migrate_disorders_and_sections_remove_versions_column migration does not support down.";
    }
}
