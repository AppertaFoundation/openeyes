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

namespace OEModule\OphCiExamination\tests\unit\controllers;

use OEModule\OphCiExamination\models\Element_OphCiExamination_NearVisualAcuity;
use OEModule\OphCiExamination\models\interfaces\BEOSidedData;
use OEModule\OphCiExamination\models\interfaces\SidedData;

/**
 * Class DefaultControllerNearVisualAcuityTest
 *
 * @package OEModule\OphCiExamination\tests\unit\controllers
 * @covers \OEModule\OphCiExamination\controllers\DefaultController
 * @group sample-data
 * @group strabismus
 * @group near-visual-acuity
 */
class DefaultControllerNearVisualAcuityTest extends BaseDefaultControllerTest
{
    use \WithFaker;
    use \WithTransactions;

    public function side_provider()
    {
        return [
            ["beo", BEOSidedData::BEO],
            ["right", BEOSidedData::RIGHT],
            ["left", BEOSidedData::LEFT]
        ];
    }

    /**
     * @test
     * @dataProvider side_provider
     */
    public function saving_only_one_side($side, $eye_id)
    {
        $this->mockCurrentInstitution();

        // simple save to ensure full success of save to the database
        $saved_element = $this->createElementWithDataWithController([
            "eye_id" => $eye_id,
            "{$side}_unable_to_assess" => '1',
            "record_mode" => Element_OphCiExamination_NearVisualAcuity::RECORD_MODE_COMPLEX
        ]);

        foreach (["beo", "right", "left"] as $side_to_check) {
            if ($side_to_check === $side) {
                $this->assertTrue($saved_element->hasEye($side_to_check));
            } else {
                $this->assertFalse($saved_element->hasEye($side_to_check));
            }
        }
    }

    /**
     * Wrapper for full request cycle to mimic POST-ing the given data
     * to the controller.
     *
     * @param $data
     * @return mixed
     */
    protected function createElementWithDataWithController($data)
    {
        $model_name = \CHtml::modelName(Element_OphCiExamination_NearVisualAcuity::class);
        $_POST[$model_name] = $data;

        $event_id = $this->performCreateRequestForRandomPatient();

        return Element_OphCiExamination_NearVisualAcuity::model()->findByAttributes(['event_id' => $event_id]);
    }
}
