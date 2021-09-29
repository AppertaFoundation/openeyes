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

use OEModule\OphCiExamination\models\Element_OphCiExamination_NearVisualAcuity;
use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;

class m200311_134907_complex_attributes_for_va extends OEMigration
{
    protected $va_tables = [
        ['ophciexamination_visualacuity_reading', 'et_ophciexamination_visualacuity'],
        ['ophciexamination_nearvisualacuity_reading', 'et_ophciexamination_nearvisualacuity']
    ];


    public function up()
    {
        $this->createOETable('ophciexamination_visual_acuity_fixation', [
            'id' => 'pk',
            'name' => 'varchar(31)',
            'display_order' => 'tinyint default 1 not null',
            'active' => 'boolean default true',
        ], true);

        $this->createOETable('ophciexamination_visual_acuity_occluder', [
            'id' => 'pk',
            'name' => 'varchar(31)',
            'display_order' => 'tinyint default 1 not null',
            'active' => 'boolean default true',
        ], true);

        $this->createOETable('ophciexamination_visual_acuity_source', [
            'id' => 'pk',
            'name' => 'varchar(31)',
            'display_order' => 'tinyint default 1 not null',
            'active' => 'boolean default true',
            'is_near' => 'boolean default false'
        ], true);

        $this->addOEColumn(
            'ophciexamination_visual_acuity_unit',
            'complex_only',
            'boolean default false',
            true);

        $this->addOEColumn(
            'ophciexamination_visualacuity_method',
            'active',
            'boolean default true',
            true);

        $this->initialiseData(dirname(__FILE__));

        foreach ($this->va_tables as $tables) {
            $reading_table = $tables[0];
            $element_table = $tables[1];

            if (strpos($reading_table, 'near') === false) {
                $this->addOEColumn($reading_table, 'fixation_id', 'int(11)', true);
                $this->addForeignKey(
                    "{$reading_table}_fix_fk",
                    $reading_table,
                    'fixation_id',
                    'ophciexamination_visual_acuity_fixation',
                    'id'
                );
            }

            $this->addOEColumn($reading_table, 'occluder_id', 'int(11)', true);
            $this->addForeignKey(
                "{$reading_table}_occ_fk",
                $reading_table,
                'occluder_id',
                'ophciexamination_visual_acuity_occluder',
                'id'
            );

            $this->addOEColumn($reading_table, 'source_id', 'int(11)', true);
            $this->addForeignKey(
                "{$reading_table}_src_fk",
                $reading_table,
                'source_id',
                'ophciexamination_visual_acuity_source',
                'id'
            );

            $this->addOEColumn($reading_table, 'with_head_posture', 'boolean', true);
            $this->addOEColumn($element_table, 'beo_notes', 'text', true);
            $this->addOEColumn($element_table, 'beo_unable_to_assess', 'boolean', true);
        }

        // beo breaks this link
        $this->dropForeignKey('et_ophciexamination_visualacuity_eye_id_fk', 'et_ophciexamination_visualacuity');
        $this->migrationEcho("**NOTICE** eye foreign key removed for Visual Acuity. This will not be restored by reverting the migration.\n");
        $this->dropForeignKey('et_ophciexamination_nearvisualacuity_eye_id_fk', 'et_ophciexamination_nearvisualacuity');
        $this->migrationEcho("**NOTICE** eye foreign key removed for Near Visual Acuity. This will not be restored by reverting the migration.\n");

        $this->createRecordModeSettingForVA();

        $this->removeOESettingForElementType('default_rows', Element_OphCiExamination_VisualAcuity::class);
        $this->migrationEcho("**NOTICE** default rows setting removed for Visual Acuity. This will not be restored by reverting the migration.\n");
    }

    public function down()
    {
        $this->removeOESettingForElementType('record_mode', Element_OphCiExamination_VisualAcuity::class);
        $this->removeOESettingForElementType('record_mode', Element_OphCiExamination_NearVisualAcuity::class);

        foreach ($this->va_tables as $tables) {
            $reading_table = $tables[0];
            $element_table = $tables[1];

            $this->dropOEColumn($element_table, 'beo_unable_to_assess', true);
            $this->dropOEColumn($element_table, 'beo_notes', true);
            $this->dropOEColumn($reading_table, 'with_head_posture', true);
            $this->dropForeignKey("{$reading_table}_src_fk", $reading_table);
            $this->dropOEColumn($reading_table, 'source_id', true);
            $this->dropForeignKey("{$reading_table}_occ_fk", $reading_table);
            $this->dropOEColumn($reading_table, 'occluder_id', true);
            if (strpos($reading_table, 'near') === false) {
                $this->dropForeignKey("{$reading_table}_fix_fk", $reading_table);
                $this->dropOEColumn($reading_table, 'fixation_id', true);
            }
        }

        $this->removeCreatedUnits();

        $this->dropOEColumn('ophciexamination_visualacuity_method', 'active', true);
        $this->dropOEColumn('ophciexamination_visual_acuity_unit', 'complex_only', true);
        $this->dropOETable('ophciexamination_visual_acuity_source', true);
        $this->dropOETable('ophciexamination_visual_acuity_occluder', true);
        $this->dropOETable('ophciexamination_visual_acuity_fixation', true);
    }

    protected function removeCreatedUnits()
    {
        $data_path = $this->getDataDirectory(dirname(__FILE__));
        $units_file = glob($data_path . '*ophciexamination_visual_acuity_unit.csv')[0];
        $fh = fopen($units_file, 'r');
        $columns = fgetcsv($fh);
        $name_col = array_search('name', $columns);
        if ($name_col === false) {
            throw new Exception('cannot resolve name column in data file');
        }

        while (($row = fgetcsv($fh)) !== false) {
            $this->removeUnit($row[$name_col]);
        }
        fclose($fh);
    }

    protected function removeUnit($unit_name)
    {
        $id = $this->getDbConnection()->createCommand()
            ->select('id')
            ->where('name = :name', [':name' => $unit_name])
            ->from('ophciexamination_visual_acuity_unit')
            ->queryScalar();

        $this->delete('ophciexamination_visual_acuity_unit_value', 'unit_id = :unit_id', [':unit_id' => $id]);
        $this->delete('ophciexamination_visual_acuity_unit', 'id = :id', [':id' => $id]);
    }

    protected function createRecordModeSettingForVA()
    {
        foreach ([Element_OphCiExamination_VisualAcuity::class, Element_OphCiExamination_NearVisualAcuity::class] as $va_cls) {
            // setting for default record mode
            $element_type_id = $this->getIdOfElementTypeByClassName($va_cls);
            $this->insert('setting_metadata', [
                'element_type_id' => $element_type_id,
                'field_type_id' => $this->dbConnection->createCommand('SELECT id FROM setting_field_type WHERE name = "Dropdown list"')
                    ->queryScalar(),
                'key' => 'record_mode',
                'data' => serialize(array(
                    'simple' => 'Standard VA without BEO',
                    'complex' => 'Extended VA with BEO',
                )),
                'name' => 'Default Visual Acuity Record Mode',
                'default_value' => 'simple',
            ]);

            foreach (['Strabismus', 'Paediatrics'] as $subspecialty_name) {
                $subspecialty_id = $this->getIdOfSubspecialtyByName($subspecialty_name);
                $this->insert('setting_subspecialty', [
                    'element_type_id' => $element_type_id,
                    'subspecialty_id' => $subspecialty_id,
                    'key' => 'record_mode',
                    'value' => 'complex',
                ]);
            }
        }
    }
}
