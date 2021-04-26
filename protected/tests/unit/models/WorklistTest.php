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
class WorklistTest extends ActiveRecordTestCase
{
    public function getModel()
    {
        return Worklist::model();
    }

    public function datesProvider()
    {
        return array(
            array('2015-06-05', '2015-06-05', true, null),
            array('2016-06-05', '2016-05-05', false, array(
                'start' => array('Start Date must be on or before End Date'),
            )),
        );
    }

    /**
     * @covers Worklist
     * @dataProvider datesProvider
     * @param $start
     * @param $end
     * @param $pass
     * @param $expected_errors
     */
    public function testDateValidation($start, $end, $pass, $expected_errors)
    {
        $worklist = new Worklist();
        $worklist->setAttributes(array(
            'start' => $start,
            'end' => $end,
        ));

        $res = $worklist->validate(array('start', 'end'));

        $this->assertEquals($pass, $res);
        $wl_errors = $worklist->getErrors();
        if ($expected_errors) {
            foreach ($expected_errors as $fld => $errors) {
                $this->assertTrue(array_key_exists($fld, $wl_errors));
                foreach ($errors as $error) {
                    $this->assertTrue(in_array($error, $wl_errors[$fld]));
                }
            }
        } else {
            $this->assertTrue(empty($wl_errors));
        }
    }

    public function getMappingAttributeIdsByNameProvider()
    {
        return array(
            array(array()),
            array(array(
                array('id' => 5, 'name' => 'foo'),
                array('id' => 7, 'name' => 'bar'),
            )),
        );
    }

    /**
     * @covers Worklist
     * @dataProvider getMappingAttributeIdsByNameProvider
     *
     * @param $worklist_attrs
     * @throws ReflectionException
     */
    public function test_getMappingAttributeIdsByName($worklist_attrs)
    {
        $worklist_attrs = array();
        $expected = array();

        foreach ($worklist_attrs as $attr) {
            $wa = ComponentStubGenerator::generate('WorklistAttribute', $attr);
            $expected[$attr['name']] = $wa;
            $worklist_attrs[] = $wa;
        }
        $worklist = new Worklist();
        $worklist->mapping_attributes = $worklist_attrs;

        $this->assertEquals($expected, $worklist->getMappingAttributeIdsByName());
    }
}
