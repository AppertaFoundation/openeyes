<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class OEFuzzyDateValidatorNotFutureTest extends CTestCase
{
	protected $validator;
	protected $cModelMock;

	public function setUp(){
		$this->validator = new OEFuzzyDateValidatorNotFuture();

		$this->cModelMock = new ModelMock();
		$this->cModelMock->foo = '2000 14 22';
		$this->cModelMock->bar = '1909 12 23';
	}

	public function testValidateAttribute()
	{
		$this->validator->validateAttribute($this->cModelMock, 'bar');
		$validDateMsg  = $this->cModelMock->getErrors('bar');
		$this->assertFalse($this->cModelMock->hasErrors());
		$this->assertInternalType('array', $validDateMsg);
	}

	public function testValidateAttributeNotFuture()
	{
		$this->cModelMock->bar = date('Y-m-d', strtotime('+1 year'));
		$this->validator->validateAttribute($this->cModelMock, 'bar');
		$notFutureDateMsg  = $this->cModelMock->getErrors('bar');
		$this->assertTrue($this->cModelMock->hasErrors());
		$this->assertInternalType('array', $notFutureDateMsg);
		$this->assertEquals('The date cannot be in the future' , $notFutureDateMsg[0]);
	}

	public function testValidateAttributeNotFutureWithNoDay()
	{
		$this->cModelMock->bar = date('Y-m', strtotime('+1 year'));
		$this->cModelMock->foo = date('Y', strtotime('+1 year'));
		$this->validator->validateAttribute($this->cModelMock, 'bar');
		$notFutureDateMsgBar  = $this->cModelMock->getErrors('bar');
		$this->validator->validateAttribute($this->cModelMock, 'foo');
		$notFutureDateMsgFoo  = $this->cModelMock->getErrors('bar');
		$this->assertTrue($this->cModelMock->hasErrors());
		$this->assertInternalType('array', $notFutureDateMsgBar);
		$this->assertEquals('The date cannot be in the future' , $notFutureDateMsgBar[0]);
		$this->assertInternalType('array', $notFutureDateMsgFoo);
		$this->assertEquals('The date cannot be in the future' , $notFutureDateMsgFoo[0]);
	}

	public function testValidateAttributeIsNotAValidDate(){
		$this->validator->validateAttribute($this->cModelMock, 'foo');
		$invalidDateMsg  = $this->cModelMock->getErrors('foo');
		$this->assertTrue($this->cModelMock->hasErrors());
		$this->assertEquals('This is not a valid date' , $invalidDateMsg[0]);
	}

	public function testValidateAttributeYearIsRequired(){
		$this->cModelMock->foo = '0000 14 22';
		$this->validator->validateAttribute($this->cModelMock, 'foo');
		$yearIsRequiredMsg  = $this->cModelMock->getErrors('foo');
		$this->assertTrue($this->cModelMock->hasErrors());
		$this->assertEquals('Year is required if month is provided' , $yearIsRequiredMsg[0]);
	}

	public function testValidateAttributeMonthIsRequired(){
		$this->cModelMock->foo = '2000 00 22';
		$this->validator->validateAttribute($this->cModelMock, 'foo');
		$monthIsRequiredMsg  = $this->cModelMock->getErrors('foo');
		$this->assertTrue($this->cModelMock->hasErrors());
		$this->assertEquals('Month is required if day is provided' , $monthIsRequiredMsg[0]);
	}

	public function testValidateAttributeInvalidMonth(){
		$this->cModelMock->foo = '2000 14';
		$this->validator->validateAttribute($this->cModelMock, 'foo');
		$invalidMonthMsg  = $this->cModelMock->getErrors('foo');
		$this->assertTrue($this->cModelMock->hasErrors());
		$this->assertEquals('Invalid month value' , $invalidMonthMsg[0]);
	}

	public function tearDown(){
		unset($this->cModelMock);
	}

}

