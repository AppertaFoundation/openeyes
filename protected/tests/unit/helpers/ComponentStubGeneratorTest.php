<?php

use PHPUnit\Framework\MockObject\MockObject;

/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class ComponentStubGeneratorTest extends PHPUnit_Framework_TestCase
{
    private MockObject $stub;

    /**
     * @throws ReflectionException
     */
    public function setUp(): void
    {
        $this->stub = ComponentStubGenerator::generate(
            'ComponentStubGeneratorTest_ExampleComponent',
            array(
                'normal_prop' => 'bar',
                'magic_get_prop' => 'bar',
                'get_method_prop' => 'bar',
            )
        );
    }

    /**
     * @covers ComponentStubGenerator
     */
    public function testNormalProp()
    {
        $this->assertEquals('bar', $this->stub->normal_prop);
    }

    /**
     * @covers ComponentStubGenerator
     */
    public function testMagicGetProp()
    {
        $this->assertEquals('bar', $this->stub->magic_get_prop);
    }

    /**
     * @covers ComponentStubGenerator
     */
    public function testGetMethodProp_Direct()
    {
        $this->assertEquals('bar', $this->stub->get_method_prop);
    }

    /**
     * @covers ComponentStubGenerator
     */
    public function testGetMethodProp_MethodCall()
    {
        $this->assertEquals('bar', $this->stub->getGet_method_prop());
    }

    /**
     * @covers ComponentStubGenerator
     */
    public function testDontOverrideMethodsThatTakeArguments()
    {
        $this->stub->expects($this->any())->method('getNormal_prop')->will($this->returnArgument(0));
        $this->assertEquals('foo', $this->stub->getNormal_prop('foo'));
    }

    /**
     * @covers ComponentStubGenerator
     */
    public function testChangeNormalProp()
    {
        $this->stub->normal_prop = 'baz';
        $this->assertEquals('baz', $this->stub->normal_prop);
    }

    /**
     * @covers ComponentStubGenerator
     */
    public function testChangeMagicGetProp()
    {
        $this->stub->magic_get_prop = 'baz';
        $this->assertEquals('baz', $this->stub->magic_get_prop);
    }

    /**
     * @covers ComponentStubGenerator
     */
    public function testChangeGetMethodProp_GetDirect()
    {
        $this->stub->get_method_prop = 'baz';
        $this->assertEquals('baz', $this->stub->get_method_prop);
    }

    /**
     * @covers ComponentStubGenerator
     */
    public function testChangeGetMethodProp_GetWithMethodCall()
    {
        $this->stub->get_method_prop = 'baz';
        $this->assertEquals('baz', $this->stub->getGet_method_prop());
    }

    /**
     * @covers ComponentStubGenerator
     */
    public function testAddNormalProp()
    {
        $this->stub->other_normal_prop = 'baz';
        $this->assertEquals('baz', $this->stub->other_normal_prop);
    }

    /**
     * @covers ComponentStubGenerator
     */
    public function testAddMagicGetProp()
    {
        $this->stub->other_magic_get_prop = 'baz';
        $this->assertEquals('baz', $this->stub->other_magic_get_prop);
    }

    /**
     * @covers ComponentStubGenerator
     */
    public function testAddGetMethodProp_GetDirect()
    {
        $this->stub->other_get_method_prop = 'baz';
        $this->assertEquals('baz', $this->stub->other_get_method_prop);
    }

    /**
     * @covers ComponentStubGenerator
     */
    public function testAddGetMethodProp_GetWithMethodCall()
    {
        $this->stub->other_get_method_prop = 'baz';
        $this->assertEquals('baz', $this->stub->getOther_get_method_prop());
    }

    /**
     * @covers ComponentStubGenerator
     */
    public function testIsset_NormalProp()
    {
        $this->assertTrue(isset($this->stub->normal_prop));
    }

    /**
     * @covers ComponentStubGenerator
     */
    public function testIsset_MagicGetProp()
    {
        $this->assertTrue(isset($this->stub->magic_get_prop));
    }

    /**
     * @covers ComponentStubGenerator
     */
    public function testIsset_GetMethodProp()
    {
        $this->assertTrue(isset($this->stub->get_method_prop));
    }

    /**
     * @covers ComponentStubGenerator
     */
    public function testIsset_AddedNormalProp()
    {
        $this->stub->other_normal_prop = 'baz';
        $this->assertTrue(isset($this->stub->other_normal_prop));
    }

    /**
     * @covers ComponentStubGenerator
     */
    public function testIsset_AddedMagicGetProp()
    {
        $this->stub->other_magic_get_prop = 'baz';
        $this->assertTrue(isset($this->stub->other_magic_get_prop));
    }

    /**
     * @covers ComponentStubGenerator
     */
    public function testIsset_AddedGetMethodProp()
    {
        $this->stub->other_get_method_prop = 'baz';
        $this->assertTrue(isset($this->stub->other_get_method_prop));
    }
}

class ComponentStubGeneratorTest_ExampleComponent extends CComponent
{
    public $normal_prop = 'foo';
    public $other_normal_prop = 'foo';

    public function __get($name)
    {
        if ($name == 'magic_get_prop' || $name == 'other_magic_get_prop') {
            return 'foo';
        }

        return parent::__get($name);
    }

    public function getGet_method_prop()
    {
        return 'foo';
    }

    public function getOther_get_method_prop()
    {
        return 'foo';
    }

    /**
     * Not actually a property getter because it takes an argument.
     */
    public function getNormal_prop($a)
    {
        return $a;
    }
}
