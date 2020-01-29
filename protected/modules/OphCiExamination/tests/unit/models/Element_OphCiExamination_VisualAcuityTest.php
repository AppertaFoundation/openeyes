<?php

use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading;
use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
use OEModule\OphCiExamination\models;

class Element_OphCiExamination_VisualAcuityTest extends CDbTestCase
{
    protected $fixtures = array(
        'element_types' => ElementType::class,
    );

    public function letter_stringProvider()
    {
        return array(
            array(
                array(null, true, false),
                null,
                "Visual acuity:\nRight Eye: Unable to assess\nLeft Eye: not recorded\n",
            ),
            array(
                    array('12/3', false, false),
                    array(null, false, false),
                    "Visual acuity:\nRight Eye: 12/3\nLeft Eye: not recorded\n",
            ),
                array(
                        array(null, true, true),
                        array('3/6, 1/12', false, false, ''),
                        "Visual acuity:\nRight Eye: Unable to assess, Eye missing\nLeft Eye: 3/6, 1/12\n",
                ),
        );
    }

    /**
     * @dataProvider letter_stringProvider
     */
    public function testgetLetter_String($right_eye, $left_eye, $res)
    {
        $this->markTestSkipped('Testing this case requires a controller object, which has not been set and is not set when running unit tests.');
        $test = $this->getMockBuilder('\OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity')
                ->disableOriginalConstructor()
                ->setMethods(array('getCombined'))
                ->getMock();

        $combined_at = 0;

        if ($right_eye) {
            if ($left_eye) {
                $test->eye_id = Eye::BOTH;
            } else {
                $test->eye_id = Eye::RIGHT;
            }

            $test->right_unable_to_assess = $right_eye[1];
            $test->right_eye_missing = $right_eye[2];

            $combined = $right_eye[0];
            $test->expects($this->at($combined_at))
                    ->method('getCombined')
                    ->with('right')
                    ->will($this->returnValue($combined));
            ++$combined_at;
            if ($combined) {
                $test->expects($this->at($combined_at))
                        ->method('getCombined')
                        ->with('right')
                        ->will($this->returnValue($combined));
                ++$combined_at;
            }
        } else {
            $test->eye_id = Eye::LEFT;
        }

        if ($left_eye) {
            $test->left_unable_to_assess = $left_eye[1];
            $test->left_eye_missing = $left_eye[2];
            $combined = $left_eye[0];

            $test->expects($this->at($combined_at))
                    ->method('getCombined')
                    ->with('left')
                    ->will($this->returnValue($combined));
            ++$combined_at;
            if ($combined) {
                $test->expects($this->at($combined_at))
                        ->method('getCombined')
                        ->with('left')
                        ->will($this->returnValue($combined));
            }
        }
        $this->assertEquals($res, $test->getLetter_string());
    }

    public function getTextForSide_Provider()
    {
        return array(
            array('left', false, true, true, 'Unable to assess, Eye missing'),
            array('right', false, true, true, 'Unable to assess, Eye missing'),
            array('left', false, true, false, 'Unable to assess'),
            array('left', false, false, true, 'Eye missing'),
            array('left', false, false, false, 'not recorded'),
            array('left', true, false, false, null),
        );
    }

    /**
     * @dataProvider getTextForSide_Provider
     */
    public function testgetTextForSide($side, $readings, $unable, $eye_missing, $res)
    {
        $readingMock = $this->getMockBuilder(OphCiExamination_VisualAcuity_Reading::class)
            ->setMethods(array('init'))
            ->getMock();

        $test = new models\Element_OphCiExamination_VisualAcuity();
        if ($side === 'left') {
            $test->eye_id = Eye::LEFT;
        } else {
            $test->eye_id = Eye::RIGHT;
        }
        if ($readings) {
            $test->{$side.'_readings'} = array($readingMock);
        }
        $test->{$side.'_unable_to_assess'} = $unable;
        $test->{$side.'_eye_missing'} = $eye_missing;

        $this->assertEquals($res, $test->getTextForSide($side));
    }

    public function validate_Provider()
    {
        $readingMock = $this->getMockBuilder('\OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading')
            ->setMethods(array('init'))
            ->getMock();

        return array(
            array(
                array('eye_id' => Eye::LEFT, 'left_readings' => array($readingMock), 'left_unable_to_assess' => true),
                false,
            ),
            array(
                    array('eye_id' => Eye::RIGHT, 'left_readings' => array($readingMock)),
                    false,
            ),
            array(
                    array('eye_id' => Eye::RIGHT),
                    false,
            ),
            array(
                    array('eye_id' => Eye::LEFT, 'left_unable_to_assess' => true),
                    true,
            ),
            array(
                array('eye_id' => Eye::LEFT, 'right_readings' => array($readingMock)),
                false,
            ),
            array(
                    array('eye_id' => Eye::LEFT, 'left_readings' => array($readingMock), 'left_eye_missing' => true),
                    false,
            ),
        );
    }

    /**
     * @dataProvider validate_Provider
     * @param $attributes array
     * @param $should_be_valid bool
     */
    public function testValidate(array $attributes, $should_be_valid)
    {
        $this->markTestIncomplete('Validation requires complex reading data in $_POST for coverage.');
        $test = new models\Element_OphCiExamination_VisualAcuity();
        foreach ($attributes as $attr => $v) {
            $test->$attr = $v;
        }
        $test->_element_type = $this->element_types('va');
        $model = str_replace('\\', '_', $test->elementType->class_name);
        $_POST[$model] = $test->attributes;
        $_POST[$model]['left_readings'] = array_key_exists('left_readings', $attributes) ? array_map(static function ($item) {
            return $item->attributes;
        }, $attributes['left_readings']) : null;
        $_POST[$model]['right_readings'] = array_key_exists('right_readings', $attributes) ? array_map(static function ($item) {
            return $item->attributes;
        }, $attributes['right_readings']) : null;

        $this->assertEquals($should_be_valid, $test->validate());
    }
}
