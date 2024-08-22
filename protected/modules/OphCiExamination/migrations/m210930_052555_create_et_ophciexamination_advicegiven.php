<?php

use OEModule\OphCiExamination\models\AdviceGiven;

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
class m210930_052555_create_et_ophciexamination_advicegiven extends OEMigration
{
    /**
     * @return void
     * @throws CException
     */
    public function safeUp()
    {
        $exam_event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')
            ->where('name=:name', array(':name' => 'Examination'))->queryRow();

        $exam_event_type_id = $exam_event_type['id'];

        $this->createOETable(
            'et_ophciexamination_advicegiven',
            array(
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
                'comments' => 'text',
            ),
            true
        );

        $this->addForeignKey(
            'et_ophciexamination_advicegiven_event_fk',
            'et_ophciexamination_advicegiven',
            'event_id',
            'event',
            'id'
        );

        $this->createElementType(
            'OphCiExamination',
            'Advice Given',
            ['class_name' => AdviceGiven::class, 'group_name' => 'Follow-up', 'display_order' => 505]
        );

        $this->createOETable(
            'ophciexamination_advice_leaflet',
            array(
                'id' => 'pk',
                'name' => 'varchar(1024) NOT NULL',
                'institution_id' => 'int(10) unsigned NOT NULL',
                'active' => 'tinyint(1) DEFAULT 1',
            ),
            true
        );

        $this->addForeignKey(
            'ophciexamination_advice_leaflet_i_fk',
            'ophciexamination_advice_leaflet',
            'institution_id',
            'institution',
            'id'
        );

        $this->createOETable(
            'ophciexamination_advice_leaflet_category',
            array(
                'id' => 'pk',
                'name' => 'varchar(255) NOT NULL',
                'institution_id' => 'int(10) unsigned NOT NULL',
                'active' => 'tinyint(1) DEFAULT 1',
            ),
            true
        );

        $this->addForeignKey(
            'ophciexamination_advice_leaflet_category_i_fk',
            'ophciexamination_advice_leaflet_category',
            'institution_id',
            'institution',
            'id'
        );

        $this->createOETable(
            'ophciexamination_advice_leaflet_category_assignment',
            array(
                'id' => 'pk',
                'category_id' => 'int',
                'leaflet_id' => 'int',
                'display_order' => 'int(10) unsigned DEFAULT 1',
            ),
            true
        );

        $this->addForeignKey(
            'ophciexamination_advice_leaflet_category_assignment_c_fk',
            'ophciexamination_advice_leaflet_category_assignment',
            'category_id',
            'ophciexamination_advice_leaflet_category',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_adviceleaflet_category_assignment_l_fk',
            'ophciexamination_advice_leaflet_category_assignment',
            'leaflet_id',
            'ophciexamination_advice_leaflet',
            'id'
        );

        $this->createOETable(
            'ophciexamination_advice_leaflet_entry',
            array(
                'id' => 'pk',
                'element_id' => 'int NOT NULL',
                'leaflet_id' => 'int NOT NULL',
                'display_order' => 'int(10) unsigned DEFAULT 1',
            ),
            true
        );
        $this->addForeignKey(
            'ophciexamination_advice_leaflet_entry_element_fk',
            'ophciexamination_advice_leaflet_entry',
            'element_id',
            'et_ophciexamination_advicegiven',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_advice_leaflet_entry_l_fk',
            'ophciexamination_advice_leaflet_entry',
            'leaflet_id',
            'ophciexamination_advice_leaflet',
            'id'
        );

        parent::safeUp();

        $this->createOETable(
            'ophciexamination_advice_leaflet_category_subspecialty',
            array(
                'id' => 'pk',
                'category_id' => 'int',
                'subspecialty_id' => 'int(10) unsigned',
                'display_order' => 'int(10) unsigned DEFAULT 1',
            ),
            true
        );
        $this->addForeignKey(
            'ophciexamination_advice_leaflet_category_subspecialty_ci_fk',
            'ophciexamination_advice_leaflet_category_subspecialty',
            'category_id',
            'ophciexamination_advice_leaflet_category',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_advice_leaflet_category_subspecialty_s_fk',
            'ophciexamination_advice_leaflet_category_subspecialty',
            'subspecialty_id',
            'subspecialty',
            'id'
        );

        $this->insert('patient_shortcode', array(
            'event_type_id' => $exam_event_type_id,
            'default_code' => 'adg',
            'code' => 'adg',
            'method' => 'getLetterAdviceGiven',
            'description' => 'Advice Given',
        ));
    }

    /**
     * @return void
     * @throws CException
     */
    public function safeDown()
    {
        $this->delete('patient_shortcode', 'code = \'adg\'');

        $this->dropOETable('ophciexamination_advice_leaflet_category_subspecialty', true);
        $this->dropOETable('ophciexamination_advice_leaflet_category_assignment', true);
        $this->dropOETable('ophciexamination_advice_leaflet_category', true);
        $this->dropOETable('ophciexamination_advice_leaflet_entry', true);
        $this->dropOETable('ophciexamination_advice_leaflet', true);

        $this->deleteElementType(
            'OphCiExamination',
            AdviceGiven::class
        );
        $this->dropOETable('et_ophciexamination_advicegiven', true);
    }
}
