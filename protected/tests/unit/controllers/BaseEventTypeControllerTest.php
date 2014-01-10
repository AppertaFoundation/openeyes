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
class BaseEventTypeControllerTest extends PHPUnit_Framework_TestCase
{
	protected $start = null;
	protected $latest = null;

	protected function getExaminationController()
	{
		$module = new BaseEventTypeModule('ExaminationEvent',null);
		return new _WrapperBaseEventTypeController('_WrapperBaseEventTypeController', $module);
	}

	protected function getEpisode()
	{
		return ComponentStubGenerator::generate('Episode');
	}

	/**
	 * Returns a stub ElementType that has the given parameters and provides a mock BaseEventTypeElement
	 * when getInstance called
	 *
	 * @param $class_name
	 * @param $name
	 * @param array $params
	 * @return object
	 */
	protected function getElementType($class_name, $name, $params = array()) {
		$et = ComponentStubGenerator::generate('ElementType',
			array_merge(array(
				'class_name' => $class_name,
				'name' => $name,
				), $params));

		foreach ($params as $k => $v) {
			$et->$k = $v;
		}

		// an element instance to return from the elementtype
		$e = $this->getMockBuilder('BaseEventTypeElement')
			->disableOriginalConstructor()
			->setMockClassName($class_name)
			->setMethods(array('getElementType', 'getElementTypeName'))
			->getMock();

		$e->expects( $this->any() )->method('getElementType')->will($this->returnValue($et));
		$e->expects( $this->any() )->method('getElementTypeName')->will($this->returnValue($name));

		$et->expects( $this->any() )->method('getInstance')->will($this->returnValue(
			$e
		));

		return $et;
	}

	/**
	 * mocks BaseEventTypeElement as the class_name of $element_type
	 * @param $element_type
	 * @return PHPUnit_Framework_MockObject_MockObject
	 */
	protected function getElement($element_type)
	{
		return $element_type->getInstance();
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
			->setMethods(array('getDefaultElements'))
			->getMock();
		$element_types = $this->getAllElementTypes();

		$event_type->expects( $this->any() )->method('getDefaultElements')->will($this->returnValue(
				array(
					$this->getElement($element_types[0]),
					$this->getElement($element_types[2])
				)
			));

		return $event_type;
	}

	protected function getEventTypeWithChildren()
	{
		$event_type = $this->getMockBuilder('EventType')
			->disableOriginalConstructor()
			->getMock();
		$element_types = $this->getAllElementTypes();
		$event_type->expects( $this->any() )->method('getDefaultElements')->will($this->returnValue(
				array(
					$this->getElement($element_types[0]),
					$this->getElement($element_types[1])
				)
			));
		return $event_type;
	}

	/**
	 * convenience function that mocks up series of element type objects to be used in various tests
	 */
	protected function getAllElementTypes()
	{
		$element_types = array(
			$this->getElementType('HistoryElementType','history'),
			$this->getElementType('PastHistoryElementType','pasthistory'),
			$this->getElementType('VisualFunctionElementType','visualfunction'),
			$this->getElementType('VisualAcuityElementType', 'va'),
		);
		$element_types[0]->child_element_types = array($element_types[1]);
		// define pasthistory as a child element type
		$element_types[1]->parent_element_type = $element_types[0];
		foreach ($element_types as $et) {
			if ($et->name == 'pasthistory') {
				$et->expects( $this->any() )->method('isChild')->will($this->returnValue(true));
			}
			else {
				$et->expects( $this->any() )->method('isChild')->will($this->returnValue(false));
			}
		}

		return $element_types;
	}
	/**
	 * @return PHPUnit_Framework_MockObject_MockObject
	 */
	protected function getEventTypeWithAllElementTypes()
	{
		$event_type = $this->getEventTypeWithChildren();

		$all_element_types = $this->getAllElementTypes();

		$event_type->expects( $this->any() )->method('getAllElementTypes')->will($this->returnValue(
				$all_element_types
			));

		return $event_type;
	}

	protected function getEventWithNoChildren()
	{
		$event = $this->getMockBuilder('Event')
			->disableOriginalConstructor()
			->getMock();
		$element_types = $this->getAllElementTypes();

		$event->expects( $this->any() )->method('getElements')->will($this->returnValue(
				array(
					$this->getElement($element_types[0]),
					$this->getElement($element_types[2])
				)
			));
		return $event;
	}

	protected function getEventWithChildren()
	{
		$event = $this->getMockBuilder('Event')
			->disableOriginalConstructor()
			->getMock();
		$element_types = $this->getAllElementTypes();

		$event->expects( $this->any() )->method('getElements')->will($this->returnValue(
				array(
					$this->getElement($element_types[0]),
					$this->getElement($element_types[1])
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
		$episode = $this->getMockBuilder('Episode');
		$controller = $this->getExaminationController();
		$controller->episode = $episode;
		$this->assertEquals($episode, $controller->current_episode);
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
		$element_types = $this->getAllElementTypes();
		$elements = array(
			$this->getElement($element_types[0]),
			$this->getElement($element_types[1])
		);

		$controller->open_elements = $elements;

		$this->assertEquals(array($elements[1]), $controller->getChildElements($element_types[0]), 'Controller should return child element for parent.');
	}

	/**
	 * @covers BaseEventTypeController::getChildElements()
	 */
	public function testgetChildElements_parentTypeWithNoChild()
	{
		$controller = new _WrapperBaseEventTypeController();
		$element_types = $this->getAllElementTypes();
		$elements = array(
			$this->getElement($element_types[0]),
			$this->getElement($element_types[3])
		);

		$controller->open_elements = $elements;

		$this->assertEquals(array(), $controller->getChildElements($element_types[0]), 'Controller should return empty array for no children.');
	}

	/**
	 * @covers BaseEventTypeController::getChildElements()
	 */
	public function testgetChildElements_nonParentType()
	{

		$controller = new _WrapperBaseEventTypeController();
		$element_types = $this->getAllElementTypes();
		$elements = array(
			$this->getElement($element_types[0]),
			$this->getElement($element_types[3])
		);

		$controller->open_elements = $elements;

		$this->assertEquals(array(), $controller->getChildElements($element_types[3]), 'Controller should return empty array for non parent element type.');
	}

	/**
	 * @covers BaseEventTypeController::getOptionalElements()
	 */
	public function testgetOptionalElements_creatingEvent()
	{
		$controller = $this->getExaminationController();

		$controller->event_type = $this->getEventTypeWithAllElementTypes();
		$controller->setOpenElementsFromCurrentEvent('create');
		$optional = $controller->getOptionalElements();

		$this->assertEquals('visualfunction', $optional[0]->getElementTypeName(), 'First optional element should be Visual function.');
		$this->assertEquals('va', $optional[1]->getElementTypeName(), 'Second optional element should be Visual acuity.');
	}

	/**
	 * @covers BaseEventTypeController::getOptionalElements()
	 */
	public function testgetOptionalElements_updatingEvent()
	{
		$controller = $this->getExaminationController();

		$controller->event_type = $this->getEventTypeWithAllElementTypes();
		$controller->open_elements = array(
			$this->getElement($this->getElementType('HistoryElementType','history')),
			$this->getElement($this->getElementType('VisualFunctionElementType','visualfunction'))
		);

		$optional = $controller->getOptionalElements();
		$optional_names = array();
		foreach ($optional as $opt) {
			$optional_names[] = $opt->getElementTypeName();
		}

		$this->assertEquals(array('va'), $optional_names, 'Should only be one optional element (pasthistory is a child element)');
	}

	/**
	 * @covers BaseEventTypeController::getChildOptionalElements()
	 * @todo complete this test
	 */
	public function testgetChildOptionalElements_nonParentElement()
	{
		$controller = $this->getExaminationController();
		$controller->event_type = $this->getEventTypeWithAllElementTypes();
		$controller->setOpenElementsFromCurrentEvent('create');
		$this->markTestIncomplete('Not had time to define this test yet.');
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
