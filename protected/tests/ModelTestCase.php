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

/**
 * Class ModelTestCase
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
abstract class ModelTestCase extends ActiveRecordTestCase
{
    use \HasModelAssertions;

    public function getModel()
    {
        $cls = $this->getModelClass();
        return $cls::model();
    }

    public function getElementInstance()
    {
        $cls = $this->getModelClass();
        return new $cls();
    }

    /** @test */
    public function model_method_return_correct_class()
    {
        $cls = $this->getModelClass();

        $this->assertInstanceOf($cls, $cls::model());
    }

    protected function getModelClass()
    {
        if (!property_exists($this, 'element_cls')) {
            $this->fail('must define the element_cls property in this test');
        }

        return $this->element_cls;
    }

    protected function createValidatingModelMock($cls)
    {
        $mock = $this->getMockBuilder($cls)
            ->disableOriginalConstructor()
            ->setMethods(['validate'])
            ->getMock();

        $mock->method('validate')
            ->willReturn(true);

        return $mock;
    }

    protected function createInvalidModelMock($cls, $errors = [])
    {
        if (count($errors) === 0) {
            // ensure an error is returned by the mock
            $errors = ['foo' => ['bar']];
        }

        $mock = $this->getMockBuilder($cls)
            ->disableOriginalConstructor()
            ->setMethods(['validate', 'getErrors'])
            ->getMock();

        $mock->method('validate')
            ->willReturn(false);
        $mock->method('getErrors')
            ->willReturn($errors);

        return $mock;
    }
}
