<?php

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

namespace OEModule\OphCiExamination\tests\unit\models;

use ModelTestCase;
use OEModule\OphCiExamination\models\OphCiExamination_Diagnosis;

/**
 * Class OphCiExamination_DiagnosisTest
 *
 * @covers OEModule\OphCiExamination\models\OphCiExamination_Diagnosis
 * @group sample-data
 * @group examination
 * @group diagnoses
 */
class OphCiExamination_DiagnosisTest extends ModelTestCase
{
    protected $element_cls = OphCiExamination_Diagnosis::class;

    /** @test */
    public function diagnosis_model_is_dirty_when_principal_is_changed()
    {
        $diagnosis = OphCiExamination_Diagnosis::factory()->create();
        $diagnosis->principal = !$diagnosis->principal;
        $this->assertTrue($diagnosis->isModelDirty(), "Updating the principal attribute should make the model dirty");
    }

    /** @test */
    public function diagnosis_model_is_dirty_when_eye_id_is_changed()
    {
        $diagnosis = OphCiExamination_Diagnosis::factory()->create();
        $diagnosis->eye_id = 3;

        $this->assertTrue($diagnosis->isModelDirty(), "Updating the eye id attribute should make the model dirty");
    }
}
