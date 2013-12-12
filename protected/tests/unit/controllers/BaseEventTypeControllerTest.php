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

/**
 * Class BaseControllerTest
 * @group controllers
 */
class BaseEventTypeControllerTest extends CDbTestCase
{
	protected $controller;
	protected $module;

	public $fixtures = array(
		'event_type' => 'EventType',
		'element_type' => 'ElementType',
		'episode' => 'Episode',
	);

	public function setUp()
	{
		// set up fake module for controller to be a part of - this then hooks into the event_type fixture
		$module = new BaseEventTypeModule('ExaminationEvent',null);
		$this->controller = new BaseEventTypeController('BaseEventTypeController', $module);
		parent::setUp();
	}


	/**
	 * @covers BaseEventTypeController::current_episode
	 *
	 */
	public function testCurrent_episode()
	{
		$this->controller->episode = $this->episode('episode1');
		$this->assertEquals($this->episode('episode1')->id, $this->controller->current_episode->id);
	}

	public function testevent_type()
	{
		$this->assertEquals($this->event_type('examination')->id, $this->controller->event_type->id);
	}

	/**
	 * @covers BaseEventTypeController::getEventElements()
	 * @todo test when controller has an event
	 */
	public function testgetEventElements()
	{
		// mock the default elements method for event type
		// for test-able behaviour on the controller
		$event_type = $this->getMockBuilder('EventType')
			->disableOriginalConstructor()
			->getMock();
		$event_type->expects( $this->any() )->method('getDefaultElements')->will($this->returnValue(array(1,2)));

		$controller = new _getEventElementsController('_getEventElementsController');
		$controller->event_type = $event_type;
		$this->assertEquals(count($event_type->getDefaultElements()), count($controller->getEventElements()));
	}
}

class _getEventElementsController extends BaseEventTypeController
{
	public $event_type;
	// expose protected method in abstract class
	public function getEventElements() { return parent::getEventElements(); }
}
