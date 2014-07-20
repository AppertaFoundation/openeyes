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

namespace services;

class DataObjectTest extends \PHPUnit_Framework_TestCase
{
	static public function getMockDataTemplate()
	{
	}

	public function setUp()
	{
		$schemas = array(
			array(
				'DataObjectTest_Obj1',
				array(
					'foo' => array(
						'type' => 'FhirType2',
						'plural' => false,
					),
					'bar' => array(
						'type' => 'FhirType2',
						'plural' => true,
					)
				),
			),
			array(
				'FhirType2',
				array(
					'baz' => array(
						'type' => 'integer',
						'plural' => false,
					),
				),
			),
		);

		$marshal = $this->getMockBuilder('FhirMarshal')->disableOriginalConstructor()->getMock();
		$marshal->expects($this->any())->method('getSchema')->will($this->returnValueMap($schemas));
		\Yii::app()->setComponent('fhirMarshal', $marshal);
	}

	public function tearDown()
	{
		\Yii::app()->setComponent('fhirMarshal', null);
	}

	public function fhirObjectDataProvider()
	{
		return array(
			array(
				(object)array(
					'foo' => (object)array('baz' => 1),
					'bar' => array(
						(object)array('baz' => 2),
						(object)array('baz' => 3),
					)
				),
				new DataObjectTest_Obj1(
					array(
						'foo' => new DataObjectTest_Obj2(array('baz' => 1)),
						'bar' => array(
							new DataObjectTest_Obj2(array('baz' => 2)),
							new DataObjectTest_Obj2(array('baz' => 3)),
						),
					)
				),
			)
		);
	}

	/**
	 * @dataProvider fhirObjectDataProvider
	 */
	public function testFromFhir($fhir_object, $object)
	{
		$this->assertEquals($object, DataObjectTest_Obj1::fromFhir($fhir_object));
	}

	/**
	 * @dataProvider fhirObjectDataProvider
	 */
	public function testToFhir($fhir_object, $object)
	{
		$this->assertEquals($fhir_object, $object->toFhir());
	}
}

abstract class DataObjectTest_BaseObj extends DataObject
{
	static public function getFhirTemplate()
	{
		class_exists('DataTemplate');
		$template = \PHPUnit_Framework_MockObject_Generator::getMock('DataTemplateComponent', array(), array(), '', false);
		$template->expects(new \PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount)->method('match')
			->will(new \PHPUnit_Framework_MockObject_Stub_ReturnCallback(function ($obj, &$warnings) { return get_object_vars($obj); }));
		$template->expects(new \PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount)->method('generate')
			->will(new \PHPUnit_Framework_MockObject_Stub_ReturnCallback(function ($values) { return (object)$values; }));
		return $template;
	}
}

class DataObjectTest_Obj1 extends DataObjectTest_BaseObj
{
	static public function getServiceClass($fhir_type)
	{
		if ($fhir_type == 'FhirType2') {
			return 'services\DataObjectTest_Obj2';
		}
		return parent::getServiceClass($fhir_type);
	}

	public $foo;
	public $bar;
}

class DataObjectTest_Obj2 extends DataObjectTest_BaseObj
{
	static protected $fhir_type = 'FhirType2';

	public $baz;
}
