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

class m200914_170003_create_correction_given extends OEMigration
{
    protected const GROUP_NAME = 'Visual Function';
    protected const ELEMENT_CLS_NAME = 'OEModule\OphCiExamination\models\CorrectionGiven';

    public function up()
    {
        $this->createElementType(
            'OphCiExamination',
            'Correction Given',
            [
                'class_name' => self::ELEMENT_CLS_NAME,
                'display_order' => 187, // after retinoscopy
                'group_name' => self::GROUP_NAME
            ]
        );

        $this->createOETable(
            'et_ophciexamination_correction_given',
            [
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
                'eye_id' => 'int(10) unsigned NOT NULL default 3',
                'right_as_found' => 'boolean',
                'right_as_found_element_type_id' => 'int(10) unsigned',
                'right_refraction' => 'varchar(127)',
                'left_as_found' => 'boolean',
                'left_as_found_element_type_id' => 'int(10) unsigned',
                'left_refraction' => 'varchar(127)'
            ],
            true
        );

        $this->addForeignKey(
            'et_ophciexamination_correction_given_ev_fk',
            'et_ophciexamination_correction_given',
            'event_id',
            'event',
            'id'
        );

        $this->addForeignKey('et_ophciexamination_rafet_eid_fk',
            'et_ophciexamination_correction_given',
            'right_as_found_element_type_id',
            'element_type',
            'id'
        );

        $this->addForeignKey('et_ophciexamination_lafet_eid_fk',
            'et_ophciexamination_correction_given',
            'left_as_found_element_type_id',
            'element_type',
            'id'
        );
        $examination_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->insert('index_search', [
            'event_type_id' => $examination_id,
            'primary_term' => 'Correction Given',
            'open_element_class_name' => self::ELEMENT_CLS_NAME,
        ]);
    }

    public function down()
    {
        $this->delete('index_search', 'open_element_class_name = ? ', [self::ELEMENT_CLS_NAME]);

        $this->dropOETable('et_ophciexamination_correction_given', true);
        $this->deleteElementType('OphCiExamination', self::ELEMENT_CLS_NAME);
    }
}
