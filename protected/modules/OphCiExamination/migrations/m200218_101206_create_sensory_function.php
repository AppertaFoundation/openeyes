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

class m200218_101206_create_sensory_function extends OEMigration
{
    protected const GROUP_NAME = 'Orthoptic Testing';
    protected const ELEMENT_CLS_NAME = 'OEModule\OphCiExamination\models\SensoryFunction';

    public function safeUp()
    {
        $this->createElementType('OphCiExamination', 'Sensory Function', [
            'class_name' => self::ELEMENT_CLS_NAME,
            'display_order' => 401,
            'group_name' => self::GROUP_NAME
        ]);

        $this->createOETable(
            'et_ophciexamination_sensoryfunction',
            [
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
            ], true);

        $this->addForeignKey('et_ophciexamination_sensoryfunction_ev_fk',
            'et_ophciexamination_sensoryfunction',
            'event_id',
            'event',
            'id');

        $this->createOETable(
            'ophciexamination_sensoryfunction_entrytype',
            [
                'id' => 'pk',
                'name' => 'varchar(31)',
                'display_order' => 'tinyint default 1 not null',
                'active' => 'boolean default true',
            ], true);

        $this->createOETable(
            'ophciexamination_sensoryfunction_distance',
            [
                'id' => 'pk',
                'name' => 'varchar(31)',
                'display_order' => 'tinyint default 1 not null',
                'active' => 'boolean default true',
            ], true);

        $this->createOETable(
            'ophciexamination_sensoryfunction_result',
            [
                'id' => 'pk',
                'name' => 'varchar(31)',
                'display_order' => 'tinyint default 1 not null',
                'active' => 'boolean default true',
            ], true);

        $this->initialiseData(dirname(__FILE__));

        $this->createOETable(
            'ophciexamination_sensoryfunction_entry',
            [
                'id' => 'pk',
                'element_id' => 'int(11)',
                'entry_type_id' => 'int(11) not null',
                'distance_id' => 'int(11) not null',
                'result_id' => 'int(11) not null',
                'with_head_posture' => 'boolean'
            ], true);

        $this->addForeignKey('et_ophciexamination_sensoryfunction_entry_el_fk',
            'ophciexamination_sensoryfunction_entry',
            'element_id',
            'et_ophciexamination_sensoryfunction',
            'id');

        $this->addForeignKey('et_ophciexamination_sensoryfunction_entry_type_fk',
            'ophciexamination_sensoryfunction_entry',
            'entry_type_id',
            'ophciexamination_sensoryfunction_entrytype',
            'id');

        $this->addForeignKey('et_ophciexamination_sensoryfunction_entry_dist_fk',
            'ophciexamination_sensoryfunction_entry',
            'distance_id',
            'ophciexamination_sensoryfunction_distance',
            'id');

        $this->addForeignKey('et_ophciexamination_sensoryfunction_entry_result_fk',
            'ophciexamination_sensoryfunction_entry',
            'result_id',
            'ophciexamination_sensoryfunction_result',
            'id');

        $this->createOETable('ophciexamination_sensoryfunction_correction_assgnmnt', [
            'entry_id' => 'int(11)',
            'correctiontype_id' => 'int(11)'
        ], true);

        $this->addForeignKey('ophciexamination_sensoryfunction_correctiontype_entry_fk',
            'ophciexamination_sensoryfunction_correction_assgnmnt',
            'entry_id',
            'ophciexamination_sensoryfunction_entry',
            'id');

        $this->addForeignKey('ophciexamination_sensoryfunction_correctiontype_correction_fk',
            'ophciexamination_sensoryfunction_correction_assgnmnt',
            'correctiontype_id',
            'ophciexamination_correctiontype',
            'id');

        $examination_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->insert('index_search', [
            'event_type_id' => $examination_id,
            'primary_term' => 'Sensory Function',
            'open_element_class_name' => self::ELEMENT_CLS_NAME,
        ]);
    }

    public function safeDown()
    {
        $this->delete('index_search', 'open_element_class_name = ? ', [self::ELEMENT_CLS_NAME]);

        $this->dropOETable('ophciexamination_sensoryfunction_correction_assgnmnt', true);
        $this->dropOETable('ophciexamination_sensoryfunction_entry', true);

        $this->dropOETable('ophciexamination_sensoryfunction_result', true);
        $this->dropOETable('ophciexamination_sensoryfunction_distance', true);
        $this->dropOETable('ophciexamination_sensoryfunction_entrytype', true);

        $this->dropOETable('et_ophciexamination_sensoryfunction', true);
        $this->deleteElementType('OphCiExamination', self::ELEMENT_CLS_NAME);
    }
}