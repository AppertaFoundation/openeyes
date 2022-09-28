<?php
/**
 * OpenEyes.
 *
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@apperta.org>
 * @copyright Copyright (c) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class OEFuzzyDateValidatorNotFutureTest extends CTestCase
{
    protected $validator;
    protected $cModelMock;

    public function setUp(): void
    {
        $this->validator = new OEFuzzyDateValidatorNotFuture();

        $this->cModelMock = new ModelMock();
        $this->cModelMock->foo = '2000 14 22';
        $this->cModelMock->bar = '1909 12 23';
    }

    /**
     * @covers OEFuzzyDateValidatorNotFuture
     */
    public function testValidateAttribute()
    {
        $this->validator->validateAttribute($this->cModelMock, 'bar');
        $validDateMsg  = $this->cModelMock->getErrors('bar');
        $this->assertFalse($this->cModelMock->hasErrors());
        $this->assertIsArray($validDateMsg);
    }

    /**
     * @covers OEFuzzyDateValidatorNotFuture
     */
    public function testValidateAttributeNotFuture()
    {
        $this->cModelMock->bar = date('Y-m-d', strtotime('+1 year'));
        $this->validator->validateAttribute($this->cModelMock, 'bar');
        $notFutureDateMsg  = $this->cModelMock->getErrors('bar');
        $this->assertTrue($this->cModelMock->hasErrors());
        $this->assertIsArray($notFutureDateMsg);
        $this->assertEquals('The date cannot be in the future', $notFutureDateMsg[0]);
    }

    /**
     * @covers OEFuzzyDateValidatorNotFuture
     */
    public function testValidateAttributeNotFutureWithNoDay()
    {
        $this->cModelMock->bar = date('Y-m', strtotime('+1 year'));
        $this->cModelMock->foo = date('Y', strtotime('+1 year'));
        $this->validator->validateAttribute($this->cModelMock, 'bar');
        $notFutureDateMsgBar  = $this->cModelMock->getErrors('bar');
        $this->validator->validateAttribute($this->cModelMock, 'foo');
        $notFutureDateMsgFoo  = $this->cModelMock->getErrors('foo');
        $this->assertTrue($this->cModelMock->hasErrors());
        $this->assertIsArray($notFutureDateMsgBar);
        $this->assertEquals('The date cannot be in the future', $notFutureDateMsgBar[0]);
        $this->assertIsArray($notFutureDateMsgFoo);
        $this->assertEquals('The date cannot be in the future', $notFutureDateMsgFoo[0]);
    }

    /**
     * @covers OEFuzzyDateValidatorNotFuture
     */
    public function testValidateAttributeIsNotAValidDate()
    {
        $this->validator->validateAttribute($this->cModelMock, 'foo');
        $invalidDateMsg  = $this->cModelMock->getErrors('foo');
        $this->assertTrue($this->cModelMock->hasErrors());
        $this->assertEquals('This is not a valid date', $invalidDateMsg[0]);
    }

    /**
     * @covers OEFuzzyDateValidatorNotFuture
     */
    public function testValidateAttributeYearIsRequired()
    {
        $this->cModelMock->foo = '0000 12 22';
        $this->validator->validateAttribute($this->cModelMock, 'foo');
        $yearIsRequiredMsg  = $this->cModelMock->getErrors('foo');
        $this->assertTrue($this->cModelMock->hasErrors());
        $this->assertEquals('Year is required if month is provided', $yearIsRequiredMsg[0]);
    }

    /**
     * @covers OEFuzzyDateValidatorNotFuture
     */
    public function testValidateAttributeMonthIsRequired()
    {
        $this->cModelMock->foo = '2000 00 22';
        $this->validator->validateAttribute($this->cModelMock, 'foo');
        $monthIsRequiredMsg  = $this->cModelMock->getErrors('foo');
        $this->assertTrue($this->cModelMock->hasErrors());
        $this->assertEquals('Month is required if day is provided', $monthIsRequiredMsg[0]);
    }

    /**
     * @covers OEFuzzyDateValidatorNotFuture
     */
    public function testValidateAttributeInvalidMonth()
    {
        $this->cModelMock->foo = '2000 14';
        $this->validator->validateAttribute($this->cModelMock, 'foo');
        $invalidMonthMsg  = $this->cModelMock->getErrors('foo');
        $this->assertTrue($this->cModelMock->hasErrors());
        $this->assertEquals('Invalid month value', $invalidMonthMsg[0]);
    }

    public function tearDown(): void
    {
        unset($this->cModelMock);
    }
}
