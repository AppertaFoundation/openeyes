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

class m200908_135901_create_retinoscopy extends OEMigration
{
    protected const GROUP_NAME = 'Visual Function';
    protected const ELEMENT_CLS_NAME = 'OEModule\OphCiExamination\models\Retinoscopy';

    public function up()
    {
        $this->createElementType(
            'OphCiExamination',
            'Retinoscopy',
            [
                'class_name' => self::ELEMENT_CLS_NAME,
                'display_order' => 185, // after refraction
                'group_name' => self::GROUP_NAME
            ]
        );

        $this->createOETable(
            'ophciexamination_retinoscopy_working_distance',
            [
                'id' => 'pk',
                'name' => 'varchar(7) not null',
                'value' => 'decimal(5,3) not null',
                'display_order' => 'tinyint default 1 not null',
                'active' => 'boolean default true',
            ],
            true
        );

        $this->initialiseData(dirname(__FILE__));

        $this->createOETable(
            'et_ophciexamination_retinoscopy',
            [
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
                'eye_id' => 'int(10) unsigned NOT NULL default 3',
                'right_working_distance_id' => 'int(11)',
                'right_angle' => 'smallint',
                'right_power1' => 'decimal(6,2)',
                'right_power2' => 'decimal(6, 2)',
                'right_dilated' => 'boolean',
                'right_refraction' => 'varchar(127)',
                'right_eyedraw' => 'text',
                'right_comments' => 'text',
                'left_working_distance_id' => 'int(11)',
                'left_angle' => 'smallint',
                'left_power1' => 'decimal(6,2)',
                'left_power2' => 'decimal(6, 2)',
                'left_dilated' => 'boolean',
                'left_refraction' => 'varchar(127)',
                'left_eyedraw' => 'text',
                'left_comments' => 'text'
            ],
            true
        );

        $this->addForeignKey(
            'et_ophciexamination_retinoscopy_ev_fk',
            'et_ophciexamination_retinoscopy',
            'event_id',
            'event',
            'id'
        );

        $this->addForeignKey(
            'et_ophciexamination_retinoscopy_rwd_fk',
            'et_ophciexamination_retinoscopy',
            'right_working_distance_id',
            'ophciexamination_retinoscopy_working_distance',
            'id'
        );

        $this->addForeignKey(
            'et_ophciexamination_retinoscopy_lwd_fk',
            'et_ophciexamination_retinoscopy',
            'left_working_distance_id',
            'ophciexamination_retinoscopy_working_distance',
            'id'
        );
        $examination_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->insert('index_search', [
            'event_type_id' => $examination_id,
            'primary_term' => 'Retinoscopy',
            'open_element_class_name' => self::ELEMENT_CLS_NAME,
        ]);
    }

    public function down()
    {
        $this->delete('index_search', 'open_element_class_name = ? ', [self::ELEMENT_CLS_NAME]);

        $this->dropOETable('et_ophciexamination_retinoscopy', true);
        $this->dropOETable('ophciexamination_retinoscopy_working_distance', true);
        $this->deleteElementType('OphCiExamination', self::ELEMENT_CLS_NAME);
    }
}
