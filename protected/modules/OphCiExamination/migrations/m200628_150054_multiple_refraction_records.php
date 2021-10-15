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
 * Class m200628_150054_multiple_refraction_records
 * Transfer single element level records to multiple readings for side.
 * Note we are also removing the axis eyedraw field as this functionality was removed
 * several versions back on the refraction element.
 * This is not restored by the down migration.
 *
 * The down migration will also not restore historical data, and will have indeterminate results
 * if multiple readings for an eye have been recorded (i.e. the new functionality has been used)
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 * @phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
 */
class m200628_150054_multiple_refraction_records extends OEMigration
{
    /**
     * @phpcs:disable Generic.Files.LineLength.TooLong
     */
    public function up()
    {
        $this->createOETable('ophciexamination_refraction_reading', [
            'id' => 'pk',
            'element_id' => 'int(10) unsigned',
            'eye_id' => 'int(11) unsigned',
            'sphere' => 'decimal(5, 2) NOT NULL',
            'cylinder' => 'decimal(5, 2) NOT NULL',
            'axis' => 'int(3) NOT NULL',
            'type_id' => 'int(10) unsigned',
            'type_other' => 'varchar(100)',
        ], true);

        $this->migrateReadingsForSide('left', 1);
        $this->migrateReadingsForSide('right', 2);

        // Adding in Indexes to migrate up quicker
        $this->execute("CREATE INDEX et_ophciexamination_refraction_version_id_idx ON et_ophciexamination_refraction_version(id) USING BTREE;");
        $this->execute("CREATE INDEX ophciexamination_refraction_reading_element_id_idx ON ophciexamination_refraction_reading(element_id) USING BTREE;");
        $this->execute("CREATE INDEX et_ophciexamination_refraction_version_eye_id_idx ON et_ophciexamination_refraction_version(eye_id) USING BTREE;");

        $this->migrateReadingVersionsForSide('left', 1);
        $this->migrateReadingVersionsForSide('right', 2);

        // remove redundant columns from the element
        $fk_exists = $this->dbConnection->createCommand(
            'SELECT count(*)
            FROM information_schema.table_constraints
            WHERE table_schema = DATABASE()
                AND table_name = "et_ophciexamination_refraction"
                AND constraint_name = "et_ophciexamination_refraction_lti_fk"
                AND constraint_type = "FOREIGN KEY"'
        )->queryScalar();
        if ($fk_exists) {
            $this->dropForeignKey('et_ophciexamination_refraction_lti_fk', 'et_ophciexamination_refraction');
        }

        // right key
        $fk_exists = $this->dbConnection->createCommand(
            'SELECT count(*)
            FROM information_schema.table_constraints
            WHERE table_schema = DATABASE()
            AND table_name = "et_ophciexamination_refraction"
            AND constraint_name = "et_ophciexamination_refraction_rti_fk"
            AND constraint_type = "FOREIGN KEY"'
        )->queryScalar();
        if ($fk_exists) {
            $this->dropForeignKey('et_ophciexamination_refraction_rti_fk', 'et_ophciexamination_refraction');
        }

        foreach (['left', 'right'] as $side) {
            foreach (['_sphere', '_cylinder', '_axis', '_axis_eyedraw', '_type_id', '_type_other'] as $column_postfix) {
                $this->dropOEColumn('et_ophciexamination_refraction', "{$side}{$column_postfix}", true);
            }
        }

        // support active flag on reading type
        $this->addOEColumn('ophciexamination_refraction_type', 'active', 'boolean default true', true);
        $this->setupPriorityOrderingForRefractionType();
    }

    public function down()
    {
        $this->dropOEColumn('ophciexamination_refraction_type', 'priority', true);
        $this->dropOEColumn('ophciexamination_refraction_type', 'active', true);

        foreach (['left', 'right'] as $side) {
            $this->addOEColumn('et_ophciexamination_refraction', "{$side}_sphere", 'decimal(5, 2)', true);
            $this->addOEColumn('et_ophciexamination_refraction', "{$side}_cylinder", 'decimal(5, 2)', true);
            $this->addOEColumn('et_ophciexamination_refraction', "{$side}_axis", 'int(3)', true);
            $this->addOEColumn('et_ophciexamination_refraction', "{$side}_axis_eyedraw", 'text', true); // NB values will not be restored
            $this->addOEColumn('et_ophciexamination_refraction', "{$side}_type_id", 'int(10) unsigned', true);
            $this->addOEColumn('et_ophciexamination_refraction', "{$side}_type_other", 'varchar(100)', true);
        }
        $this->addForeignKey(
            'et_ophciexamination_refraction_lti_fk',
            'et_ophciexamination_refraction',
            'left_type_id',
            'ophciexamination_refraction_type',
            'id'
        );
        $this->addForeignKey(
            'et_ophciexamination_refraction_rti_fk',
            'et_ophciexamination_refraction',
            'right_type_id',
            'ophciexamination_refraction_type',
            'id'
        );

        // restore left readings
        $this->execute(<<<EOSQL
UPDATE et_ophciexamination_refraction element, ophciexamination_refraction_reading reading
SET element.left_sphere = reading.sphere,
    element.left_cylinder = reading.cylinder,
    element.left_axis = reading.axis,
    element.left_type_id = reading.type_id,
    element.left_type_other = reading.type_other
WHERE element.id = reading.element_id
AND element.eye_id in (1, 3)
AND reading.eye_id = 1
EOSQL
        );

        // restore right readings
        $this->execute(<<<EOSQL
UPDATE et_ophciexamination_refraction element, ophciexamination_refraction_reading reading
SET element.right_sphere = reading.sphere,
    element.right_cylinder = reading.cylinder,
    element.right_axis = reading.axis,
    element.right_type_id = reading.type_id,
    element.right_type_other = reading.type_other
WHERE element.id = reading.element_id
AND element.eye_id in (2, 3)
AND reading.eye_id = 2
EOSQL
        );

        $this->dropOETable('ophciexamination_refraction_reading', true);
    }

    /**
     * Grabs all the values from the element and creates a reading entry for the given side
     *
     * @param $side
     * @param $eye_id
     */
    protected function migrateReadingsForSide($side, $eye_id)
    {
        $this->execute(<<<EOSQL
INSERT INTO ophciexamination_refraction_reading (element_id, eye_id, sphere, cylinder, axis, type_id, type_other, last_modified_user_id, last_modified_date, created_user_id, created_date)
SELECT id, $eye_id, {$side}_sphere, {$side}_cylinder, {$side}_axis, {$side}_type_id, {$side}_type_other, last_modified_user_id, last_modified_date, created_user_id, created_date
FROM et_ophciexamination_refraction
WHERE eye_id in ($eye_id, 3)
EOSQL
        );
    }

    /**
     * create version records for {$side} readings
     * this covers previous versions for sides that have been maintained on the original element
     * Note that it assumes that it will attach to current reading of the same side as it's parent reading, and fall back
     * to the reading for the other side if that is not present. This is to allow for a version history where the side
     * has switched from one to the other.
     *
     * @param $side
     * @param $eye_id
     */
    protected function migrateReadingVersionsForSide($side, $eye_id)
    {

        $this->execute(<<<EOSQL
INSERT INTO ophciexamination_refraction_reading_version (id, element_id, version_date, eye_id, sphere, cylinder, axis, type_id, type_other, last_modified_user_id, last_modified_date, created_user_id, created_date)
SELECT IFNULL(reading.id, reading_other.id) as reading_id, et.id, et.version_date, $eye_id, et.{$side}_sphere, et.{$side}_cylinder, et.{$side}_axis, et.{$side}_type_id, et.{$side}_type_other, et.last_modified_user_id, et.last_modified_date, et.created_user_id, et.created_date
FROM et_ophciexamination_refraction_version et
LEFT JOIN ophciexamination_refraction_reading reading on reading.element_id = et.id AND reading.eye_id = $eye_id
LEFT JOIN ophciexamination_refraction_reading reading_other on reading_other.element_id = et.id AND reading_other.eye_id != $eye_id
WHERE et.eye_id in ($eye_id, 3)
AND et.{$side}_sphere IS NOT NULL
AND (reading.id IS NOT NULL OR reading_other.id IS NOT NULL)
EOSQL
        );

        // if we can't find a reading id for the version entry, this indicates that we have version history for an element that has subsequently been removed from the event.
        // To maintain the history, we create fake reading ids for these entries, and ensure they are unique by incrementing from
        // the current maximum reading id.
        $this->execute(<<<EOSQL
SET @vid = (select max(id) from ophciexamination_refraction_reading);
INSERT INTO ophciexamination_refraction_reading_version (id, element_id, version_date, eye_id, sphere, cylinder, axis, type_id, type_other, last_modified_user_id, last_modified_date, created_user_id, created_date)
SELECT @vid:=(@vid+1) as reading_id, et.id, et.version_date, $eye_id, et.{$side}_sphere, et.{$side}_cylinder, et.{$side}_axis, et.{$side}_type_id, et.{$side}_type_other, et.last_modified_user_id, et.last_modified_date, et.created_user_id, et.created_date
FROM et_ophciexamination_refraction_version et
LEFT JOIN ophciexamination_refraction_reading reading on reading.element_id = et.id AND reading.eye_id = $eye_id
LEFT JOIN ophciexamination_refraction_reading reading_other on reading_other.element_id = et.id AND reading_other.eye_id != $eye_id
WHERE et.eye_id in ($eye_id, 3)
AND et.{$side}_sphere IS NOT NULL
AND (reading.id IS NULL AND reading_other.id IS NULL)
EOSQL
        );

        $new_max_id_for_readings = $this->dbConnection->createCommand()->select('MAX(id)')->from('ophciexamination_refraction_reading_version')->queryScalar() + 1;
        $this->execute(<<<EOSQL
ALTER TABLE ophciexamination_refraction_reading AUTO_INCREMENT = $new_max_id_for_readings
EOSQL
        );
    }

    protected function setupPriorityOrderingForRefractionType()
    {
        $this->addOEColumn('ophciexamination_refraction_type', 'priority', 'int(2) unsigned', true);

        $max = 0;
        foreach (['optometrist', 'ophthalmologist', 'auto-refraction', 'focimetry'] as $expected_type) {
            $type_id = $this->dbConnection
                ->createCommand()
                ->select('id')
                ->from('ophciexamination_refraction_type')
                ->where('LOWER(name) = ?', [$expected_type])
                ->queryScalar();
            if ($type_id) {
                $this->dbConnection
                    ->createCommand()
                    ->update('ophciexamination_refraction_type', ['priority' => ++$max], 'id = :id', [':id' => $type_id]);
            }
        }
        // check for any others that might have been created, and make them lower priority by default
        $other_records = $this->dbConnection
            ->createCommand()
            ->select('id')
            ->from('ophciexamination_refraction_type')
            ->where('priority IS NULL')
            ->order('display_order')
            ->queryAll();

        if (count($other_records) > 0) {
            $this->migrationEcho("Found unexpected refraction types, please verify the priority settings after migration\n");
            foreach ($other_records as $other_record) {
                $this->dbConnection
                    ->createCommand()
                    ->update('ophciexamination_refraction_type', ['priority' => ++$max], 'id = :id', [':id' => $other_record['id']]);
            }
        }
    }
}
