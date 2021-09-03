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

class m200918_121305_create_prism_fusion_range extends OEMigration
{

    protected const GROUP_NAME = 'Orthoptic Testing';
    protected const ELEMENT_CLS_NAME = 'OEModule\OphCiExamination\models\PrismFusionRange';

    public function up()
    {
        $this->createElementType(
            'OphCiExamination',
            'Prism Fusion Range',
            [
                'class_name' => self::ELEMENT_CLS_NAME,
                'display_order' => 403,
                'group_name' => self::GROUP_NAME
            ]
        );

        $this->createOETable(
            'et_ophciexamination_prismfusionrange',
            [
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
                'comments' => 'text'
            ],
            true
        );

        $this->addForeignKey(
            'et_ophciexamination_prismfusionrange_ev_fk',
            'et_ophciexamination_prismfusionrange',
            'event_id',
            'event',
            'id'
        );

        $this->createOETable(
            'ophciexamination_prismfusionrange_entry',
            [
                'id' => 'pk',
                'element_id' => 'int(11)',
                'prism_over_eye_id' => 'int(10) NOT NULL',
                'near_bo' => 'int',
                'near_bi' => 'int',
                'near_bu' => 'int',
                'near_bd' => 'int',
                'distance_bo' => 'int',
                'distance_bi' => 'int',
                'distance_bu' => 'int',
                'distance_bd' => 'int',
                'correctiontype_id' => 'int(11)',
                'with_head_posture' => 'boolean'
            ],
            true
        );

        $this->addForeignKey(
            'ophciexamination_prismfusionrange_entry_el_fk',
            'ophciexamination_prismfusionrange_entry',
            'element_id',
            'et_ophciexamination_prismfusionrange',
            'id'
        );

        $this->addForeignKey(
            'ophciexamination_prismfusionrange_entry_ct_fk',
            'ophciexamination_prismfusionrange_entry',
            'correctiontype_id',
            'ophciexamination_correctiontype',
            'id'
        );
        $examination_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->insert('index_search', [
            'event_type_id' => $examination_id,
            'primary_term' => 'Prism Fusion Range',
            'open_element_class_name' => self::ELEMENT_CLS_NAME,
        ]);
    }

    public function safeDown()
    {
        $this->delete('index_search', 'open_element_class_name = ? ', [self::ELEMENT_CLS_NAME]);

        $this->dropOETable('ophciexamination_prismfusionrange_entry', true);
        $this->dropOETable('et_ophciexamination_prismfusionrange', true);
        $this->deleteElementType('OphCiExamination', self::ELEMENT_CLS_NAME);
    }
}
