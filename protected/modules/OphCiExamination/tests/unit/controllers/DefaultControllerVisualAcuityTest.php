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

use OEModule\OphCiExamination\controllers\DefaultController;
use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnitValue;
use OEModule\OphCiExamination\tests\traits\InteractsWithVisualAcuity;

/**
 * Class DefaultControllerVisualAcuityTest
 *
 * @package OEModule\OphCiExamination\tests\unit\controllers
 * @covers \OEModule\OphCiExamination\controllers\DefaultController
 * @group sample-data
 * @group visual-acuity
 * @group strabismus
 */
class DefaultControllerVisualAcuityTest extends BaseDefaultControllerTest
{
    use \WithFaker;
    use \WithTransactions;
    use InteractsWithVisualAcuity;

    /** @test */
    public function saving_a_simple_element()
    {
        $savedElement = $this->createElementWithDataWithController([
            'eye_id' => \Eye::LEFT,
            'left_unable_to_assess' => "1"
        ]);

        $this->assertNotNull($savedElement);
        $this->assertEquals('1', $savedElement->left_unable_to_assess);
    }

    /** @test */
    public function saving_element_with_readings()
    {
        $unit = $this->getStandardVisualAcuityUnit();

        $form_data = [
            'eye_id' => \Eye::BOTH,
            'left_readings' => [
                $this->generateVisualAcuityReadingData(['unit_id' => $unit->id])
            ],
            'right_readings' => [
                $this->generateVisualAcuityReadingData(['unit_id' => $unit->id])
            ]
        ];

        $saved_element = $this->createElementWithDataWithController($form_data);
        $this->assertNotNull($saved_element);

        foreach (['right', 'left'] as $side) {
            $side_form_data = $form_data["{$side}_readings"][0];
            $saved_reading = $saved_element->{"{$side}_readings"}[0];
            $this->assertEquals($side_form_data['unit_id'], $saved_reading->unit_id);
            $this->assertEquals($side_form_data['method_id'], $saved_reading->method_id);
            $this->assertEquals($side_form_data['value'], $saved_reading->value);
        }
    }

    /** @test */
    public function readings_are_removed_from_the_element_when_none_submitted()
    {
        $element = $this->generateSavedVisualAcuityElementWithReadings();

        $this->setVariablesInSession($element->event->episode->firm_id);

        // set up the request data for submitting values
        $_REQUEST['patient_id'] = $element->event->episode->patient_id;
        $model_name = \CHtml::modelName(Element_OphCiExamination_VisualAcuity::class);
        $_POST[$model_name] = [
            "left_unable_to_assess" => "1",
            "right_unable_to_assess" => "1"
        ];
        $_GET['id'] = $element->event_id;

        $this->performUpdateRequestWithController();

        $updated_element = Element_OphCiExamination_VisualAcuity::model()->findByPk($element->getPrimaryKey());
        $this->assertCount(0, $updated_element->left_readings);
        $this->assertCount(0, $updated_element->right_readings);
    }

    /** @test */
    public function element_form_retrieved_successfully_for_complex_mode()
    {
        $patient = $this->getPatientWithEpisodes();
        $element_type = Element_OphCiExamination_VisualAcuity::model()->getElementType();
        $_GET['id'] = $element_type->id;
        $_GET['patient_id'] = $patient->id;
        $_GET['record_mode'] = Element_OphCiExamination_VisualAcuity::RECORD_MODE_COMPLEX;

        $episode = $patient->episodes[0];
        // enables controller to know what episode the event will be created in.
        $this->setVariablesInSession($episode->firm_id);

        ob_start();

        $this->performElementFormRequest();

        $response = ob_get_contents();

        ob_end_clean();
        $record_mode_field_name = \CHtml::modelName(Element_OphCiExamination_VisualAcuity::model()) . "[record_mode]";
        $record_mode_value = Element_OphCiExamination_VisualAcuity::RECORD_MODE_COMPLEX;

        $this->assertNotEmpty($response);
        // brittle here as the attribute order is not guaranteed in the input element
        $this->assertContains("value=\"{$record_mode_value}\" name=\"{$record_mode_field_name}\"", $response);
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
        $model_name = \CHtml::modelName(Element_OphCiExamination_VisualAcuity::class);
        $_POST[$model_name] = $data;

        $event_id = $this->performCreateRequestForRandomPatient();

        return Element_OphCiExamination_VisualAcuity::model()->findByAttributes(['event_id' => $event_id]);
    }

    protected function generateVisualAcuityReadingData($attrs = [])
    {
        $result = array_merge(
            [
                'unit_id' => !array_key_exists('unit_id', $attrs) ? $this->getStandardVisualAcuityUnit()->id : null,
                'method_id' => !array_key_exists('method_id', $attrs) ? $this->getRandomLookup(OphCiExamination_VisualAcuity_Method::class)->id : null
            ],
            $attrs
        );

        // ensuring value is valid for the unit
        if (!isset($result['value'])) {
            $value_criteria = new \CDbCriteria();
            $value_criteria->addColumnCondition(['unit_id' => $result['unit_id'], 'selectable' => true]);
            $result['value'] = $this->getRandomLookup(OphCiExamination_VisualAcuityUnitValue::class, 1, $value_criteria)->base_value;
        }

        return $result;
    }
}
