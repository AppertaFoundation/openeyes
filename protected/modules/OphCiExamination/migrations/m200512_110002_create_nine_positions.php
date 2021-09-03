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

class m200512_110002_create_nine_positions extends OEMigration
{
    protected $group_name = 'Orthoptic Testing';
    protected const ELEMENT_CLS_NAME = 'OEModule\OphCiExamination\models\NinePositions';
    // see the widget constants defined for these fields
    protected static $settings_options = [
        'enable_dvd' => 'Enable DVD',
        'enable_head_posture' => 'Enable with head posture',
        'enable_correction' => 'Enable with correction',
        'enable_wong_supine_positive' => 'Enable Wong supine positive',
        'enable_hess_chart' => 'Enable Hess chart'
    ];

    public function up()
    {
        $this->createElementType('OphCiExamination', 'Nine Positions', [
            'class_name' => self::ELEMENT_CLS_NAME,
            'display_order' => 399,
            'group_name' => $this->group_name
        ]);

        $this->createOETable('et_ophciexamination_ninepositions', [
            'id' => 'pk',
            'event_id' => 'int(10) unsigned',
        ], true);

        $this->addForeignKey(
            'et_ophciexamination_ninepositions_ev_fk',
            'et_ophciexamination_ninepositions',
            'event_id',
            'event',
            'id'
        );

        $this->createOETable(
            'ophciexamination_ninepositions_horizontal_x_deviation',
            [
                'id' => 'pk',
                'name' => 'varchar(31) not null',
                'abbreviation' => 'varchar(7) not null',
                'display_order' => 'tinyint default 1 not null',
                'active' => 'boolean default true',
            ],
            true
        );

        $this->createOETable(
            'ophciexamination_ninepositions_horizontal_e_deviation',
            [
                'id' => 'pk',
                'name' => 'varchar(31) not null',
                'abbreviation' => 'varchar(7) not null',
                'display_order' => 'tinyint default 1 not null',
                'active' => 'boolean default true',
            ],
            true
        );

        $this->createOETable(
            'ophciexamination_ninepositions_vertical_deviation',
            [
                'id' => 'pk',
                'name' => 'varchar(31) not null',
                'abbreviation' => 'varchar(7) not null',
                'display_order' => 'tinyint default 1 not null',
                'active' => 'boolean default true',
            ],
            true
        );

        $this->createOETable(
            'ophciexamination_ninepositions_reading',
            [
                'id' => 'pk',
                'element_id' => 'int(11) not null',
                'with_correction' => 'boolean',
                'with_head_posture' => 'boolean',
                'wong_supine_positive' => 'boolean',
                'hess_chart' => 'boolean',
                'right_dvd' => 'int',
                'left_dvd' => 'int',
                'right_eyedraw' => 'text',
                'left_eyedraw' => 'text',
                'full_ocular_movement' => 'boolean',
                'comments' => 'text'
            ],
            true
        );

        $this->addForeignKey(
            'ophciexamination_ninepositions_reading_el_fk',
            'ophciexamination_ninepositions_reading',
            'element_id',
            'et_ophciexamination_ninepositions',
            'id'
        );

        $this->createOETable('ophciexamination_ninepositions_movement',
            [
                'id' => 'pk',
                'name' => 'varchar(7) not null',
                'display_order' => 'tinyint default 1 not null',
                'active' => 'boolean default true',
            ],
        );

        $this->initialiseData(dirname(__FILE__));

        $this->createOETable(
            'ophciexamination_ninepositions_alignmentforgaze',
            [
                'id' => 'pk',
                'reading_id' => 'int(11) not null',
                'gaze_type' => 'varchar(31) not null',
                'horizontal_angle' => 'int',
                'horizontal_e_deviation_id' => 'int(11)',
                'horizontal_x_deviation_id' => 'int(11)',
                'horizontal_prism_position' => 'varchar(2)',
                'vertical_angle' => 'int',
                'vertical_deviation_id' => 'int(11)',
                'vertical_prism_position' => 'varchar(4)'
            ],
            true
        );

        $this->addForeignKey(
            'ophciexamination_ninepositions_alignmentforgaze_rd_fk',
            'ophciexamination_ninepositions_alignmentforgaze',
            'reading_id',
            'ophciexamination_ninepositions_reading',
            'id'
        );

        $this->addForeignKey(
            'ophciexamination_ninepositions_alignmentforgaze_hed_fk',
            'ophciexamination_ninepositions_alignmentforgaze',
            'horizontal_e_deviation_id',
            'ophciexamination_ninepositions_horizontal_e_deviation',
            'id'
        );

        $this->addForeignKey(
            'ophciexamination_ninepositions_alignmentforgaze_hxd_fk',
            'ophciexamination_ninepositions_alignmentforgaze',
            'horizontal_x_deviation_id',
            'ophciexamination_ninepositions_horizontal_x_deviation',
            'id'
        );

        $this->addForeignKey(
            'ophciexamination_ninepositions_alignmentforgaze_vd_fk',
            'ophciexamination_ninepositions_alignmentforgaze',
            'vertical_deviation_id',
            'ophciexamination_ninepositions_vertical_deviation',
            'id'
        );

        $this->createOETable(
            'ophciexamination_ninepositions_movementforgaze',
            [
                'id' => 'pk',
                'reading_id' => 'int(11) not null',
                'gaze_type' => 'varchar(31) not null',
                'movement_id' => 'int(11) not null',
                'eye_id' => 'int(10) unsigned NOT NULL'
            ],
            true
        );

        $this->addForeignKey(
            'ophciexamination_ninepositions_movementforgaze_rd_fk',
            'ophciexamination_ninepositions_movementforgaze',
            'reading_id',
            'ophciexamination_ninepositions_reading',
            'id'
        );

        $this->addForeignKey(
            'ophciexamination_ninepositions_movementforgaze_mv_fk',
            'ophciexamination_ninepositions_movementforgaze',
            'movement_id',
            'ophciexamination_ninepositions_movement',
            'id'
        );

        $this->createSettings();

        $examination_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->insert('index_search', [
            'event_type_id' => $examination_id,
            'primary_term' => 'Nine Positions of Gaze',
            'open_element_class_name' => self::ELEMENT_CLS_NAME,
        ]);
    }

    public function down()
    {
        $this->delete('index_search', 'open_element_class_name = ? ', [self::ELEMENT_CLS_NAME]);

        $this->removeSettings();

        $this->dropOETable('ophciexamination_ninepositions_movementforgaze', true);

        $this->dropOETable('ophciexamination_ninepositions_alignmentforgaze', true);

        $this->dropOETable('ophciexamination_ninepositions_reading', true);

        $this->dropOETable('ophciexamination_ninepositions_movement', true);

        $this->dropOETable('ophciexamination_ninepositions_vertical_deviation', true);

        $this->dropOETable('ophciexamination_ninepositions_horizontal_e_deviation', true);

        $this->dropOETable('ophciexamination_ninepositions_horizontal_x_deviation', true);

        $this->dropOETable('et_ophciexamination_ninepositions', true);

        $this->deleteElementType('OphCiExamination', self::ELEMENT_CLS_NAME);

        $this->deleteElementGroupForEventType($this->group_name, 'OphCiExamination');
    }

    protected function createSettings()
    {
        $element_type_id = $this->getIdOfElementTypeByClassName(self::ELEMENT_CLS_NAME);
        $checkbox_field_type_id = $this
            ->dbConnection
            ->createCommand('SELECT id FROM setting_field_type WHERE name = "Checkbox"')
            ->queryScalar();

        foreach (static::$settings_options as $key => $name) {
            $this->insert('setting_metadata', [
                'element_type_id' => $element_type_id,
                'field_type_id' => $checkbox_field_type_id,
                'key' => $key,
                'name' => $name,
                'default_value' => '1',
            ]);
        }
    }

    protected function removeSettings()
    {
        $element_type_id = $this->getIdOfElementTypeByClassName(self::ELEMENT_CLS_NAME);
        $this->delete('setting_metadata', 'element_type_id = ?', [$element_type_id]);
    }

}
