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
use OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses;
use OEModule\OphCiExamination\models\OphCiExamination_Diagnosis;

/**
 * Class ElementOphCiExaminationDiagnosesWithSampleDataTest
 *
 * @covers OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses
 * @group sample-data
 * @group diagnoses
 */
class Element_OphCiExamination_DiagnosesWithSampleDataTest extends ModelTestCase
{
    protected $element_cls = Element_OphCiExamination_Diagnoses::class;

    /** @test */
    public function element_model_is_not_dirty_when_diagnoses_entry_is_not_dirty()
    {
        $diagnosis = $this->getMockBuilder(OphCiExamination_Diagnosis::class)->onlyMethods(['isModelDirty'])->getMock();

        $diagnosis->expects($this->once())
            ->method('isModelDirty')
            ->willReturn(false);

        $initial = Element_OphCiExamination_Diagnoses::factory()->withLeftDiagnoses()->create();
        $initial->diagnoses = [$diagnosis];

        $this->assertFalse($initial->isModelDirty());
    }

    /** @test */
    public function element_model_is_dirty_with_diagnoses_count_mismatch()
    {
        $initial = Element_OphCiExamination_Diagnoses::factory()->withLeftDiagnoses()->create();
        $initial->diagnoses = [];

        $this->assertTrue($initial->isModelDirty());
    }
}
