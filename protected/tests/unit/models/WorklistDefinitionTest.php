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
class WorklistDefinitionTest extends ActiveRecordTestCase
{
    public function getModel()
    {
        return WorklistDefinition::model();
    }

    protected array $columns_to_skip = [
        'active_from'
    ];

    public function validateMappingKeyProvider()
    {
        return array(
            array(array(), 'fine', null, true),
            array(array('fish', 'cake', 'cow'), 'lemming', null, true),
            array(array('fish', 'cake', 'cow'), 'lemming', 1, true),
            array(array('fish', 'cake', 'cow'), 'cow', null, false),
            array(array('fish', 'cake', 'cow'), 'cow', 2, true),
            array(array('fish', 'cake', 'cow'), 'cow', 1, false),
        );
    }

    /**
     * @covers WorklistDefinition
     * @dataProvider validateMappingKeyProvider
     *
     * @param $mappings
     * @param $key
     * @param $key_id
     * @param $expected
     * @throws ReflectionException
     */
    public function test_validateMappingKey($mappings, $key, $key_id, $expected)
    {
        $test = $this->getMockBuilder('WorklistDefinition')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $test_maps = array();
        foreach ($mappings as $id => $map_key) {
            $test_maps[] = ComponentStubGenerator::generate('WorklistDefinitionMapping', array(
                'id' => $id,
                'key' => $map_key,
            ));
        }

        $test->mappings = $test_maps;

        $this->assertEquals($expected, $test->validateMappingKey($key, $key_id));
    }

    public function getNextDisplayOrderProvider()
    {
        return array(
            array(array(), 1),
            array(array(1, 2, 4), 5),
            array(array(1, null, 3, null, 2), 4),
        );
    }

    /**
     * @covers WorklistDefinition
     * @dataProvider getNextDisplayOrderProvider
     *
     * @param $mappings
     * @param $expected
     * @throws ReflectionException
     */
    public function test_getNextDisplayOrder($mappings, $expected)
    {
        $test = $this->getMockBuilder('WorklistDefinition')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $test_maps = array();
        foreach ($mappings as $id => $display_order) {
            $test_maps[] = ComponentStubGenerator::generate('WorklistDefinitionMapping', array(
                'id' => $id,
                'display_order' => $display_order,
            ));
        }

        $test->mappings = $test_maps;

        $this->assertEquals($expected, $test->getNextDisplayOrder());
    }
}
