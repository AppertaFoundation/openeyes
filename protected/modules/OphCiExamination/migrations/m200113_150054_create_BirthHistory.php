<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m200113_150054_create_BirthHistory extends OEMigration
{
    protected const GROUP_NAME = 'History';
    protected const ELEMENT_CLS_NAME = 'OEModule\OphCiExamination\models\BirthHistory';
    public function up()
    {
        $this->createElementType('OphCiExamination', 'Birth History', [
            'class_name' => self::ELEMENT_CLS_NAME,
            'display_order' => 123, // after Social History
            'group_name' => self::GROUP_NAME
        ]);

        $this->createOETable(
            'ophciexamination_birthhistory_deliverytype',
            [
                'id' => 'pk',
                'name' => 'varchar(63) not null',
                'display_order' => 'tinyint not null',
                'active' => 'boolean',
            ], true);

        $this->createOETable(
            'et_ophciexamination_birthhistory',
            [
                'id' => 'pk',
                'event_id' => 'int(10) unsigned not null',
                'weight_recorded_units' => 'varchar(2)',
                'weight_grams' => 'int',
                'weight_ozs' => 'int',
                'birth_history_delivery_type_id' => 'int(11)',
                'gestation_weeks' => 'tinyint',
                'had_neonatal_specialist_care' => 'tinyint',
                'was_multiple_birth' => 'tinyint',
                'comments' => 'text'
            ], true );

        $this->addForeignKey('et_ophciexamination_birthhistory_ev_fk',
            'et_ophciexamination_birthhistory',
            'event_id',
            'event',
            'id');

        $this->addForeignKey('et_ophciexamination_birthhistory_dt_fk',
            'et_ophciexamination_birthhistory',
            'birth_history_delivery_type_id',
            'ophciexamination_birthhistory_deliverytype',
            'id');

        $this->initialiseData(dirname(__FILE__));

        $examination_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->insert('index_search', [
            'event_type_id' => $examination_id,
            'primary_term' => 'Birth History',
            'open_element_class_name' => self::ELEMENT_CLS_NAME,
        ]);
    }

    public function down()
    {
        $this->delete('index_search', 'open_element_class_name = ? ', [self::ELEMENT_CLS_NAME]);
        $this->dropOETable('et_ophciexamination_birthhistory', true);
        $this->dropOETable('ophciexamination_birthhistory_deliverytype', true);

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
                [':class_name' => 'OEModule\OphCiExamination\models\BirthHistory', ':eid' => $event_type_id])
            ->queryScalar();
        $this->delete('ophciexamination_element_set_item', 'element_type_id = :element_type_id',
            [':element_type_id' => $element_type_id]);
        $this->delete('element_type', 'id = :id',
            [':id' => $element_type_id]);
    }
}
