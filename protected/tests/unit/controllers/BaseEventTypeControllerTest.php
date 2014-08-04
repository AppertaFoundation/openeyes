<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2013-2014
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

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'BaseEventTypeControllerTestNS.php';

/**
 * Class BaseEventTypeControllerTest
 * @group controllers
 */
class BaseEventTypeControllerTest extends PHPUnit_Framework_TestCase
{

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
	protected function getElementType($class_name, $name, $params = array(), $mock_element = null) {
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
			->getMock();

		//ComponentStubGenerator::propertiesSetAndMatch($e);

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

		// overriding relations to define the proper parent/child relations
		foreach ($element_types as $et) {
			// set child arrays as no db look up
			if ($et->name == 'history') {
				$et->child_element_types = array($element_types[1]);
			}
			else {
				$et->child_element_types = array();
			}

			if ($et->name == 'pasthistory') {
				$et->expects( $this->any() )->method('isChild')->will($this->returnValue(true));
				$et->parent_element_type = $element_types[0];
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
		$event_type = $this->getMockBuilder('EventType')
			->disableOriginalConstructor()
			->setMethods(array('getAllElementTypes'))
			->getMock();

		$ets = $this->getAllElementTypes();

		$event_type->expects($this->once())
			->method('getAllElementTypes')
			->will($this->returnValue($ets));

		$controller = $this->getMockBuilder('BaseEventTypeController')
			->disableOriginalConstructor()
			->setMethods(array('getEvent_Type'))
			->getMock();

		$controller->expects($this->any())
			->method('getEvent_Type')
			->will($this->returnValue($event_type));

		$r = new ReflectionClass('BaseEventTypeController');
		$oe_prop = $r->getProperty('open_elements');
		$oe_prop->setAccessible(true);
		$oe_prop->setValue($controller, array($ets[0]->getInstance()));


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
	 */
	public function testgetChildOptionalElements_nonParentElement()
	{
		$controller = $this->getExaminationController();
		$controller->event_type = $this->getEventTypeWithAllElementTypes();
		$controller->setOpenElementsFromCurrentEvent('create');

		$element_types = $this->getAllElementTypes();

		$optional = $controller->getChildOptionalElements($element_types[3]);
		$this->assertCount(0, $optional, 'Non-parent element should return empty array of optional children');
	}

	/**
	 * @covers BaseEventTypeController::beforeAction()
	 */
	public function testbeforeAction()
	{
		$controller = $this->getMockBuilder('_WrapperBaseEventTypeController')
				->setConstructorArgs(array('_WrapperBaseEventTypeController',new BaseEventTypeModule('ExaminationEvent',null)))
				->setMethods(array('setFirmFromSession','initAction', 'verifyActionAccess', 'registerAssets', 'setupAssetManager'))
				->getMock();
		$controller->expects($this->once())->method('setFirmFromSession');
		$controller->expects($this->once())->method('initAction');
		$controller->expects($this->once())->method('verifyActionAccess');
		$controller->expects($this->once())->method('registerAssets');
		$controller->expects($this->once())->method('setupAssetManager');

		$action = new CInlineAction($controller,'create');
		$controller->action = $action;
		$controller->firm = true;
		$controller->event_type = new EventType();
		$controller->beforeAction($action);
	}

	/**
	 * @covers BaseEventTypeController::redirectToPatientEpisodes()
	 *
	 */
	public function testredirectToPatientEpisodes()
	{
		$controller = $this->getMockBuilder('_WrapperBaseEventTypeController')
				->setConstructorArgs(array('_WrapperBaseEventTypeController',new BaseEventTypeModule('ExaminationEvent',null)))
				->setMethods(array('redirect'))
				->getMock();

		// nulling getId allows us to set the property
		$patient = $this->getMockBuilder('Patient')
				->disableOriginalConstructor()
				->setMethods(array('getId'))
				->getMock();

		$patient->id = 2;
		$controller->patient = $patient;

		// checking full url seems a little dirty, but I don't want to dwell too much on this method for now
		$controller->expects($this->once())
				->method('redirect')
				->with($this->equalTo(array("/patient/episodes/2")));

		$controller->redirectToPatientEpisodes();
	}

	/**
	 * @covers BaseEventTypeController::setElementDefaultOptions()
	 */
	public function testsetElementDefaultOptions_create()
	{
		$controller = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->getMock();

		$cls = new ReflectionClass('BaseEventTypeController');
		$method = $cls->getMethod('setElementDefaultOptions');
		$method->setAccessible(true);

		$el = $this->getMockBuilder('BaseEventTypeElement')
				->disableOriginalConstructor()
				->setMockClassName('SampleElement')
				->setMethods(array('setDefaultOptions'))
				->getMock();

		$el->expects($this->once())->method('setDefaultOptions');

		$method->invoke($controller, $el, 'create');
	}

	/**
	 * @covers BaseEventTypeController::setElementDefaultOptions()
	 */
	public function testsetElementDefaultOptions_createCustomControllerMethod()
	{
		$controller = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->setMethods(array('setElementDefaultOptions_SampleElement'))
				->getMock();

		$cls = new ReflectionClass('BaseEventTypeController');
		$method = $cls->getMethod('setElementDefaultOptions');
		$method->setAccessible(true);

		$el = $this->getMockBuilder('BaseEventTypeElement')
				->disableOriginalConstructor()
				->setMockClassName('SampleElement')
				->setMethods(array('setDefaultOptions'))
				->getMock();

		$el->expects($this->once())->method('setDefaultOptions');
		$controller->expects($this->once())
				->method('setElementDefaultOptions_SampleElement')
				->with($this->identicalTo($el), $this->identicalTo('create'));

		$method->invoke($controller, $el, 'create');
	}

	/**
	 * @covers BaseEventTypeController::setElementDefaultOptions()
	 */
	public function testsetElementDefaultOptions_createCustomControllerMethodNS()
	{
		$controller = $this->getMockBuilder('BaseEventTypeControllerTestNS\TestNSController')
				->disableOriginalConstructor()
				->setMethods(array('setElementDefaultOptions_TestNamespacedElement'))
				->getMock();

		$cls = new ReflectionClass('BaseEventTypeControllerTestNS\TestNSController');
		$method = $cls->getMethod('setElementDefaultOptions');
		$method->setAccessible(true);

		$el = $this->getMockBuilder('BaseEventTypeControllerTestNS\models\NamespacedElement')
			->disableOriginalConstructor()
			->setMockClassName('TestNamespacedElement')
			->setMethods(array('setDefaultOptions'))
			->getMock();

		$el->expects($this->once())->method('setDefaultOptions');
		$controller->expects($this->once())
				->method('setElementDefaultOptions_TestNamespacedElement')
				->with($this->identicalTo($el), $this->identicalTo('create'));

		$method->invoke($controller, $el, 'create');
	}

	public function testsetElementOptions()
	{
		$controller = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->setMethods(array('setElementDefaultOptions'))
				->getMock();

		$cls = new ReflectionClass('BaseEventTypeController');
		$open_elements = $cls->getProperty('open_elements');
		$open_elements->setAccessible(true);
		$method = $cls->getMethod('setElementOptions');
		$method->setAccessible(true);

		$ets = $this->getAllElementTypes();
		$e1 = $ets[0]->getInstance();
		$e2 = $ets[1]->getInstance();

		$open_elements->setValue($controller, array($e1, $e2));
		$controller->expects($this->at(0))
				->method('setElementDefaultOptions')
				->with($this->identicalTo($e1), $this->identicalTo('create'));
		$controller->expects($this->at(1))
				->method('setElementDefaultOptions')
				->with($this->identicalTo($e2), $this->identicalTo('create'));
		$method->invoke($controller,'create');
	}

	/**
	 * @covers BaseEventTypeController::hasPrevious()
	 */
	public function testhasPrevious_true()
	{
		$episode = $this->getMockBuilder('Episode')
				->disableOriginalConstructor()
				->setMethods(array('getElementsOfType'))
				->getMock();

		$et = $this->getElementType('HistoryElementType','history');
		$controller = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->setMethods(null)
				->getMock();

		$cls = new ReflectionClass('BaseEventTypeController');
		$ep_prop = $cls->getProperty('episode');
		$ep_prop->setAccessible(true);
		$ep_prop->setValue($controller, $episode);

		$episode->expects($this->once())
			->method('getElementsOfType')
			->with($this->identicalTo($et))
			->will($this->returnValue(array($et->getInstance())));

		$this->assertTrue($controller->hasPrevious($et));
	}

	/**
	 * @covers BaseEventTypeController::hasPrevious()
	 */
	public function testhasPrevious_exclude_false()
	{
		$episode = $this->getMockBuilder('Episode')
				->disableOriginalConstructor()
				->setMethods(array('getElementsOfType'))
				->getMock();

		$et = $this->getElementType('HistoryElementType','history');
		$controller = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->setMethods(null)
				->getMock();

		$cls = new ReflectionClass('BaseEventTypeController');
		$ep_prop = $cls->getProperty('episode');
		$ep_prop->setAccessible(true);
		$ep_prop->setValue($controller, $episode);

		$episode->expects($this->once())
				->method('getElementsOfType')
				->with($this->identicalTo($et), $this->identicalTo(4))
				->will($this->returnValue(array()));

		$this->assertFalse($controller->hasPrevious($et, 4));
	}

	/**
	 * @covers BaseEventTypeController::canCopy()
	 */
	public function testcanCopy_true()
	{
		$controller = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->setMethods(array('hasPrevious'))
				->getMock();
		$controller->expects($this->once())
			->method('hasPrevious')
			->will($this->returnValue(true));

		$element = ComponentStubGenerator::generate('BaseEventTypeElement',
				array('event_id' => 1));

		$element->expects($this->once())
			->method('canCopy')
			->will($this->returnValue(true));
		$element->expects($this->once())
				->method('getElementType')
				->will($this->returnValue(null));

		$this->assertTrue($controller->canCopy($element));
	}

	/**
	 * @covers BaseEventTypeController::canCopy()
	 */
	public function testcanCopy_false()
	{
		$controller = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->setMethods(null)
				->getMock();

		$element = $this->getMockBuilder('BaseEventTypeElement')
				->disableOriginalConstructor()
				->setMethods(array('canCopy'))
				->getMock();

		$element->expects($this->once())
				->method('canCopy')
				->will($this->returnValue(false));

		$this->assertFalse($controller->canCopy($element));
	}

	public function testcanViewPrevious_true()
	{
		$controller = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->setMethods(array('hasPrevious'))
				->getMock();

		$et = $this->getElementType('HistoryElementType','history');
		$e = $et->getInstance();
		ComponentStubGenerator::propertiesSetAndMatch($e, array('event_id' => 1), true);
		$e->expects($this->once())
			->method('canViewPrevious')
			->will($this->returnValue(true));

		$controller->expects($this->once())
			->method('hasPrevious')
			->with($this->identicalTo($et), $this->identicalTo(1))
			->will($this->returnValue(true));

		$this->assertTrue($controller->canViewPrevious($e));

	}

	public function testcanViewPrevious_false()
	{
		$controller = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->setMethods(array('hasPrevious'))
				->getMock();

		$et = $this->getElementType('HistoryElementType','history');
		$e = $et->getInstance();
		ComponentStubGenerator::propertiesSetAndMatch($e, array('event_id' => 1), true);
		$e->expects($this->once())
				->method('canViewPrevious')
				->will($this->returnValue(true));

		$controller->expects($this->once())
				->method('hasPrevious')
				->with($this->identicalTo($et), $this->identicalTo(1))
				->will($this->returnValue(false));

		$this->assertFalse($controller->canViewPrevious($e));
	}

	public function testgetControllerPrefix()
	{
		$test = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->setMethods(array())
				->setMockClassName('ADifferentNameForTheController')
				->getMock();

		$r = new ReflectionClass($test);
		$m = $r->getMethod('getControllerPrefix');
		$m->setAccessible(true);

		$this->assertEquals('adifferentnameforthe', $m->invoke($test));
	}

	public function testgetControllerPrefix_NS()
	{
		$test = $this->getMockBuilder('BaseEventTypeControllerTestNS\TestNSController')
				->disableOriginalConstructor()
				->setMethods(array())
				->setMockClassName('ANamespacedController')
				->getMock();

		$r = new ReflectionClass($test);
		$m = $r->getMethod('getControllerPrefix');
		$m->setAccessible(true);

		$this->assertEquals('anamespaced', $m->invoke($test));
	}

	public function testgetElementModulePathAlias()
	{
		// non namespaced should just return the modulePathAlias for the controller
		$test = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->setMethods(null)
				->getMock();

		$test->modulePathAlias = 'A Test Alias';
		$el = $this->getMockBuilder('BaseEventTypeElement')
				->disableOriginalConstructor()
				->getMock();

		$this->assertEquals('A Test Alias', $test->getElementModulePathAlias($el));
	}

	public function testgetElementModulePathAlias_NS()
	{
		$test = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->setMethods(null)
				->getMock();

		$test->modulePathAlias = 'A Test Alias';
		$el = new BaseEventTypeControllerTestNS\models\NamespacedElement();

		$this->assertEquals('BaseEventTypeControllerTestNS', $test->getElementModulePathAlias($el));
	}

	public function testgetAssetPathForElement_same()
	{
		$test = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->setMethods(array('getElementModulePathAlias'))
				->getMock();
		$test->assetPath = "Test Asset Path";

		$test->expects($this->once())
			->method('getElementModulePathAlias')
			->will($this->returnValue(false));

		$el = new BaseEventTypeControllerTestNS\models\NamespacedElement();

		$this->assertEquals("Test Asset Path", $test->getAssetPathForElement($el));
	}

	public function testgetAssetPathForElement_different()
	{
		Yii::app()->assetManager->setBasePath(Yii::getPathOfAlias('application.tests.assets'));

		$test = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->setMethods(array('getElementModulePathAlias'))
				->getMock();
		$test->assetPath = "Test Asset Path";

		$test->expects($this->once())
				->method('getElementModulePathAlias')
				->will($this->returnValue("AssetManagerPath"));

		$el = new BaseEventTypeControllerTestNS\models\NamespacedElement();

		$this->assertEquals(Yii::app()->assetManager->getPublishedPathofAlias('AssetManagerPath.alias'), $test->getAssetPathForElement($el));
	}

	public function testgetElementViewPathAlias_null()
	{
		$test = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->setMethods(array('getElementModulePathAlias'))
				->getMock();

		$r = new ReflectionClass($test);
		$m = $r->getMethod('getElementViewPathAlias');
		$m->setAccessible(true);

		$el = $this->getMockBuilder('BaseEventTypeElement')
				->disableOriginalConstructor()
				->getMock();

		$test->expects($this->once())
				->method('getElementModulePathAlias')
				->with($this->identicalTo($el))
				->will($this->returnValue(null));

		$this->assertEquals('', $m->invokeArgs($test, array($el)));
	}

	public function testgetElementViewPathAlias_value()
	{
		$test = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->setMethods(array('getElementModulePathAlias','getControllerPrefix'))
				->getMock();

		$r = new ReflectionClass($test);
		$m = $r->getMethod('getElementViewPathAlias');
		$m->setAccessible(true);

		$el = $this->getMockBuilder('BaseEventTypeElement')
				->disableOriginalConstructor()
				->getMock();

		$test->expects($this->once())
				->method('getElementModulePathAlias')
				->with($this->identicalTo($el))
				->will($this->returnValue("value"));

		$test->expects($this->once())
			->method('getControllerPrefix')
			->will($this->returnValue('prefix'));

		$this->assertEquals('value.views.prefix.', $m->invokeArgs($test, array($el)));
	}

	public function testrenderPartial()
	{
		$test_view_str = 'simpleTestView';
		$test = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->setMethods(array('getViewFile','renderFile'))
				->getMock();

		$test->expects($this->any())
			->method('getViewFile')
			->with($this->equalTo($test_view_str))
			->will($this->returnValue('found/file'));

		$test->expects($this->once())
			->method('renderFile')
			->with($this->equalTo('found/file'))
			->will($this->returnValue('test output'));

		$this->assertEquals('test output', $test->renderPartial($test_view_str, null, true));

	}
	public function testrenderPartial_parent()
	{
		$test_view_str = 'aTestView';
		$test = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->setMethods(array('getViewFile','getModule', 'getControllerPrefix','renderFile'))
				->getMock();

		$module = $this->getMockBuilder('BaseEventTypeModule')
				->disableOriginalConstructor()
				->setMethods(array('getModuleInheritanceList'))
				->getMock();

		$p_module = $this->getMockBuilder('BaseEventTypeModule')
				->disableOriginalConstructor()
				->setMethods(null)
				->getMock();

		$p_module->setId('ParentModule');

		$module->expects($this->once())
			->method('getModuleInheritanceList')
			->will($this->returnValue(array($p_module)));

		$test->expects($this->at(0))
				->method('getViewFile')
				->will($this->returnValue(false));

		$test->expects($this->once())
			->method('getModule')
			->will($this->returnValue($module));
		$test->expects($this->once())
			->method('getControllerPrefix')
			->will($this->returnValue('prefix'));

		$test->expects($this->at(3))
			->method('getViewFile')
			->with('ParentModule.views.prefix.'.$test_view_str)
			->will($this->returnValue('a/fake/file'));

		$test->expects($this->at(5))
			->method('getViewFile')
			->will($this->returnValue(true));

		$test->expects($this->once())
			->method('renderFile')
			->will($this->returnValue('successful output'));

		$this->assertEquals('successful output', $test->renderPartial($test_view_str, null, true));
	}

	public function testrenderOptionalElement()
	{
		$test = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->setMethods(array('getElementViewPathAlias', 'getViewFile', 'renderPartial'))
				->getMock();

		$r = new ReflectionClass($test);
		$m = $r->getMethod('renderOptionalElement');
		$m->setAccessible(true);

		$el = $this->getMockBuilder('BaseEventTypeElement')
				->disableOriginalConstructor()
				->setMethods(array('getDefaultView'))
				->getMock();
		$el->expects($this->once())
			->method('getDefaultView')
			->will($this->returnValue('default_view'));

		$test->expects($this->once())
			->method('getElementViewPathAlias')
			->with($this->identicalTo($el))
			->will($this->returnValue('el.view.alias'));
		$test->expects($this->any())
			->method('getViewFile')
			->with($this->equalTo('el.view.alias_optional_default_view'))
			->will($this->returnValue('view/file'));
		$test->expects($this->once())
			->method('renderPartial')
			->with($this->equalTo('el.view.alias_optional_default_view'));

		$m->invokeArgs($test, array($el, 'create', null, null));

	}

	public function testrenderElement()
	{
		$this->markTestIncomplete('To test');
	}

	public function testinitActionCreate()
	{
		$controller = $this->getMockBuilder('BaseEventTypeController')
				->disableOriginalConstructor()
				->setMethods(array('setPatient','getEpisode', 'getEvent_Type'))
				->getMock();

		$event_type = ComponentStubGenerator::generate('EventType', array('id' => 12));

		$_REQUEST['patient_id'] = 126;
		$episode = ComponentStubGenerator::generate('Episode', array('id' => 453));

		$controller->expects($this->once())
			->method('setPatient')
			->with($this->equalTo($_REQUEST['patient_id']));

		$controller->expects($this->once())
				->method('getEvent_Type')
				->will($this->returnValue($event_type));

		$controller->expects($this->once())
			->method('getEpisode')
			->will($this->returnValue($episode));



		$r = new ReflectionClass('BaseEventTypeController');
		$iac_meth = $r->getMethod('initActionCreate');
		$iac_meth->setAccessible(true);

		$iac_meth->invoke($controller);

		$this->assertTrue($controller->event->isNewRecord);
		$this->assertSame($episode->id, $controller->event->episode_id);
		$this->assertEquals($event_type->id, $controller->event->event_type_id);
	}

}

/**
 * Class _WrapperBaseEventTypeController
 *
 * wrapper class around BaseEventTypeController to expose protected methods for testing
 * TODO: see if ReflectionClass can be used to expose these as much as possible
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
	public $action;
	// expose protected methods
	public function getEventElements() { return parent::getEventElements(); }
	public function getOpenElements() { return $this->open_elements; }
	public function setOpenElementsFromCurrentEvent($action) { parent::setOpenElementsFromCurrentEvent($action); }
	public function beforeAction($action) { parent::beforeAction($action); }
	public function redirectToPatientEpisodes() { parent::redirectToPatientEpisodes(); }

}
