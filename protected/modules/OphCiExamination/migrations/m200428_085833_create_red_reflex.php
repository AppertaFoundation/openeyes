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

class m200428_085833_create_red_reflex extends OEMigration
{
    protected const GROUP_NAME = 'Orthoptic Testing';
    protected const ELEMENT_CLS_NAME = 'OEModule\OphCiExamination\models\RedReflex';

    public function up()
    {
        $this->createElementType('OphCiExamination', 'Red Reflex', [
            'class_name' => self::ELEMENT_CLS_NAME,
            'display_order' => 407,
            'group_name' => self::GROUP_NAME
        ]);

        $this->createOETable(
            'et_ophciexamination_red_reflex',
            [
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
                'eye_id' => 'int(10) unsigned NOT NULL DEFAULT 3',
                'right_has_red_reflex' => 'boolean',
                'left_has_red_reflex' => 'boolean',
            ],
            true
        );

        $this->addForeignKey(
            'et_ophciexamination_red_reflex_ev_fk',
            'et_ophciexamination_red_reflex',
            'event_id',
            'event',
            'id'
        );

        $examination_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->insert('index_search', [
            'event_type_id' => $examination_id,
            'primary_term' => 'Red Reflex',
            'open_element_class_name' => self::ELEMENT_CLS_NAME,
        ]);
    }

    public function down()
    {
        $this->delete('index_search', 'open_element_class_name = ? ', [self::ELEMENT_CLS_NAME]);

        $this->dropOETable('et_ophciexamination_red_reflex', true);

        $this->deleteElementType('OphCiExamination', self::ELEMENT_CLS_NAME);
    }
}
