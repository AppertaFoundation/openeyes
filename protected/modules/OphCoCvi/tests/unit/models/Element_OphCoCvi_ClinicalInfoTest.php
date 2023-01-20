<?php

use OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo;
use OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder;

/**
 * (C) Apperta Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @group sample-data
 * @group cvi
 * @group cvi-clinical-info
 */
class Element_OphCoCvi_ClinicalInfoTest extends ModelTestCase
{
    protected $element_cls = Element_OphCoCvi_ClinicalInfo::class;

    /** @test */
    public function is_for_adult_true_when_patient_type_null_reflecting_default()
    {
        $instance = $this->getElementInstance();
        $instance->patient_type = null;

        $this->assertTrue($instance->isForAdult());
    }

    /** @test */
    public function is_for_adult_reflects_values_assigned_to_patient_type()
    {
        $instance = $this->getElementInstance();
        $instance->patient_type = Element_OphCoCvi_ClinicalInfo::CVI_TYPE_ADULT;

        $this->assertTrue($instance->isForAdult());

        $instance->patient_type = Element_OphCoCvi_ClinicalInfo::CVI_TYPE_CHILD;

        $this->assertFalse($instance->isForAdult());
    }

    /** @test */
    public function is_for_child_false_when_patient_type_null_reflecting_default()
    {
        $instance = $this->getElementInstance();
        $instance->patient_type = null;

        $this->assertFalse($instance->isForChild());
    }

    /** @test */
    public function is_for_child_reflects_values_assigned_to_patient_type()
    {
        $instance = $this->getElementInstance();
        $instance->patient_type = Element_OphCoCvi_ClinicalInfo::CVI_TYPE_CHILD;

        $this->assertTrue($instance->isForChild());

        $instance->patient_type = Element_OphCoCvi_ClinicalInfo::CVI_TYPE_ADULT;

        $this->assertFalse($instance->isForChild());
    }
}
