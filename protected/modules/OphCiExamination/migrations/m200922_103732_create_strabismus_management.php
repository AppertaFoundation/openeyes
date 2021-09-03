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
 * Class m200922_103732_create_strabismus_management
 *
 * N.B. the looks up are just text lookups, and no link is maintained when selecting
 * as such, no active flag is required, and no versioning of values is necessary.
 */
class m200922_103732_create_strabismus_management extends OEMigration
{
    protected const GROUP_NAME = 'Clinical Management';
    protected const ELEMENT_CLS_NAME = 'OEModule\OphCiExamination\models\StrabismusManagement';

    public function safeUp()
    {
        $this->createElementType(
            'OphCiExamination',
            'Strabismus Management',
            [
                'class_name' => self::ELEMENT_CLS_NAME,
                'display_order' => 495, // bottom of list at this point
                'group_name' => self::GROUP_NAME
            ]
        );

        $this->createOETable(
            'ophciexamination_strabismusmanagement_treatment',
            [
                'id' => 'pk',
                'name' => 'varchar(63) NOT NULL',
                'display_order' => 'tinyint DEFAULT 1 NOT NULL',
                'reason_required' => 'boolean DEFAULT 0 NOT NULL',
                'column1_multiselect' => 'boolean DEFAULT 0 NOT NULL',
                'column2_multiselect' => 'boolean DEFAULT 0 NOT NULL',
            ],
            false
        );

        $this->createOETable(
            'ophciexamination_strabismusmanagement_treatmentoption',
            [
                'id' => 'pk',
                'name' => 'varchar(63) NOT NULL',
                'treatment_id' => 'int(11)',
                'column_number' => 'tinyint NOT NULL DEFAULT 1',
                'display_order' => 'tinyint DEFAULT 1 NOT NULL',
            ],
            false
        );

        $this->createOETable(
            'ophciexamination_strabismusmanagement_treatmentreason',
            [
                'id' => 'pk',
                'name' => 'varchar(63) NOT NULL',
                'display_order' => 'tinyint DEFAULT 1 NOT NULL'
            ],
            false
        );

        $this->initialiseData(dirname(__FILE__));

        $this->createOETable(
            'et_ophciexamination_strabismusmanagement',
            [
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
                'comments' => 'text'
            ],
            true
        );

        $this->addForeignKey(
            'et_ophciexamination_strabismusmanagement_ev_fk',
            'et_ophciexamination_strabismusmanagement',
            'event_id',
            'event',
            'id'
        );

        $this->createOETable(
            'ophciexamination_strabismusmanagement_entry',
            [
                'id' => 'pk',
                'element_id' => 'int(11)',
                'eye_id' => 'int(10) unsigned NOT NULL default 3',
                'treatment' => 'varchar(63) not null',
                'treatment_options' => 'varchar(255)',
                'treatment_reason' => 'varchar(63)'
            ],
            true
        );

        $this->addForeignKey(
            'ophciexamination_strabismusmanagement_entry_el_fk',
            'ophciexamination_strabismusmanagement_entry',
            'element_id',
            'et_ophciexamination_strabismusmanagement',
            'id'
        );

        $examination_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->insert('index_search', [
            'event_type_id' => $examination_id,
            'primary_term' => 'Strabismus Management',
            'open_element_class_name' => self::ELEMENT_CLS_NAME,
        ]);

        return true;
    }

    public function safeDown()
    {
        $this->delete('index_search', 'open_element_class_name = ? ', [self::ELEMENT_CLS_NAME]);

        $this->dropOETable('ophciexamination_strabismusmanagement_entry', true);
        $this->dropOETable('et_ophciexamination_strabismusmanagement', true);
        $this->dropOETable('ophciexamination_strabismusmanagement_treatmentreason', false);
        $this->dropOETable('ophciexamination_strabismusmanagement_treatmentoption', false);
        $this->dropOETable('ophciexamination_strabismusmanagement_treatment', false);
        $this->deleteElementType('OphCiExamination', self::ELEMENT_CLS_NAME);

        return true;
    }
}
