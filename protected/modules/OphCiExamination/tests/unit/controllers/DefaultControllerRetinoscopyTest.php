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

use OEModule\OphCiExamination\models\interfaces\SidedData;
use OEModule\OphCiExamination\models\Retinoscopy;
use OEModule\OphCiExamination\tests\traits\InteractsWithRetinoscopy;

/**
 * Class DefaultControllerRetinoscopyTest
 *
 * @package OEModule\OphCiExamination\tests\unit\controllers
 * @covers \OEModule\OphCiExamination\controllers\DefaultController
 * @group sample-data
 * @group strabismus
 * @group retinoscopy
 */
class DefaultControllerRetinoscopyTest extends BaseDefaultControllerTest
{
    use \WithFaker;
    use \WithTransactions;
    use InteractsWithRetinoscopy;

    /** @test */
    public function saving_a_simple_element()
    {
        $data = $this->generateRetinoscopyData();
        $data['eye_id'] = SidedData::RIGHT | SidedData::LEFT;

        $savedElement = $this->createElementWithDataWithController($data);

        $this->assertNotNull($savedElement);
        foreach ($data as $attr => $value) {
            $this->assertEquals($value, $savedElement->$attr, "{$attr} should be set to {$value}");
        }
    }

    /** @test */
    public function side_data_removed_when_not_submitted()
    {
        $element = $this->generateSavedRetinoscopyElementWithReadings();

        $sideToKeep = $this->faker->randomElement(['right', 'left']);
        $sideToRemove = $sideToKeep === 'right' ? 'left' : 'right';
        $updateData = $this->generateRetinoscopyData();
        foreach ($element->sidedFields() as $fld) {
            unset($updateData["{$sideToRemove}_{$fld}"]);
        }
        $updateData['eye_id'] = $sideToKeep === 'right' ? SidedData::RIGHT : SidedData::LEFT;

        $this->updateElementWithDataWithController($element, $updateData);

        $element->refresh();

        foreach ($element->sidedFields() as $fld) {
            $this->assertEmpty($element->{"{$sideToRemove}_$fld"});
            $this->assertEquals($updateData["{$sideToKeep}_$fld"], $element->{"{$sideToKeep}_$fld"});
        }
    }

    protected function createElementWithDataWithController($data)
    {
        $model_name = \CHtml::modelName(Retinoscopy::class);
        $_POST[$model_name] = $data;

        $event_id = $this->performCreateRequestForRandomPatient();

        return Retinoscopy::model()->findByAttributes(['event_id' => $event_id]);
    }
}
