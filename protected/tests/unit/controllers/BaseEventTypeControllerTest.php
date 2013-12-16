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
 * Class BaseEventTypeControllerTest
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

	protected function getEpisode()
	{
		return ComponentStubGenerator::generate('Episode');
	}

	protected function getElement($element_type)
	{
		return ComponentStubGenerator::generate('BaseEventTypeElement',
			array('element_type' => $element_type));
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

	/**
	 * @covers BaseEventTypeController::getEventType
	 * @todo should be part of BaseModuleController tests
	 */
	public function testevent_type()
	{
		$this->assertEquals($this->event_type('examination')->id, $this->controller->event_type->id);
	}

	/**
	 * @covers BaseEventTypeController::getEventElements()
	 */
	public function testgetEventElements()
	{
		// mock the default elements method for event type
		// for test-able behaviour on the controller
		$event_type = $this->getMockBuilder('EventType')
			->disableOriginalConstructor()
			->getMock();
		$event_type->expects( $this->any() )->method('getDefaultElements')->will($this->returnValue(array(1,2)));

		$controller = new _getEventElementsController();
		$controller->event_type = $event_type;
		$this->assertEquals($event_type->getDefaultElements(), $controller->getEventElements(), 'Controller should return default elements for its event type when no event is set.');

		$event = $this->getMockBuilder('Event')
			->disableOriginalConstructor()
			->getMock();
		$event->expects( $this->any() )->method('getElements')->will($this->returnValue(array('4','5','6')));

		$controller = new _getEventElementsController();
		$controller->event = $event;
		$this->assertEquals($event->getElements(), $controller->getEventElements(), 'Controller should return event elements when the event is set.');
	}

	/**
	 * @covers BaseEventTypeController::setOpenElementsFromCurrentEvent
	 */
	public function testsetOpenElementsFromCurrentEvent()
	{
		$event_type = $this->getMockBuilder('EventType')
			->disableOriginalConstructor()
			->getMock();
		$event_type->expects( $this->any() )->method('getDefaultElements')->will($this->returnValue(array($this->getElement($this->element_type('history')))));

		$controller = new _getEventElementsController();
		$controller->event_type = $event_type;
		$this->assertEquals(null, $controller->getOpenElements(), 'Controller should start with no open elements');
		$controller->setOpenElementsFromCurrentEvent('create');
		$this->assertEquals($event_type->getDefaultElements(), $controller->getOpenElements(), 'Controller should set open_elements to the default elements of the event type.');

		$event = $this->getMockBuilder('Event')
			->disableOriginalConstructor()
			->getMock();
		$event->expects( $this->any() )->method('getElements')->will($this->returnValue(
				array(
					$this->getElement($this->element_type('history')),
					$this->getElement($this->element_type('pasthistory'))
				)
			));

		$controller = new _getEventElementsController();
		$controller->event = $event;
		$controller->setOpenElementsFromCurrentEvent('update');
		$this->assertEquals($event->getElements(), $controller->getOpenElements(), 'Controller should set open_elements to the elements of the assigned event.');
	}

}

class _getEventElementsController extends BaseEventTypeController
{

	public function __construct()
	{
		parent::__construct('_getEventElementsController');
	}

	public $event_type;
	// expose protected method in abstract class
	public function getEventElements() { return parent::getEventElements(); }
	// expose protected open_elements property
	public function getOpenElements() { return $this->open_elements; }
	// expose protected setOpenElementsFromCurrentEvent method
	public function setOpenElementsFromCurrentEvent($action) { parent::setOpenElementsFromCurrentEvent($action); }
}
