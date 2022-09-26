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

namespace OEModule\OphCiExamination\tests\unit\components;

use OEModule\OphCiExamination\components\OphCiExamination_API;
use OEModule\OphCiExamination\components\traits\Refraction_API;
use OEModule\OphCiExamination\models\CorrectionGiven;
use OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction;
use OEModule\OphCiExamination\models\OphCiExamination_Refraction_Type;
use OEModule\OphCiExamination\models\Retinoscopy;
use OEModule\OphCiExamination\tests\traits\InteractsWithCorrectionGiven;
use OEModule\OphCiExamination\tests\traits\InteractsWithRefraction;
use OEModule\OphCiExamination\tests\traits\InteractsWithRetinoscopy;

/**
 * Class OphCiExamination_API_RefractionTest
 *
 * @package OEModule\OphCiExamination\tests\unit\components
 * @covers \OEModule\OphCiExamination\components\traits\Refraction_API
 * @group sample-data
 * @group strabismus
 *
 */
class OphCiExamination_API_RefractionTest extends \OEDbTestCase
{
    use InteractsWithRefraction;
    use InteractsWithCorrectionGiven;
    use InteractsWithRetinoscopy;
    use \InteractsWithPatient;

    use \WithTransactions;

    public function setUp(): void
    {
        parent::setUp();
        OphCiExamination_API::clearDataCache();
    }

    /** @test */
    public function data_structure_is_defined_correctly_for_refraction_data()
    {
        $refraction = $this->generateSavedRefractionWithReadings();
        $api = new OphCiExamination_API();

        $result = $api->getMostRecentRefractionData($refraction->event->patient);
        foreach (['event_date', 'right_comments', 'left_comments', 'right_readings', 'left_readings'] as $expected_key) {
            $this->assertArrayHasKey($expected_key, $result);
        }

        $this->assertEquals($refraction->event->event_date, $result['event_date']);

        foreach (['right', 'left'] as $side) {
            $this->assertTrue(is_array($result["{$side}_readings"]));
            foreach ($result["{$side}_readings"] as $reading) {
                $this->assertArrayHasRefractionReadingStructure($reading);
            }
            $this->assertArrayHasRefractionReadingStructure($result["{$side}_priority_reading"]);
        }
    }

    /** @test */
    public function refraction_text_returns_only_single_result_as_spherical_equivalent()
    {
        $types = OphCiExamination_Refraction_Type::model()->findAll(['order' => 'priority asc']);
        $right_readings = [
            $this->generateRefractionReading(['eye_id' => \Eye::RIGHT, 'type_id' => $types[0]->id]),
            $this->generateRefractionReading(['eye_id' => \Eye::RIGHT, 'type_id' => $types[2]->id]),
            $this->generateRefractionReading(['eye_id' => \Eye::RIGHT, 'type_id' => $types[1]->id])
        ];

        $left_readings = [
            $this->generateRefractionReading(['eye_id' => \Eye::LEFT, 'type_id' => $types[3]->id]),
            $this->generateRefractionReading(['eye_id' => \Eye::LEFT, 'type_id' => $types[1]->id])
        ];

        $reading = $this->generateSavedRefractionWithReadings(['right_readings' => $right_readings, 'left_readings' => $left_readings]);

        $api = new OphCiExamination_API();
        $result = $api->getRefractionTextFromEvent($reading->event);

        $this->assertStringContainsString($right_readings[0]->getSphericalEquivalent(), $result, "result should contain highest priority result on right");
        $this->assertStringContainsString($left_readings[1]->getSphericalEquivalent(), $result, "result should contain highest priority result on left");
    }

    public function element_refraction_values_provider()
    {
        return [
            ['generateSavedRetinoscopyElementWithReadings', 'right_refraction', 'left_refraction'],
            ['generateSavedRefractionWithReadings', 'right_priority_reading', 'left_priority_reading'],
            ['generateSavedCorrectionGiven', 'right_refraction', 'left_refraction']
        ];
    }

    /**
     * @param $element_generator
     * @param $expected_right_attr
     * @param $expected_left_attr
     * @test
     * @dataProvider element_refraction_values_provider
     */
    public function element_refraction_values_returned($element_generator, $expected_right_attr, $expected_left_attr)
    {
        $element = $this->$element_generator();
        $api = new OphCiExamination_API();

        $result = $api->getLatestRefractionReadingFromAnyElementType($element->event->patient);
        $this->assertEquals($element->$expected_right_attr, $result['right']);
        $this->assertEquals($element->$expected_left_attr, $result['left']);
    }

    public function element_priority_provider()
    {
        return [
            [[Element_OphCiExamination_Refraction::class, Retinoscopy::class], 0, 'refraction over retinoscopy'],
            [[Element_OphCiExamination_Refraction::class, CorrectionGiven::class], 1, 'correction given over retinoscopy'],
            [[Element_OphCiExamination_Refraction::class, CorrectionGiven::class, Retinoscopy::class], 1, 'correction given over refraction/retinoscopy'],
            [[CorrectionGiven::class, Retinoscopy::class], 0, 'correction given over retinoscopy']
        ];
    }

    /**
     * @param $element_classes
     * @param $expected_element_class_index
     * @test
     * @dataProvider element_priority_provider
     */
    public function correct_element_values_returned_when_on_same_event($element_classes, $expected_element_class_index, $explanation)
    {
        $event = $this->getEventToSaveWith();
        $generated = array_map(
            function ($cls) use ($event) {
                return $this->generateElementForClassForEvent($cls, $event);
            },
            $element_classes
        );

        $api = new OphCiExamination_API();
        $result = $api->getLatestRefractionReadingFromAnyElementType($event->patient);

        $this->assertEquals(
            $this->getReadingValueForSideFromElement('right', $generated[$expected_element_class_index]),
            $result['right'],
            $explanation
        );

        $this->assertEquals(
            $this->getReadingValueForSideFromElement('left', $generated[$expected_element_class_index]),
            $result['left'],
            $explanation
        );
    }

    /** @test */
    public function latest_refraction_null_for_patient_with_no_data()
    {
        $patient = $this->generateSavedPatient();
        $api = new OphCiExamination_API();

        $this->assertNull($api->getLatestRefractionReadingFromAnyElementType($patient));
    }

    protected function generateElementForClassForEvent($cls, $event)
    {
        $generateMethod = [
            Element_OphCiExamination_Refraction::class => 'generateRefractionData',
            Retinoscopy::class => 'generateRetinoscopyData',
            CorrectionGiven::class => 'generateCorrectionGivenData'
        ][$cls];

        $data = $this->$generateMethod();
        $element = new $cls();
        $element->setAttributes($data);
        $element->event_id = $event->id;
        $element->save();

        return $element;
    }

    protected function getReadingValueForSideFromElement($side, $element)
    {
        if (get_class($element) === Element_OphCiExamination_Refraction::class) {
            return (string) $element->{"{$side}_priority_reading"};
        }
        return $element->{"{$side}_refraction"};
    }

    protected function assertArrayHasRefractionReadingStructure($data)
    {
        foreach (['type_name', 'refraction', 'spherical_equivalent'] as $key) {
            $this->assertArrayHasKey($key, $data, "missing expected attribute for reading");
        }
    }
}
