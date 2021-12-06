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
use OEModule\OphCiExamination\components\traits\VisualAcuity_API;
use OEModule\OphCiExamination\models\Element_OphCiExamination_NearVisualAcuity;
use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit;
use OEModule\OphCiExamination\tests\traits\InteractsWithVisualAcuity;
use Patient;

/**
 * Class OphCiExamination_API_VisualAcuityTest
 *
 * @package OEModule\OphCiExamination\tests\unit\components
 * @covers VisualAcuity_API
 * @group sample-data
 * @group strabismus
 * @group visual-acuity
 */
class OphCiExamination_API_VisualAcuityTest extends \OEDbTestCase
{
    use \WithTransactions;
    use \WithFaker;
    use InteractsWithVisualAcuity;

    /** @test */
    public function data_structure_is_defined_correctly_for_va_data()
    {
        $va = $this->generateSavedVisualAcuityElementWithReadings(true);
        $api = new OphCiExamination_API();

        $result = $api->getMostRecentVAData($va->event->patient);
        foreach ([
                     'right_unable_to_assess',
                     'right_eye_missing',
                     'left_unable_to_assess',
                     'left_eye_missing',
                     'beo_unable_to_assess'
                 ] as $bool_attr) {
            $this->assertFalse($result[$bool_attr]);
        }

        foreach (['right', 'left', 'beo'] as $side) {
            $this->assertTrue(is_array($result["{$side}_readings"]));
            $this->assertArrayHasKey("{$side}_comments", $result);
        }

        $this->assertArrayHasKey('event_date', $result);
    }

    /** @test */
    public function bools_casting_in_data_structure_for_va_data()
    {
        $attrs = [
            'right_unable_to_assess' => (string)rand(0, 1),
            'right_eye_missing' => (string)rand(0, 1),
            'left_unable_to_assess' => (string)rand(0, 1),
            'left_eye_missing' => (string)rand(0, 1),
            'beo_unable_to_assess' => (string)rand(0, 1)
        ];
        // ensure readings can validate
        if ($attrs['right_unable_to_assess'] === '1' || $attrs['right_eye_missing'] === '1') {
            $attrs['right_readings'] = [];
        }
        if ($attrs['left_unable_to_assess'] === '1' || $attrs['left_eye_missing'] === '1') {
            $attrs['left_readings'] = [];
        }
        if ($attrs['beo_unable_to_assess'] === '1') {
            $attrs['beo_readings'] = [];
        }

        $va = $this->generateSavedVisualAcuityElementWithReadings(true, $attrs);
        $api = new OphCiExamination_API();
        $result = $api->getMostRecentVAData($va->event->patient);

        foreach ([
                     'right_unable_to_assess',
                     'right_eye_missing',
                     'left_unable_to_assess',
                     'left_eye_missing',
                     'beo_unable_to_assess'
                 ] as $bool_attr) {
            $this->assertTrue(is_bool($result[$bool_attr]), "$bool_attr should always be bool");
            if ((string)$va->$bool_attr === '1') {
                $this->assertEquals(true, $result[$bool_attr], "mismatch for $bool_attr");
            } else {
                $this->assertEquals(false, $result[$bool_attr], "mismatch for $bool_attr");
            }
        }
    }

    /** @test */
    public function standardised_va_data_contains_best_result_and_flags_empty_for_others()
    {
        $missing_side = $this->faker->randomElement(['right', 'left', 'beo']);
        $attrs = [
            "{$missing_side}_unable_to_assess" => "1",
            "{$missing_side}_readings" => []
        ];

        $va = $this->generateSavedVisualAcuityElementWithReadings(true, $attrs);
        $api = new APIWithVisualAcuityAPITrait();

        $result = $api->getMostRecentVADataStandardised($va->event->patient);

        foreach (['right', 'left', 'beo'] as $side) {
            if ($side === $missing_side) {
                $this->assertFalse($result["has_{$side}"]);
                $this->assertArrayNotHasKey("{$side}_result", $result);
                $this->assertArrayNotHasKey("{$side}_method", $result);
                $this->assertArrayNotHasKey("{$side}_method_abbrev", $result);
            } else {
                $this->assertTrue($result["has_{$side}"], "should have {$side} data");
                $this->assertEquals($va->getBest($side), $result["{$side}_result"]);
            }
        }
    }

    public function api_letter_string_provider()
    {
        return [
            [Element_OphCiExamination_VisualAcuity::class, 'getLetterVisualAcuityFindings'],
            [Element_OphCiExamination_NearVisualAcuity::class, 'getLetterNearVisualAcuityFindings']
        ];
    }

    /**
     * @test
     * @dataProvider api_letter_string_provider
     */
    public function letter_string_called_on_element_cls($cls, $api_method)
    {
        $test_api = new APIWithVisualAcuityAPITraitWithLatestElementStub();
        $letter_string = $this->faker->sentence;

        $mock_element = $this->getMockBuilder($cls)
            ->disableOriginalConstructor()
            ->setMethods(['getLetter_string'])
            ->getMock();

        $mock_element->method('getLetter_string')
            ->willReturn($letter_string);

        $test_api->setLatestElement($mock_element, $cls);

        $this->assertEquals($letter_string, $test_api->$api_method(new Patient()));
    }

    /** @test */
    public function best_visual_acuity_for_event_returns_empty_when_no_va_recorded_for_event()
    {
        $test_api = new APIWithVisualAcuityAPITraitWithLatestElementStub();
        $event = $this->getEventToSaveWith();

        $this->assertNull($test_api->getBestVisualAcuityFromEvent($event));
    }

    /** @test */
    public function best_visual_acuity_for_event_returns_best_values_from_element_for_event()
    {
        $test_api = new APIWithVisualAcuityAPITraitWithLatestElementStub();
        $va_element = $this->generateVisualAcuityElementWithReadings(2, 2, 0, false);
        $this->saveElement($va_element);

        $this->assertNotNull($va_element->event);
        $best_right_va = $va_element->getBest('right');
        $best_left_va = $va_element->getBest('left');

        $this->assertNotNull($best_right_va);
        $this->assertNotNull($best_left_va);

        $this->assertEquals(sprintf("%s Right Eye %s Left Eye", $best_right_va, $best_left_va), $test_api->getBestVisualAcuityFromEvent($va_element->event));
    }

    /** @test */
    public function snellen_for_sides_returns_best_acuity_for_side_in_snellen_from_latest_element()
    {
        $test_api = new APIWithVisualAcuityAPITraitWithLatestElementStub();
        $snellen_id = OphCiExamination_VisualAcuityUnit::model()->find('name = ?', array('Snellen Metre'))->id;

        $va_element = $this->generateVisualAcuityElementWithReadings(2, 2, 0, false);
        $this->saveElement($va_element);

        $this->assertNotNull($va_element->event);
        $best_right_va = $va_element->getBestReading('right');
        $best_left_va = $va_element->getBestReading('left');
        $this->assertNotNull($best_right_va);
        $this->assertNotNull($best_left_va);

        $test_api->setLatestElement($va_element);

        $this->assertEquals($best_right_va->convertTo($best_right_va->value, $snellen_id), $test_api->getSnellenVisualAcuityForRight(new Patient()));
        $this->assertEquals($best_left_va->convertTo($best_left_va->value, $snellen_id), $test_api->getSnellenVisualAcuityForLeft(new Patient()));
    }

    /** @test */
    public function snellen_for_sides_is_null_for_no_event()
    {
        $test_api = new APIWithVisualAcuityAPITraitWithLatestElementStub();

        $this->assertNull($test_api->getSnellenVisualAcuityForRight(new Patient()));
        $this->assertNull($test_api->getSnellenVisualAcuityForLeft(new Patient()));
    }

    /** @test */
    public function snellen_for_sides_is_null_with_no_readings()
    {
        $test_api = new APIWithVisualAcuityAPITraitWithLatestElementStub();

        $va_element = $this->generateVisualAcuityElementWithReadings(0, 0, 0, false);
        $this->saveElement($va_element);

        $test_api->setLatestElement($va_element);
        $this->assertNull($test_api->getSnellenVisualAcuityForRight(new Patient()));
        $this->assertNull($test_api->getSnellenVisualAcuityForLeft(new Patient()));
    }

    /** @test */
    public function snellen_for_sides_returns_nr_text_when_requested_on_element_with_no_readings()
    {
        $test_api = new APIWithVisualAcuityAPITraitWithLatestElementStub();
        $mock_element = $this->getMockBuilder(Element_OphCiExamination_VisualAcuity::class)
            ->disableOriginalConstructor()
            ->setMethods(['hasEye','getNotRecordedTextForSide'])
            ->getMock();
        $mock_element->method('hasEye')
            ->willReturn($this->returnValue(true));
        $mock_element
            ->method('getNotRecordedTextForSide')
            ->will(
                $this->returnValueMap([
                    ['right', 'RIGHT NR'],
                    ['left', 'LEFT NR'],
                ]));

        $test_api->setLatestElement($mock_element, Element_OphCiExamination_VisualAcuity::class);

        $this->assertEquals('RIGHT NR', $test_api->getSnellenVisualAcuityForRight(new Patient(), true));
        $this->assertEquals('LEFT NR', $test_api->getSnellenVisualAcuityForLeft(new Patient(), true));
    }

    /** @test */
    public function snellen_for_both_uses_snellen_for_sides()
    {
        $api = $this->getMockBuilder(APIWithVisualAcuityAPITraitWithLatestElementStub::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSnellenVisualAcuityForRight', 'getSnellenVisualAcuityForLeft'])
            ->getMock();

        $test_patient = new Patient();

        $api->method('getSnellenVisualAcuityForRight')
            ->with($test_patient, false)
            ->willReturn('[Right VA Value]');
        $api->method('getSnellenVisualAcuityForLeft')
            ->with($test_patient, false)
            ->willReturn('[Left VA Value]');

        $this->assertEquals('[Right VA Value] on the right and [Left VA Value] on the left', $api->getSnellenVisualAcuityForBoth($test_patient));
    }

    public function letter_visual_acuity_for_side_provider()
    {
        return [
            ['right', 'getLetterVisualAcuityRight'],
            ['left', 'getLetterVisualAcuityLeft'],
        ];
    }

    /**
     * @param $side
     * @param $method
     * @test
     * @dataProvider letter_visual_acuity_for_side_provider
     */
    public function letter_visual_acuity_for_side_returns_the_best_result_in_its_recorded_units($side, $method)
    {
        $test_api = new APIWithVisualAcuityAPITraitWithLatestElementStub();

        $va_element = $this->saveElement($this->generateVAElementWithReadingsForSide($side));
        $best_va = $va_element->getBestReading($side);

        $test_api->setLatestElement($va_element);

        $this->assertEquals($best_va->display_value, $test_api->$method(new Patient()));
    }

    /**
     * @param $side
     * @param $method
     * @test
     * @dataProvider letter_visual_acuity_for_side_provider
     */
    public function letter_visual_acuity_for_side_returns_not_recorded_when_no_readings_set_for_side($side, $method)
    {
        $test_api = new APIWithVisualAcuityAPITraitWithLatestElementStub();

        $va_element = $this->saveElement($this->generateVAElementWithReadingsForSide($side, 0));

        $test_api->setLatestElement($va_element);

        $this->assertEquals('Not Recorded', $test_api->$method(new Patient()));
    }

    /** @test */
    public function letter_visual_acuity_for_both_returns_best_in_recorded_units_for_both_sides()
    {
        $test_api = new APIWithVisualAcuityAPITraitWithLatestElementStub();

        $va_element = $this->saveElement(
            $this->generateVisualAcuityElementWithReadings(2, 2, 0, false)
        );

        $best_right_va = $va_element->getBestReading('right');
        $best_left_va = $va_element->getBestReading('left');
        $this->assertNotNull($best_right_va);
        $this->assertNotNull($best_left_va);

        $test_api->setLatestElement($va_element);

        $this->assertEquals(
            sprintf("%s on the right and %s on the left", $best_right_va->display_value, $best_left_va->display_value),
            $test_api->getLetterVisualAcuityBoth(new Patient())
        );
    }

    /** @test */
    public function letter_visual_acuity_for_both_returns_not_recorded_on_left()
    {
        $test_api = new APIWithVisualAcuityAPITraitWithLatestElementStub();

        $va_element = $this->saveElement(
            $this->generateVAElementWithReadingsForSide("left", 0)
        );

        $test_api->setLatestElement($va_element);

        $this->assertStringContainsString(
            "not recorded on the left",
            $test_api->getLetterVisualAcuityBoth(new Patient())
        );
    }

    /** @test */
    public function letter_visual_acuity_for_both_returns_not_recorded_on_right()
    {
        $test_api = new APIWithVisualAcuityAPITraitWithLatestElementStub();

        $va_element = $this->saveElement(
            $this->generateVAElementWithReadingsForSide("right", 0)
        );

        $test_api->setLatestElement($va_element);

        $this->assertStringContainsString(
            "not recorded on the right",
            $test_api->getLetterVisualAcuityBoth(new Patient())
        );
    }

    public function side_provider()
    {
        return [
            ['right'],
            ['left']
        ];
    }

    /**
     * @test
     * @dataProvider side_provider
     */
    public function letter_visual_acuity_last_6_weeks_ignores_older_va_for_side($side)
    {
        $patient = $this->getPatientWithEpisodes();
        $event = $this->getEventToSaveWith($patient, ['event_date' => date('Y-m-d 00:00:00', strtotime('-6 weeks - 1 day'))]);
        $element = $this->generateVAElementWithReadingsForSide($side);
        $element->event_id = $event->getPrimaryKey();
        $this->assertTrue($element->save(), "element must save successfully");

        $test_api = new APIWithVisualAcuityAPITrait();
        $this->assertEmpty($test_api->{"getLetterVisualAcuity" . ucfirst($side) . "Last6weeks"}($patient));
    }

    /**
     * @test
     * @dataProvider side_provider
     */
    public function letter_visual_acuity_last_6_weeks_returns_most_recent_best_va_for_side($side)
    {
        $patient = $this->getPatientWithEpisodes();
        $older_event = $this->getEventToSaveWith($patient, ['event_date' => date('Y-m-d 00:00:00', strtotime('-5 weeks'))]);
        $older_element = $this->generateVAElementWithReadingsForSide($side);
        $older_element->event_id = $older_event->getPrimaryKey();
        $this->assertTrue($older_element->save(), "element must save successfully");

        $latest_event = $this->getEventToSaveWith($patient, ['event_date' => date('Y-m-d 00:00:00', strtotime('-3 weeks'))]);
        $latest_element = $this->generateVAElementWithReadingsForSide($side);
        $latest_element->event_id = $latest_event->getPrimaryKey();
        $this->assertTrue($latest_element->save(), "element must save successfully");

        $test_api = new APIWithVisualAcuityAPITrait();
        $this->assertEquals(
            sprintf("%s (recorded on %s)", $latest_element->getBest($side), date('j M Y', strtotime('-3 weeks'))),
            $test_api->{"getLetterVisualAcuity" . ucfirst($side) . "Last6weeks"}($patient)
        );
    }

    /** @test */
    public function letter_visual_acuity_last_6_weeks_both_returns_combined_string_for_both_sides()
    {
        $patient = new Patient();
        $mocked_api = $this->getMockBuilder(APIWithVisualAcuityAPITrait::class)
            ->disableOriginalConstructor()
            ->setMethods(["getLetterVisualAcuityRightLast6weeks", "getLetterVisualAcuityLeftLast6weeks"])
            ->getMock();

        $mocked_api->method("getLetterVisualAcuityRightLast6weeks")
            ->with($patient, false)
            ->willReturn("right va");

        $mocked_api->method("getLetterVisualAcuityLeftLast6weeks")
            ->with($patient, false)
            ->willReturn("left va");

        $this->assertEquals(
            "right va on the right and left va on the left",
            $mocked_api->getLetterVisualAcuityBothLast6Weeks(new Patient())
        );
    }

    /**
     * @param $side
     * @test
     * @dataProvider side_provider
     */
    public function letter_va_principal_eye_is_wrapper_for_sided_method($side)
    {
        $wrapped_method = 'getLetterVisualAcuity' . ucfirst($side);
        $eye_for_side = \Eye::model()->findByPk(\Eye::getIdFromName($side));

        $mocked_api = $this->getMockBuilder(APIWithVisualAcuityAPITrait::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPrincipalEye', $wrapped_method])
            ->getMock();
        $mocked_api->method('getPrincipalEye')
            ->willReturn($eye_for_side);

        $mocked_api->method($wrapped_method)
            ->willReturn('foo');

        $this->assertEquals('foo', $mocked_api->getLetterVisualAcuityPrincipal(new Patient()));
    }

    /**
     * @param $side
     * @test
     * @dataProvider side_provider
     */
    public function letter_va_principal_eye_last_6_weeks_is_wrapper_for_sided_method($side)
    {
        $wrapped_method = 'getLetterVisualAcuity' . ucfirst($side) . 'Last6Weeks';
        $eye_for_side = \Eye::model()->findByPk(\Eye::getIdFromName($side));

        $mocked_api = $this->getMockBuilder(APIWithVisualAcuityAPITrait::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPrincipalEye', $wrapped_method])
            ->getMock();
        $mocked_api->method('getPrincipalEye')
            ->willReturn($eye_for_side);

        $mocked_api->method($wrapped_method)
            ->willReturn('foo');

        $this->assertEquals('foo', $mocked_api->getLetterVisualAcuityPrincipalLast6Weeks(new Patient()));
    }

    protected function generateVAElementWithReadingsForSide($side, $count = 2, $complex = false)
    {
        return $this->generateVisualAcuityElementWithReadings(
            $side === 'right' ? $count : 0,
            $side === 'left' ? $count : 0,
            $side === 'beo' ? $count : 0,
            $complex
        );
    }
}

/** Wrapper API class to provide implementation access to the API trait */
class APIWithVisualAcuityAPITraitWithLatestElementStub extends \BaseAPI
{
    use VisualAcuity_API;

    protected $latestElement;
    protected $expectedLatestElementClass;

    public function setLatestElement($element, $expected_cls = null)
    {
        // to make cache collisions easier to deal with:
        $this->resetCacheData();
        $this->latestElement = $element;
        $this->expectedLatestElementClass = $expected_cls ?: get_class($element);
    }

    public function getLatestElement($element, Patient $patient, $use_context = false, $before = null, $after = null)
    {
        if ($element !== $this->expectedLatestElementClass) {
            return null;
        }

        return $this->latestElement;
    }
}

/** Wrapper API class to provide direct implementation access to the API trait */
class APIWithVisualAcuityAPITrait extends \BaseAPI
{
    use VisualAcuity_API;
}
