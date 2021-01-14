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

class m200302_134907_multi_unit_va extends OEMigration
{
    protected $va_tables = [
        ['ophciexamination_visualacuity_reading', 'et_ophciexamination_visualacuity'],
        ['ophciexamination_nearvisualacuity_reading', 'et_ophciexamination_nearvisualacuity']
    ];

    public function safeUp()
    {
        foreach ($this->va_tables as $tables) {
            $reading_table = $tables[0];
            $element_table = $tables[1];
            $reading_table_version = $reading_table . "_version";
            $element_table_version = $element_table . "_version";

            $this->addOEColumn(
                $reading_table,
                'unit_id',
                'int(10) unsigned',
                true
            );

            $this->execute(<<<EOSQL
UPDATE $reading_table reading
LEFT JOIN $element_table element
ON reading.element_id = element.id
SET reading.unit_id = element.unit_id
EOSQL
            );

            $this->execute(<<<EOSQL
UPDATE $reading_table_version reading
LEFT JOIN $element_table_version element
ON reading.element_id = element.id
AND reading.version_date = element.version_date
SET reading.unit_id = element.unit_id
EOSQL
            );

            // the versions don't always line up exactly because the readings are saved independently of the element.
            // so there can be a second or so difference. This accounts for those:
            $this->execute(<<<EOSQL
UPDATE $reading_table_version reading
LEFT JOIN $element_table_version el
ON
el.version_id = (
    SELECT version_id
    FROM $element_table_version join_el
    WHERE join_el.id = reading.element_id
    ORDER BY ABS(TIMEDIFF(join_el.version_date, reading.version_date))
    LIMIT 1)
SET reading.unit_id = el.unit_id
WHERE reading.unit_id IS NULL
EOSQL
            );

            $this->alterOEColumn($reading_table, 'unit_id', 'int(10) unsigned NOT NULL', true);
            $this->addForeignKey($reading_table . '_unit_fk',
                $reading_table, 'unit_id',
                'ophciexamination_visual_acuity_unit', 'id');

            $this->dropForeignKey($element_table . '_unit_fk', $element_table);
            $this->alterOEColumn($element_table, 'unit_id', 'int(10) unsigned', true);
            $this->renameOEColumn($element_table, 'unit_id', 'archive_unit_id', true);

            $this->addOEColumn($element_table, 'record_mode', "varchar(15) not null default 'simple'", true);
        }
    }

    public function safeDown()
    {
        foreach ($this->va_tables as $tables) {
            $reading_table = $tables[0];
            $element_table = $tables[1];

            $this->dropOEColumn($element_table, 'record_mode', true);
            // this will fall over if we are trying to migrate down after data changes which we would want
            // as it will require us working out what the unit id should be on the element table from new
            // readings.
            $this->alterOEColumn($element_table, 'archive_unit_id', 'int(10) unsigned NOT NULL', true);
            $this->renameOEColumn($element_table, 'archive_unit_id', 'unit_id', true);
            $this->addForeignKey($element_table . '_unit_fk',
                $element_table, 'unit_id',
                'ophciexamination_visual_acuity_unit', 'id');

            $this->dropForeignKey($reading_table . '_unit_fk', $reading_table);
            $this->dropOEColumn($reading_table, 'unit_id', true);
        }
    }
}