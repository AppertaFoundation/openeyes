<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_DR_Retinopathy;

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

class m191209_235138_create_dr_retinopathy_element extends OEMigration
{
    /**
     * @return bool|void
     */
    public function up()
    {
        $this->createOETable(
            'et_ophciexamination_dr_retinopathy',
            array(
                'id' => 'pk',
                'eye_id' => 'int(10) unsigned DEFAULT ' . Eye::BOTH,
                'event_id' => 'int(10) unsigned',
                'left_overall_grade' => 'string',
                'right_overall_grade' => 'string',
            ),
            true
        );

        $this->addForeignKey(
            'et_ophciexamination_dr_retinopathy_event_fk',
            'et_ophciexamination_dr_retinopathy',
            'event_id',
            'event',
            'id'
        );

        $this->addForeignKey(
            'et_ophciexamination_dr_retinopathy_eye_fk',
            'et_ophciexamination_dr_retinopathy',
            'eye_id',
            'eye',
            'id'
        );

        $this->createOETable(
            'ophciexamination_dr_retinopathy_entry',
            array(
                'id' => 'pk',
                'element_id' => 'int',
                'feature_id' => 'int',
                'eye_id' => 'int(10) unsigned',
                'feature_count' => 'string' // In most cases this will not be set, but to begin with for MA this will be specified as 1, 2, 3, 4 or 5+
            ),
            true
        );

        $this->addForeignKey(
            'ophciexamination_dr_retinopathy_entry_element_fk',
            'ophciexamination_dr_retinopathy_entry',
            'element_id',
            'et_ophciexamination_dr_retinopathy',
            'id'
        );

        $this->addForeignKey(
            'ophciexamination_dr_retinopathy_entry_feature_fk',
            'ophciexamination_dr_retinopathy_entry',
            'feature_id',
            'ophciexamination_drgrading_feature',
            'id'
        );

        $this->addForeignKey(
            'ophciexamination_dr_retinopathy_entry_eye_fk',
            'ophciexamination_dr_retinopathy_entry',
            'eye_id',
            'eye',
            'id'
        );

        $this->createElementType('OphCiExamination', 'DR Retinopathy', [
            'class_name' => Element_OphCiExamination_DR_Retinopathy::class,
            'display_order' => 310,
            'group_name' => 'Retina'
        ]);
    }

    public function down()
    {
        $this->delete(
            'element_type',
            'class_name = :class_name',
            [':class_name' => Element_OphCiExamination_DR_Retinopathy::class]
        );
        $this->dropOETable('ophciexamination_dr_retinopathy_entry', true);
        $this->dropOETable('et_ophciexamination_dr_retinopathy', true);
    }
}
