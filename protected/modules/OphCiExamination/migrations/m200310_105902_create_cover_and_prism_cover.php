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

class m200310_105902_create_cover_and_prism_cover extends OEMigration
{
    protected const GROUP_NAME = 'Orthoptic Testing';
    protected const ELEMENT_CLS_NAME = 'OEModule\OphCiExamination\models\CoverAndPrismCover';
    public function safeUp()
    {
        $this->createElementType('OphCiExamination', 'Cover Test', [
            'class_name' => self::ELEMENT_CLS_NAME,
            'display_order' => 398,
            'group_name' => self::GROUP_NAME
        ]);

        $this->createOETable(
            'et_ophciexamination_coverandprismcover',
            [
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
                'comments' => 'text',
            ], true);

        $this->addForeignKey('et_ophciexamination_coverandprismcover_ev_fk',
            'et_ophciexamination_coverandprismcover',
            'event_id',
            'event',
            'id');

        $this->createOETable(
            'ophciexamination_coverandprismcover_horizontalprism',
            [
                'id' => 'pk',
                'name' => 'varchar(31)',
                'display_order' => 'tinyint default 1 not null',
                'active' => 'boolean default true',
            ], true);

        $this->createOETable(
            'ophciexamination_coverandprismcover_verticalprism',
            [
                'id' => 'pk',
                'name' => 'varchar(31)',
                'display_order' => 'tinyint default 1 not null',
                'active' => 'boolean default true',
            ], true);

        $this->createOETable(
            'ophciexamination_coverandprismcover_distance',
            [
                'id' => 'pk',
                'name' => 'varchar(31)',
                'display_order' => 'tinyint default 1 not null',
                'active' => 'boolean default true',
            ], true);

        $this->initialiseData(dirname(__FILE__));

        $this->createOETable(
            'ophciexamination_coverandprismcover_entry',
            [
                'id' => 'pk',
                'element_id' => 'int(11)',
                'correctiontype_id' => 'int(11)',
                'distance_id' => 'int(11)',
                'horizontal_prism_id' => 'int(11)',
                'horizontal_value' => 'int(11)',
                'vertical_prism_id' => 'int(11)',
                'vertical_value' => 'int(11)',
                'with_head_posture' => 'boolean',
                'comments' => 'text'
            ], true);

        $this->addForeignKey('et_ophciexamination_coverandprismcover_entry_el_fk',
            'ophciexamination_coverandprismcover_entry',
            'element_id',
            'et_ophciexamination_coverandprismcover',
            'id');

        $this->addForeignKey('et_ophciexamination_coverandprismcover_entry_ct_fk',
            'ophciexamination_coverandprismcover_entry',
            'correctiontype_id',
            'ophciexamination_correctiontype',
            'id');

        $this->addForeignKey('et_ophciexamination_coverandprismcover_entry_ht_fk',
            'ophciexamination_coverandprismcover_entry',
            'horizontal_prism_id',
            'ophciexamination_coverandprismcover_horizontalprism',
            'id');

        $this->addForeignKey('et_ophciexamination_coverandprismcover_entry_vt_fk',
            'ophciexamination_coverandprismcover_entry',
            'vertical_prism_id',
            'ophciexamination_coverandprismcover_verticalprism',
            'id');

        $this->addForeignKey('et_ophciexamination_coverandprismcover_entry_in_fk',
            'ophciexamination_coverandprismcover_entry',
            'distance_id',
            'ophciexamination_coverandprismcover_distance',
            'id');

        $examination_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->insert('index_search', [
            'event_type_id' => $examination_id,
            'primary_term' => 'Cover Test',
            'open_element_class_name' => self::ELEMENT_CLS_NAME,
        ]);
    }

    public function safeDown()
    {
        $this->delete('index_search', 'open_element_class_name = ? ', [self::ELEMENT_CLS_NAME]);

        $this->dropOETable('ophciexamination_coverandprismcover_entry', true);

        $this->dropOETable('ophciexamination_coverandprismcover_distance', true);

        $this->dropOETable('ophciexamination_coverandprismcover_verticalprism', true);

        $this->dropOETable('ophciexamination_coverandprismcover_horizontalprism', true);

        $this->dropOETable('et_ophciexamination_coverandprismcover', true);

        $this->deleteElementType('OphCiExamination', self::ELEMENT_CLS_NAME);
    }
}