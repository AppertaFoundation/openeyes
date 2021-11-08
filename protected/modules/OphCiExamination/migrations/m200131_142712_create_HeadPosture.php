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

class m200131_142712_create_HeadPosture extends OEMigration
{
    protected const GROUP_NAME = 'Orthoptic Testing';
    protected const ELEMENT_CLS_NAME = 'OEModule\OphCiExamination\models\HeadPosture';

    public function safeUp()
    {
        $this->createElementType('OphCiExamination', 'Corrective Head Posture', [
            'class_name' => self::ELEMENT_CLS_NAME,
            'display_order' => 397,
            'group_name' => self::GROUP_NAME
        ]);

        $this->createOETable(
            'et_ophciexamination_headposture',
            [
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
                'tilt' => 'varchar(7)',
                'turn' => 'varchar(7)',
                'chin' => 'varchar(15)',
                'comments' => 'text',
            ], true);

        $this->addForeignKey('et_ophciexamination_headposture_ev_fk',
            'et_ophciexamination_headposture',
            'event_id',
            'event',
            'id');

        $examination_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->insert('index_search', [
            'event_type_id' => $examination_id,
            'primary_term' => 'Head Posture',
            'open_element_class_name' => self::ELEMENT_CLS_NAME,
        ]);
    }

    public function safeDown()
    {
        $this->delete('index_search', 'open_element_class_name = ? ', [self::ELEMENT_CLS_NAME]);

        $this->dropForeignKey('et_ophciexamination_headposture_ev_fk',
            'et_ophciexamination_headposture');

        $this->dropOETable('et_ophciexamination_headposture', true);

        $event_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name = :class_name',
                [':class_name' => 'OphCiExamination'])
            ->queryScalar();
        $element_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where('class_name = :class_name AND event_type_id = :eid',
                [':class_name' => self::ELEMENT_CLS_NAME, ':eid' => $event_type_id])
            ->queryScalar();
        $this->delete('ophciexamination_element_set_item', 'element_type_id = :element_type_id',
            [':element_type_id' => $element_type_id]);
        $this->delete('element_type', 'id = :id',
            [':id' => $element_type_id]);

        $this->deleteElementGroupForEventType(self::GROUP_NAME, 'OphCiExamination');
    }
}
