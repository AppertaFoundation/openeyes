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

class m200206_163412_create_stereo_acuity extends OEMigration
{
    protected const GROUP_NAME = 'Orthoptic Testing';
    protected const ELEMENT_CLS_NAME = 'OEModule\OphCiExamination\models\StereoAcuity';
    public function safeUp()
    {
        $this->createElementType('OphCiExamination', 'Stereoacuity', [
            'class_name' => self::ELEMENT_CLS_NAME,
            'display_order' => 404,
            'group_name' => self::GROUP_NAME
        ]);

        $this->createOETable(
            'et_ophciexamination_stereoacuity',
            [
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
            ], true);

        $this->addForeignKey('et_ophciexamination_stereoacuity_ev_fk',
            'et_ophciexamination_stereoacuity',
            'event_id',
            'event',
            'id');

        $this->createOETable(
            'ophciexamination_stereoacuity_method',
            [
                'id' => 'pk',
                'name' => 'varchar(31)',
                'display_order' => 'tinyint default 1 not null',
                'active' => 'boolean default true',
            ], true);

        $this->initialiseData(dirname(__FILE__));

        $this->createOETable(
            'ophciexamination_stereoacuity_entry',
            [
                'id' => 'pk',
                'element_id' => 'int(11)',
                'method_id' => 'int(11) not null',
                'result' => 'varchar(31)',
                'inconclusive' => 'boolean',
                'correctiontype_id' => 'int(11)',
                'with_head_posture' => 'boolean'
            ], true);

        $this->addForeignKey('et_ophciexamination_stereoacuity_entry_el_fk',
            'ophciexamination_stereoacuity_entry',
            'element_id',
            'et_ophciexamination_stereoacuity',
            'id');

        $this->addForeignKey('et_ophciexamination_stereoacuity_entry_mt_fk',
            'ophciexamination_stereoacuity_entry',
            'method_id',
            'ophciexamination_stereoacuity_method',
            'id');

        $this->addForeignKey('et_ophciexamination_stereoacuity_entry_ct_fk',
            'ophciexamination_stereoacuity_entry',
            'correctiontype_id',
            'ophciexamination_correctiontype',
            'id');
        $examination_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->insert('index_search', [
            'event_type_id' => $examination_id,
            'primary_term' => 'Stereo Acuity',
            'open_element_class_name' => self::ELEMENT_CLS_NAME,
        ]);
    }

    public function safeDown()
    {
        $this->delete('index_search', 'open_element_class_name = ? ', [self::ELEMENT_CLS_NAME]);

        $this->dropOETable('ophciexamination_stereoacuity_entry', true);

        $this->dropOETable('ophciexamination_stereoacuity_method', true);

        $this->dropOETable('et_ophciexamination_stereoacuity', true);
        $this->deleteElementType('OphCiExamination', self::ELEMENT_CLS_NAME);
    }
}