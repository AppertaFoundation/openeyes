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
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

require_once dirname(__FILE__) . '/NamespacedBaseAPI.php';

class BaseAPITest extends CDbTestCase
{
	public $fixtures = array(
		'event_types' => 'EventType'
	);

	public function testgetModuleClass_unnamespaced()
	{
		$test = $this->getMockBuilder('BaseAPI')
				->disableOriginalConstructor()
				->setMethods(null)
				->setMockClassName('TestModule_API')
				->getMock();

		$r = new ReflectionClass('BaseAPI');
		$m = $r->getMethod('getModuleClass');
		$m->setAccessible(true);

		$this->assertEquals('TestModule', $m->invoke($test));
	}

	public function testgetModuleClass_namespaced()
	{
		$test = new RandomNamespace\Test\TestModule_API();
		$r = new ReflectionClass($test);
		$m = $r->getMethod('getModuleClass');
		$m->setAccessible(true);

		$this->assertEquals('TestModule', $m->invoke($test));
	}

	public function testGetEventType()
	{
		$test = $this->getMockBuilder('BaseAPI')
				->disableOriginalConstructor()
				->setMethods(array('getModuleClass'))
				->getMock();

		$test->expects($this->once())
			->method('getModuleClass')
			->will($this->returnValue($this->event_types('event_type1')->class_name));

		$r = new ReflectionClass('BaseAPI');
		$m = $r->getMethod('getEventType');
		$m->setAccessible(true);

		$this->assertEquals($this->event_types('event_type1'), $m->invoke($test));
	}

}
