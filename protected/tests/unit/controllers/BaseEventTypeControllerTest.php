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

	protected function getExaminationController()
	{
		$module = new BaseEventTypeModule('ExaminationEvent',null);
		return new _WrapperBaseEventTypeController('_WrapperBaseEventTypeController', $module);
	}


	protected function getEpisode()
	{
		return ComponentStubGenerator::generate('Episode');
	}

	protected function getElement($element_type)
	{
		// relies on the element type class having been defined in the fixtures
		return new $element_type->class_name;
	}

	/**
	 * mocks the getDefaultElements method to define the elements it should return by default
	 *
	 * @return PHPUnit_Framework_MockObject_MockObject
	 */
	protected function getEventTypeWithNoChildren()
	{
		$event_type = $this->getMockBuilder('EventType')
			->disableOriginalConstructor()
			->getMock();
		$event_type->expects( $this->any() )->method('getDefaultElements')->will($this->returnValue(
				array(
					$this->getElement($this->element_type('history')),
					$this->getElement($this->element_type('visualfunction'))
				)
			));
		return $event_type;
	}

	protected function getEventTypeWithChildren()
	{
		$event_type = $this->getMockBuilder('EventType')
			->disableOriginalConstructor()
			->getMock();
		$event_type->expects( $this->any() )->method('getDefaultElements')->will($this->returnValue(
				array(
					$this->getElement($this->element_type('history')),
					$this->getElement($this->element_type('pasthistory'))
				)
			));
		return $event_type;
	}

	protected function getEventWithNoChildren()
	{
		$event = $this->getMockBuilder('Event')
			->disableOriginalConstructor()
			->getMock();
		$event->expects( $this->any() )->method('getElements')->will($this->returnValue(
				array(
					$this->getElement($this->element_type('history')),
					$this->getElement($this->element_type('visualfunction'))
				)
			));
		return $event;
	}

	protected function getEventWithChildren()
	{
		$event = $this->getMockBuilder('Event')
			->disableOriginalConstructor()
			->getMock();
		$event->expects( $this->any() )->method('getElements')->will($this->returnValue(
				array(
					$this->getElement($this->element_type('history')),
					$this->getElement($this->element_type('pasthistory'))
				)
			));
		return $event;
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
	public function testgetEventElements_noEvent()
	{
		$event_type = $this->getEventTypeWithChildren();

		$controller = new _WrapperBaseEventTypeController();
		$controller->event_type = $event_type;
		$this->assertEquals($event_type->getDefaultElements(), $controller->getEventElements(), 'Controller should return default elements for its event type when no event is set.');
	}

	/**
	 * @covers BaseEventTypeController::getEventElements()
	 */
	public function testgetEventElements_withEvent()
	{
		$event = $this->getEventWithNoChildren();

		$controller = new _WrapperBaseEventTypeController();
		$controller->event = $event;
		$this->assertEquals($event->getElements(), $controller->getEventElements(), 'Controller should return event elements when the event is set.');
	}

	/**
	 * @covers BaseEventTypeController::setOpenElementsFromCurrentEvent
	 */
	public function testsetOpenElementsFromCurrentEvent_create()
	{
		$event_type = $this->getEventTypeWithNoChildren();

		$controller = new _WrapperBaseEventTypeController();
		$controller->event_type = $event_type;

		$controller->setOpenElementsFromCurrentEvent('create');
		$this->assertEquals($event_type->getDefaultElements(), $controller->getOpenElements(), 'Controller should set open_elements to the default elements of the event type.');
	}

	/**
	 * @covers BaseEventTypeController::setOpenElementsFromCurrentEvent
	 */
	public function testsetOpenElementsFromCurrentEvent_update()
	{
		$event = $this->getEventWithChildren();

		$controller = new _WrapperBaseEventTypeController();
		$controller->event = $event;
		$controller->setOpenElementsFromCurrentEvent('update');
		$this->assertEquals($event->getElements(), $controller->getOpenElements(), 'Controller should set open_elements to the elements of the assigned event.');
	}

	/**
	 * @covers BaseEventTypeController::getElements()
	 */
	public function testgetElements_createNoChildren()
	{
		//Create event type with default elements
		$event_type = $this->getEventTypeWithNoChildren();

		//Assign to test controller
		$controller = new _WrapperBaseEventTypeController();
		$controller->event_type = $event_type;
		$controller->setOpenElementsFromCurrentEvent('create');

		//Ensure getElements returns the default elements of the test event type
		$this->assertEquals($event_type->getDefaultElements(), $controller->getElements(), 'Controller should return all the default elements from getElements when none are children');
	}

	/**
	 * @covers BaseEventTypeController::getElements()
	 */
	public function testgetElements_createWithChildren()
	{
		//Create event type with default elements
		$event_type = $this->getEventTypeWithChildren();

		//Assign to test controller
		$controller = new _WrapperBaseEventTypeController();
		$controller->event_type = $event_type;
		$controller->setOpenElementsFromCurrentEvent('create');

		$expected = array();
		foreach ($event_type->getDefaultElements() as $el) {
			if (!$el->getElementType()->isChild()) {
				$expected[] = $el;
			}
		}

		//Ensure that only parent elements are returned, not children
		$this->assertEquals($expected, $controller->getElements(), 'Controller should only return default elements that are not children');
	}

	/**
	 * @covers BaseEventTypeController::getElements()
	 */
	public function testgetElements_updateNoChildren()
	{
		//Create event
		$event = $this->getEventWithNoChildren();

		//Assign to test controller
		$controller = new _WrapperBaseEventTypeController();
		$controller->event = $event;
		$controller->setOpenElementsFromCurrentEvent('update');

		//Ensure getElements returns the default elements of the test event type
		$this->assertEquals($event->getElements(), $controller->getElements(), 'Controller should return all the elements from getElements when none are children');
	}

	/**
	 * @covers BaseEventTypeController::getElements()
	 */
	public function testgetElements_updateWithChildren()
	{
		//Create event
		$event = $this->getEventWithChildren();

		//Assign to test controller
		$controller = new _WrapperBaseEventTypeController();
		$controller->event = $event;
		$controller->setOpenElementsFromCurrentEvent('update');

		$expected = array();
		foreach ($event->getElements() as $el) {
			if (!$el->getElementType()->isChild()) {
				$expected[] = $el;
			}
		}

		//Ensure that only parent elements are returned, not children
		$this->assertEquals($expected, $controller->getElements(), 'Controller should only return event elements that are not children');
	}

	/**
	 * @covers BaseEventTypeController::getChildElements()
	 */
	public function testgetChildElements_parentTypeWithChild()
	{
		$controller = new _WrapperBaseEventTypeController();
		$elements = array(
			$this->getElement($this->element_type('history')),
			$this->getElement($this->element_type('pasthistory'))
		);

		$controller->open_elements = $elements;

		$this->assertEquals(array($elements[1]), $controller->getChildElements($this->element_type('history')), 'Controller should return child element for parent.');
	}

	/**
	 * @covers BaseEventTypeController::getChildElements()
	 */
	public function testgetChildElements_parentTypeWithNoChild()
	{
		$controller = new _WrapperBaseEventTypeController();
		$elements = array(
			$this->getElement($this->element_type('history')),
			$this->getElement($this->element_type('va'))
		);

		$controller->open_elements = $elements;

		$this->assertEquals(array(), $controller->getChildElements($this->element_type('history')), 'Controller should return empty array for no children.');
	}

	/**
	 * @covers BaseEventTypeController::getChildElements()
	 */
	public function testgetChildElements_nonParentType()
	{
		$controller = new _WrapperBaseEventTypeController();
		$elements = array(
			$this->getElement($this->element_type('history')),
			$this->getElement($this->element_type('va'))
		);

		$controller->open_elements = $elements;

		$this->assertEquals(array(), $controller->getChildElements($this->element_type('va')), 'Controller should return empty array for non parent element type.');
	}

	public function testgetOptionalElements()
	{
		$controller = $this->getExaminationController();
		$event_type = $this->getEventTypeWithChildren();
		$event_type->expects( $this->any() )->method('getAllElementTypes')->will($this->returnValue(
				array(
					$this->element_type('history'),
					$this->element_type('pasthistory'),
					$this->element_type('visualfunction'),
					$this->element_type('va'),
				)
			));
		$controller->event_type = $event_type;
		$controller->setOpenElementsFromCurrentEvent('create');
		$optional = $controller->getOptionalElements();

		$this->assertEquals('Visual function', $optional[0]->getElementTypeName(), 'First optional element should be Visual function.');
		$this->assertEquals('Visual acuity', $optional[1]->getElementTypeName(), 'Second optional element should be Visual acuity.');
	}
}

/**
 * Class _WrapperBaseEventTypeController
 *
 * wrapper class around BaseEventTypeController to expose protected methods for testing
 */
class _WrapperBaseEventTypeController extends BaseEventTypeController
{

	public function __construct($id='_WrapperBaseEventTypeController', $module=null)
	{
		parent::__construct($id, $module);
	}

	// expose protected attributes
	public $event_type;
	public $open_elements;
	// expose protected method in abstract class
	public function getEventElements() { return parent::getEventElements(); }
	// expose protected open_elements property
	public function getOpenElements() { return $this->open_elements; }
	// expose protected setOpenElementsFromCurrentEvent method
	public function setOpenElementsFromCurrentEvent($action) { parent::setOpenElementsFromCurrentEvent($action); }
}

/*
class _WrapperElementType extends ElementType
{
	public function __construct($class_name) {
		parent::__construct();
		$this->class_name = $class_name;
	}
}

class TestElementType extends BaseEventTypeElement
{

}
*/