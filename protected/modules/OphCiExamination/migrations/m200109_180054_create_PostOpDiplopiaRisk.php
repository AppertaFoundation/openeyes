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

class m200109_180054_create_PostOpDiplopiaRisk extends OEMigration
{
    protected const GROUP_NAME = 'Orthoptic Testing';
    protected const ELEMENT_CLS_NAME = 'OEModule\OphCiExamination\models\PostOpDiplopiaRisk';
    public function up()
    {
        // this should place the group after Investigation
        $this->createElementGroupForEventType(self::GROUP_NAME,'OphCiExamination', 115);

        $this->createElementType('OphCiExamination', 'Post-Op Diplopia Risk', [
            'class_name' => self::ELEMENT_CLS_NAME,
            'display_order' => 408,
            'group_name' => self::GROUP_NAME
        ]);

        $this->createOETable(
            'et_ophciexamination_postopdiplopiarisk',
            [
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
                'comments' => 'text',
                'at_risk' => 'boolean',
            ], true);

        $this->addForeignKey('et_ophciexamination_postopdiplopiarisk_ev_fk',
            'et_ophciexamination_postopdiplopiarisk',
            'event_id',
            'event',
            'id');

        $examination_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->insert('index_search', [
            'event_type_id' => $examination_id,
            'primary_term' => 'Post-Op Diplopia Risk',
            'secondary_term_list' => 'POPD',
            'open_element_class_name' => self::ELEMENT_CLS_NAME,
        ]);
    }

    public function down()
    {
        $this->delete('index_search', 'open_element_class_name = ? ', [self::ELEMENT_CLS_NAME]);
        $this->dropForeignKey('et_ophciexamination_postopdiplopiarisk_ev_fk',
            'et_ophciexamination_postopdiplopiarisk');

        $this->dropOETable('et_ophciexamination_postopdiplopiarisk', true);

        $this->deleteElementType('OphCiExamination', self::ELEMENT_CLS_NAME);
        $this->deleteElementGroupForEventType(self::GROUP_NAME, 'OphCiExamination');
    }
}
