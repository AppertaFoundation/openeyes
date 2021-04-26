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
class WorklistDefinitionMappingTest extends ActiveRecordTestCase
{
    public function getModel()
    {
        return WorklistDefinitionMapping::model();
    }

    public function getMockMappingValue($methods = null)
    {
        return $this->getMockBuilder('WorklistDefinitionMappingValue')
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    public function updateValues_Provider()
    {
        return array(
            array(array()),
            array(array('one')),
            array(array('one', 'two')),
        );
    }

    /**
     * @covers WorklistDefinitionMapping
     * @dataProvider updateValues_Provider
     *
     * @param $values
     */
    public function test_updateValues_new($values)
    {
        $test = $this->getMockBuilder('WorklistDefinitionMapping')
            ->disableOriginalConstructor()
            ->setMethods(array('getInstanceForClass', 'setValueList'))
            ->getMock();

        for ($i = 0; $i < count($values); ++$i) {
            $mock = $this->getMockMappingValue(array('save'));
            $mock->expects($this->once())
                ->method('save')
                ->will($this->returnValue(true));

            $test->expects($this->at($i))
                ->method('getInstanceForClass')
                ->will($this->returnValue($mock));
        }

        $test->expects($this->once())
            ->method('setValueList')
            ->with($values);

        $test->updateValues($values);
    }

    /**
     * @covers WorklistDefinitionMapping
     * @dataProvider updateValues_Provider
     *
     * @param $values
     */
    public function test_updateValues_empty($values)
    {
        $test = $this->getMockBuilder('WorklistDefinitionMapping')
            ->disableOriginalConstructor()
            ->setMethods(array('getInstanceForClass', 'setValueList'))
            ->getMock();

        $mapping_values = array();

        for ($i = 0; $i < count($values); ++$i) {
            $mock = $this->getMockMappingValue(array('delete'));
            $mock->expects($this->once())
                ->method('delete')
                ->will($this->returnValue(true));

            $mock->mapping_value = $values[$i];

            $mapping_values[] = $mock;
        }
        $test->values = $mapping_values;

        $test->expects($this->never())
            ->method('getInstanceForClass');

        $test->expects($this->once())
            ->method('setValueList')
            ->with(array());

        $test->updateValues();
    }
}
