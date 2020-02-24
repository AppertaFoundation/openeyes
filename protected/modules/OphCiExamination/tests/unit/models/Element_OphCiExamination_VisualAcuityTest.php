<?php

use OEModule\OphCiExamination\controllers\DefaultController;
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
        'readings' => models\OphCiExamination_VisualAcuity_Reading::class,
        'unit_values' => models\OphCiExamination_VisualAcuityUnitValue::class,
    );

    public function letter_stringProvider()
    {
        return array(
            array(
                array(array('reading1'), false, false),
                array(null, false, false),
                '6/9',
                null,
            ),
            array(
                array(null, true, true),
                array(array('reading1', 'reading2'), false, false, ''),
                '6/9',
                '12/3',
            ),
        );
    }

    /**
     * @dataProvider letter_stringProvider
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity::getLetter_string
     */
    public function testgetLetter_String($right_eye, $left_eye, $left_res, $right_res)
    {
        $test = new Element_OphCiExamination_VisualAcuity();
        $test->_element_type = $this->element_types('va');

        $left_reading_stub = $this->getMockBuilder(OphCiExamination_VisualAcuity_Reading::class)
            ->disableOriginalConstructor()
            ->setMethods(array('convertTo'))
            ->getMock();
        $left_reading_stub->value = 101;
        $left_reading_stub->method = models\OphCiExamination_VisualAcuity_Method::model()->findByPk(1);

        $left_reading_stub->method('convertTo')
            ->with(101)
            ->willReturn('6/9');

        $right_reading_stub = $this->getMockBuilder(OphCiExamination_VisualAcuity_Reading::class)
            ->disableOriginalConstructor()
            ->setMethods(array('convertTo'))
            ->getMock();
        $right_reading_stub->value = 109;
        $right_reading_stub->method = models\OphCiExamination_VisualAcuity_Method::model()->findByPk(1);

        $right_reading_stub->method('convertTo')
            ->with(109)
            ->willReturn('12/3');

        $test->left_readings = array($left_reading_stub);
        if ($right_res) {
            $test->right_readings = array($right_reading_stub);
        }

        // Unable to mock this class because the output from renderPartial is printed rather than returned as a string.
        $controller = new OEModule\OphCiExamination\controllers\DefaultController(1);

        if ($right_eye) {
            if ($left_eye) {
                $test->eye_id = Eye::BOTH;
            } else {
                $test->eye_id = Eye::RIGHT;
            }

            $test->right_unable_to_assess = $right_eye[1];
            $test->right_eye_missing = $right_eye[2];
        } else {
            $test->eye_id = Eye::LEFT;
        }

        if ($left_eye) {
            $test->left_unable_to_assess = $left_eye[1];
            $test->left_eye_missing = $left_eye[2];
        }
        Yii::app()->controller = $controller;
        if ($left_res) {
            $this->assertContains($left_res, trim(strip_tags($test->getLetter_string())));
        }
        if ($right_res) {
            $this->assertContains($right_res, trim(strip_tags($test->getLetter_string())));
        }
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
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity::getTextForSide
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
        return array(
            array(
                array('eye_id' => Eye::LEFT, 'left_readings' => 'reading1', 'left_unable_to_assess' => true),
                false,
            ),
            array(
                array('eye_id' => Eye::RIGHT, 'left_readings' => 'reading1'),
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
                array('eye_id' => Eye::LEFT, 'right_readings' => 'reading1'),
                false,
            ),
            array(
                array('eye_id' => Eye::LEFT, 'left_readings' => 'reading1', 'left_eye_missing' => true),
                false,
            ),
        );
    }

    /**
     * @dataProvider validate_Provider
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity::validate
     * @param $attributes array
     * @param $should_be_valid bool
     */
    public function testValidate(array $attributes, $should_be_valid)
    {
        //$this->markTestIncomplete('Validation requires complex reading data in $_POST for coverage.');
        $test = new models\Element_OphCiExamination_VisualAcuity();
        foreach ($attributes as $attr => $v) {
            $test->$attr = $v;
        }
        $test->_element_type = $this->element_types('va');
        $model = str_replace('\\', '_', $test->elementType->class_name);
        $_POST[$model] = $test->attributes;
        if (array_key_exists('left_readings', $attributes)) {
            $_POST[$model]['left_readings'] = array($this->readings($attributes['left_readings'])->attributes);
        }
        if (array_key_exists('right_readings', $attributes)) {
            $_POST[$model]['right_readings'] = array($this->readings($attributes['right_readings'])->attributes);
        }
        $this->assertEquals($should_be_valid, $test->validate());
    }
}
