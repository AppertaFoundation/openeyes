<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class WorklistPatientTest extends ActiveRecordTestCase
{
    public function getModel()
    {
        return WorklistPatient::model();
    }

    /**
     * @covers WorklistPatient
     * @throws ReflectionException
     */
    public function test_afterValidate_for_scheduled_worklist()
    {
        $wl = ComponentStubGenerator::generate('Worklist', array('scheduled' => true));

        $wp = new WorklistPatient();
        $wp->worklist = $wl;

        $wp->afterValidate();

        $this->assertTrue($wp->hasErrors());
        $this->assertArrayHasKey('when', $wp->getErrors());
    }

    /**
     * @covers WorklistPatient
     * @throws ReflectionException
     */
    public function test_afterValidate_for_unscheduled_worklist()
    {
        $wl = ComponentStubGenerator::generate('Worklist', array('scheduled' => false));

        $wp = new WorklistPatient();
        $wp->worklist = $wl;
        $wp->when = (new DateTime())->format('Y-m-d H:i:s');

        $wp->afterValidate();

        $this->assertTrue($wp->hasErrors());
        $this->assertArrayHasKey('when', $wp->getErrors());
    }

    public function getWorklistAttributeValueProvider()
    {
        return array(
            array(
                array(
                    array('worklist_attribute_id' => 3, 'attribute_value' => 'foo'),
                    array('worklist_attribute_id' => 5, 'attribute_value' => 'bar'),
                    array('worklist_attribute_id' => 8, 'attribute_value' => 'foo'),
                ),
                array('id' => 5),
                'bar',
            ),
            array(
                array(
                    array('worklist_attribute_id' => 3, 'attribute_value' => 'foo'),
                    array('worklist_attribute_id' => 8, 'attribute_value' => 'foo'),
                ),
                array('id' => 5),
                null,
            ),
            array(
                array(
                ),
                array('id' => 5),
                null,
            ),
        );
    }

    /**
     * @covers WorklistPatient
     * @dataProvider getWorklistAttributeValueProvider
     *
     * @param $wp_attrs
     * @param $attr
     * @param $expected
     * @throws ReflectionException
     */
    public function test_getWorklistAttributeValue($wp_attrs, $attr, $expected)
    {
        $worklist_attribute = ComponentStubGenerator::generate('WorklistAttribute', $attr);
        $worklist_patient_attrs = array();
        foreach ($wp_attrs as $attr) {
            $worklist_patient_attrs[] = ComponentStubGenerator::generate('WorklistPatientAttribute', $attr);
        }
        $worklist_patient = new WorklistPatient();
        $worklist_patient->worklist_attributes = $worklist_patient_attrs;

        $this->assertEquals($expected, $worklist_patient->getWorklistAttributeValue($worklist_attribute));
    }

    public function getCurrentAttributesByIdProvider()
    {
        return array(
            array(array()),
            array(
                array(
                    array('worklist_attribute_id' => 5, 'attribute_value' => 'foo'),
                    array('worklist_attribute_id' => 8, 'attribute_value' => 'foo'),
                ),
            ),
        );
    }

    /**
     * @covers WorklistPatient
     * @dataProvider getCurrentAttributesByIdProvider
     *
     * @param $wp_attrs
     * @throws ReflectionException
     */
    public function test_getCurrentAttributesById($wp_attrs)
    {
        $worklist_patient_attrs = array();
        $expected = array();

        foreach ($wp_attrs as $attr) {
            $wpa = ComponentStubGenerator::generate('WorklistPatientAttribute', $attr);
            $expected[$attr['worklist_attribute_id']] = $wpa;
            $worklist_patient_attrs[] = $wpa;
        }
        $worklist_patient = new WorklistPatient();
        $worklist_patient->worklist_attributes = $worklist_patient_attrs;

        $this->assertEquals($expected, $worklist_patient->getCurrentAttributesById());
    }
}
