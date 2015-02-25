<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class RequiredIfFieldValidatorTest extends PHPUnit_Framework_TestCase
{
	private $val;

	public function setUp()
	{
		parent::setUp();

		$this->val = new RequiredIfFieldValidator;
	}

	public function testBasicField_NotRequired()
	{
		$this->val->field = 'f1';
		$this->val->value = 2;

		$object = ComponentStubGenerator::generate('CActiveRecord', array('f1' => 1, 'f2' => null));

		$this->val->validateAttribute($object, 'f2');

		$this->assertEmpty($object->getErrors());
	}

	public function testBasicField_NotMissing()
	{
		$this->val->field = 'f1';
		$this->val->value = 2;

		$object = ComponentStubGenerator::generate('CActiveRecord', array('f1' => 2, 'f2' => 1));

		$this->val->validateAttribute($object, 'f2');

		$this->assertEmpty($object->getErrors());
	}

	public function testBasicField_Missing()
	{
		$this->val->field = 'f1';
		$this->val->value = 2;

		$object = ComponentStubGenerator::generate('CActiveRecord', array('f1' => 2, 'f2' => null));
		$object->expects($this->any())->method('getAttributeLabel')->with('f2')->will($this->returnValue('field 2'));
		$object->expects($this->once())->method('addError')->with('f2', 'field 2 cannot be blank');

		$this->val->validateAttribute($object, 'f2');

		$object->__phpunit_verify();
	}

	public function testSingleRelation_NotRequired()
	{
		$this->val->relation = 'rel';
		$this->val->field = 'f1';
		$this->val->value = 2;

		$related = ComponentStubGenerator::generate('CActiveRecord', array('f1' => 1));
		$object = ComponentStubGenerator::generate('CActiveRecord', array('f2' => null, 'rel' => $related));

		$this->val->validateAttribute($object, 'f2');

		$this->assertEmpty($object->getErrors());
	}

	public function testSingleRelation_NotMissing()
	{
		$this->val->relation = 'rel';
		$this->val->field = 'f1';
		$this->val->value = 2;

		$related = ComponentStubGenerator::generate('CActiveRecord', array('f1' => 2));
		$object = ComponentStubGenerator::generate('CActiveRecord', array('f2' => 1, 'rel' => $related));

		$this->val->validateAttribute($object, 'f2');

		$this->assertEmpty($object->getErrors());
	}

	public function testSingleRelation_Missing()
	{
		$this->val->relation = 'rel';
		$this->val->field = 'f1';
		$this->val->value = 2;

		$related = ComponentStubGenerator::generate('CActiveRecord', array('f1' => 2));
		$object = ComponentStubGenerator::generate('CActiveRecord', array('f2' => null, 'rel' => $related));

		$object->expects($this->any())->method('getAttributeLabel')->with('f2')->will($this->returnValue('field 2'));
		$object->expects($this->once())->method('addError')->with('f2', 'field 2 cannot be blank');

		$this->val->validateAttribute($object, 'f2');

		$object->__phpunit_verify();
	}

	public function testMultipleRelation_NotRequired()
	{
		$this->val->relation = 'rel';
		$this->val->field = 'f1';
		$this->val->value = 2;

		$related = array(
			ComponentStubGenerator::generate('CActiveRecord', array('f1' => 1)),
			ComponentStubGenerator::generate('CActiveRecord', array('f1' => 3)),
		);
		$object = ComponentStubGenerator::generate('CActiveRecord', array('f2' => null, 'rel' => $related));

		$this->val->validateAttribute($object, 'f2');

		$this->assertEmpty($object->getErrors());
	}

	public function testMultipleRelation_NotMissing()
	{
		$this->val->relation = 'rel';
		$this->val->field = 'f1';
		$this->val->value = 2;

		$related = array(
			ComponentStubGenerator::generate('CActiveRecord', array('f1' => 1)),
			ComponentStubGenerator::generate('CActiveRecord', array('f1' => 2)),
		);
		$object = ComponentStubGenerator::generate('CActiveRecord', array('f2' => 1, 'rel' => $related));

		$this->val->validateAttribute($object, 'f2');

		$this->assertEmpty($object->getErrors());
	}

	public function testMultipleRelation_Missing()
	{
		$this->val->relation = 'rel';
		$this->val->field = 'f1';
		$this->val->value = 2;

		$related = array(
			ComponentStubGenerator::generate('CActiveRecord', array('f1' => 1)),
			ComponentStubGenerator::generate('CActiveRecord', array('f1' => 2)),
		);
		$object = ComponentStubGenerator::generate('CActiveRecord', array('f2' => null, 'rel' => $related));

		$object->expects($this->any())->method('getAttributeLabel')->with('f2')->will($this->returnValue('field 2'));
		$object->expects($this->once())->method('addError')->with('f2', 'field 2 cannot be blank');

		$this->val->validateAttribute($object, 'f2');

		$object->__phpunit_verify();
	}

	public function testZeroIsAValue()
	{
		$this->val->field = 'f1';
		$this->val->value = 2;

		$object = ComponentStubGenerator::generate('CActiveRecord', array('f1' => 2, 'f2' => 0));
		$object->expects($this->never())->method('addError');

		$this->val->validateAttribute($object, 'f2');

		$object->__phpunit_verify();
	}

	public function testEmptyStringIsNotAValue()
	{
		$this->val->field = 'f1';
		$this->val->value = 2;

		$object = ComponentStubGenerator::generate('CActiveRecord', array('f1' => 2, 'f2' => ''));
		$object->expects($this->any())->method('getAttributeLabel')->with('f2')->will($this->returnValue('field 2'));
		$object->expects($this->once())->method('addError')->with('f2', 'field 2 cannot be blank');

		$this->val->validateAttribute($object, 'f2');

		$object->__phpunit_verify();
	}
}
