<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/*
 * This controller class provides functionality to load elements via ajax, and supports the parent/child relationship between event elements to allow them to be displayed in a nested format.
 * 
 * It also adheres to a more dyanmic convention for view files. it will look for the $action_[elementclassname] files, but if they do not exist, it will render using the form_[elementclassname] files 
 * (i.e. allowing the same form to be used for create and update). 
 */

class NestedElementsEventTypeController extends BaseEventTypeController {
	
	protected function beforeAction($action)
	{
		$res = parent::beforeAction($action);
		
		if (!Yii::app()->getRequest()->getIsAjaxRequest()) {
			Yii::app()->getClientScript()->registerScript('nestedElementJS', 'var moduleName = "' . $this->getModule()->name . '";', CClientScript::POS_HEAD);
			Yii::app()->getClientScript()->registerCssFile(Yii::app()->createUrl('css/nested_elements.css'));
			Yii::app()->getClientScript()->registerScriptFile(Yii::app()->createUrl('js/nested_elements.js'));
		}
		
		return $res;
	}
	
	/*
	 * Ajax method for loading an individual element (and its children)
	 */
	public function actionElementForm($id, $patient_id) {
		// first prevent invalid requests
		$element_type = ElementType::model()->findByPk($id);
		if(!$element_type) {
			throw new CHttpException(404, 'Unknown ElementType');
		}
		$patient = Patient::model()->findByPk($patient_id);
		if(!$patient) {
			throw new CHttpException(404, 'Unknown Patient');
		}
		$this->patient = $patient;
		$session = Yii::app()->session;
		$firm = Firm::model()->findByPk($session['selected_firm_id']);
		$this->episode = $this->getEpisode($firm, $this->patient->id);
		$element = new $element_type->class_name;
		$element->setDefaultOptions();
		$form = Yii::app()->getWidgetFactory()->createWidget($this,'BaseEventTypeCActiveForm',array(
				'id' => 'clinical-create',
				'enableAjaxValidation' => false,
				'htmlOptions' => array('class' => 'sliding'),
		));
		// Render called with processOutput
		// TODO: Check that scripts aren't being double loaded
		try {
			// look for element specific view file
			$this->renderPartial('create_' . $element->create_view, array(
				'element' => $element,
				'data' => null,
				'form' => $form,
			), false, true);
		}
		catch (Exception $e) {
			if (strpos($e->getMessage(), "cannot find the requested view") === false) {
				// it's a different, unexpected problem
				throw $e;
			}
			// use the default view file
			$this->renderPartial('_form', array(
				'element' => $element,
				'data' => null,
				'form' => $form,
			), false, true);
		}
	}
	
	/*
	 * returns the default elements to be displayed - ignoring elements which have parents (child elements)
	 *
	 * @see BaseEventTypeController::getDefaultElements()
	 */
	public function getDefaultElements($action, $event_type_id = false, $event = false) {
		if(!$event && isset($this->event)) {
			$event = $this->event;
		}
		if ($event && !$event_type_id) {
			$event_type_id = $event->eventType->id;
		}
		
		if (!$event_type_id) {
			$event_type_id = EventType::model()->find('class_name = ?',array($this->getModule()->name))->id;
		}
			
		$elements = array();
		
		if(empty($_POST)) {
			// it's a fresh render
			if(isset($event->event_type_id)) {
				$element_types = ElementType::model()->findAll(array(
						'condition' => 'event_type_id = :id AND parent_element_type_id is NULL',
						'order' => 'display_order',
						'params' => array(':id' => $event_type_id),
				));
				foreach($element_types as $element_type) {
					$element_class = $element_type->class_name;
					if($element = $element_class::model()->find('event_id = ?', array($event->id))) {
						$elements[] = $element;
					}
				}
			} else {
				$elements = $this->getCleanDefaultElements($event_type_id, $event);
			}
		} else {
			// work out the required elements based on the POST keys, and then create elements from this.
			foreach($_POST as $key => $value) {
				if(preg_match('/^Element|^OEElement/', $key)) {
					if($element_type = ElementType::model()->find('class_name = ? AND parent_element_type_id is NULL',array($key))) {
						$element_class = $element_type->class_name;
						if(!isset($event->event_type_id) || !($element = $element_class::model()->find('event_id = ?',array($event->id)))) {
							$element= new $element_class;
						}
						$element->attributes = $_POST[$key];
						$elements[] = $element;
					}
				}
			}
		}
		return $elements;
	}
	
	/*
	 * gets the child elements to be displayed in full for the provided parent element class
	 */
	public function getChildDefaultElements($parent_class, $action, $event_type_id = false, $event = false) {
		// determine current status to allow us to get existing child elements if appropriate
		if (!$event && isset($this->event)) {
			$event = $this->event;
		}
		if ($event && !$event_type_id) {
			$event_type_id = $event->eventType->id;
		}
		
		if (!$event_type_id) {
			$event_type_id = EventType::model()->find('class_name = ?',array($this->getModule()->name))->id;
		}
		
		if ($event) {
			$parent = ElementType::model()->find( array('condition' => 'class_name = :name and event_type_id = :eid', 'params' => array(':name'=>$parent_class, ':eid' => $event->event_type_id)) );
		} else {
			$parent = ElementType::model()->find( array('condition' => 'class_name = :name', 'params' => array(':name'=>$parent_class)) );
		}
	
		$elements = array();
		if (empty($_POST)) {
			// fresh render
			if (isset($event->event_type_id)) {
				// this must be an edit request, so the children are those elements that have been defined for this event.
				$element_types = ElementType::model()->findAll(array(
						'condition' => 'parent_element_type_id = :id',
						'order'     => 'display_order',
						'params'    => array(':id' => $parent->id),
				));
				foreach($element_types as $element_type) {
					$element_class = $element_type->class_name;
					if ($element = $element_class::model()->find('event_id = ?', array($event->id))) {
						$elements[] = $element;
					}
				}
			} else {
				// just get the configured child elements for this parent
				$elements = $this->getCleanChildDefaultElements($parent, $event_type_id);
			}
		} else {
			// define and populate according to the POST keys
			foreach($_POST as $key => $value) {
				if(preg_match('/^Element|^OEElement/', $key)) {
					if($element_type = ElementType::model()->find('class_name = ? AND parent_element_type_id = ? ',array($key, $parent->id))) {
						$element_class = $element_type->class_name;
						if(!isset($event->event_type_id) || !($element = $element_class::model()->find('event_id = ?',array($event->id)))) {
							$element= new $element_class;
							$element->attributes = $_POST[$key];
						}
						$elements[] = $element;
					}
				}
			}
		}
		return $elements;
	}
	
	/*
	 * return the standard set of elements for the event
	 * (note this is abstracted to allow override for event types that allow configurable clean sets of elements
	 */
	
	protected function getCleanDefaultElements($event_type_id) {
		$criteria = new CDbCriteria;
		$criteria->compare('event_type_id',$event_type_id);
		$criteria->order = 'display_order asc';
		$criteria->compare('`default`',1);
		
		$elements = array();
		foreach (ElementType::model()->findAll($criteria) as $element_type) {
			if (!$element_type->parent_element_type_id) {
				$elements[] = new $element_type->class_name;
			}
		}
		
		return $elements;
	}
	

	
	/*
	 * returns the child elements of the provided parent element that are in the set for the current subspecialty etc
	 * 
	 * @param Element $parent
	 * @param Episode $episode
	 * 
	 */
	protected function getCleanChildDefaultElements($parent, $event_type_id) {
		$criteria = new CDbCriteria;
		$criteria->compare('event_type_id',$event_type_id);
		$criteria->order = 'display_order asc';
		$criteria->compare('parent_element_type_id', $parent->id);
		$criteria->compare('`default`',1);
		
		$elements = array();
		foreach(ElementType::model()->findAll($criteria) as $element_type) {
			$elements[] = new $element_type->class_name;
		}
		return $elements;
	}
	
	/**
	 * returns the elements that are optional that are not child elements
	 *
	 * @see BaseEventTypeController::getOptionalElements()
	 */
	public function getOptionalElements($action) {
		$elements = array();
		$default_element_types = array();
		foreach($this->getDefaultElements($action) as $default_element) {
			$default_element_types[] = get_class($default_element);
		}
		$element_types = ElementType::model()->findAll(array(
				'condition' => 'event_type_id = :id AND parent_element_type_id is NULL',
				'order' => 'display_order',
				'params' => array(':id' => $this->event_type->id),
		));
		foreach($element_types as $element_type) {
			$element_class = $element_type->class_name;
			if(!in_array($element_class, $default_element_types)) {
				$elements[] = new $element_class;
			}
		}
		return $elements;
	}
	
	/*
	 * returns the optional child elements
	 */
	public function getChildOptionalElements($parent_class, $action) {
		$elements = array();
		$default_element_types = array();
		foreach($this->getChildDefaultElements($parent_class, $action) as $default_element) {
			$default_element_types[] = get_class($default_element);
		}
		if ($event = $this->event) {
			$parent = ElementType::model()->find( array('condition' => 'class_name = :name and event_type_id = :eid', 'params' => array(':name'=>$parent_class, ':eid' => $event->event_type_id)) );
		} else {
			$parent = ElementType::model()->find( array('condition' => 'class_name = :name', 'params' => array(':name'=>$parent_class)) );
		}
	
		$element_types = ElementType::model()->findAll(array(
				'condition' => 'parent_element_type_id = :id',
				'order' => 'display_order',
				'params' => array(':id' => $parent->id),
		));
		foreach($element_types as $element_type) {
			$element_class = $element_type->class_name;
			if(!in_array($element_class, $default_element_types)) {
				$elements[] = new $element_class;
			}
		}
		return $elements;
	}
	
	/**
	 * @see BaseEventTypeController::renderDefaultElements()
	 */
	public function renderDefaultElements($action, $form = false, $data = false) {
		foreach ($this->getDefaultElements($action) as $element) {
			if(empty($_POST)) {
				if ($action == 'create') {
					$element->setDefaultOptions();
				} else if($action == 'update') {
					$element->setUpdateOptions();
				}
			}
			try {
				// look for an action/element specific view file
				$this->renderPartial(
						$action . '_' . $element->{$action.'_view'},
						array('element' => $element, 'data' => $data, 'form' => $form)
				);
			}
			catch (Exception $e) {
				if (strpos($e->getMessage(), "cannot find the requested view") === false) {
					throw $e;
				}
				// otherwise use the default layout
				$this->renderPartial(
						'_form',
						array('element' => $element, 'data' => $data, 'form' => $form)
				);
			}
				
		}
	}
	
	/*
	 * render the default child elements for the given parent element
	*/
	public function renderChildDefaultElements($parent, $action, $form = false, $data = false ) {
		foreach ($this->getChildDefaultElements(get_class($parent), $action) as $child ) {
			if ($action == 'create' && empty($_POST)) {
				$child->setDefaultOptions();
			}
			else if ($action == 'ElementForm') {
				// ensure we use a property that the child element can recognise
				$action = 'create';
			}
			try {
				$this->renderPartial(
						// look for elemenet specific view file
						$action . '_' . $child->{$action.'_view'},
						array('element' => $child, 'data' => $data, 'form' => $form, 'child' => true)
				);
			}
			catch (Exception $e) {
				if (strpos($e->getMessage(), "cannot find the requested view") === false) {
					throw $e;
				}
				// otherwise use the default view
				$this->renderPartial(
						'_form',
						array('element' => $child, 'data' => $data, 'form' => $form, 'child' => true)
				);
			}
		}
	}
	
	/**
	 * @see BaseEventTypeController::renderOptionalElements()
	 */
	public function renderOptionalElements($action, $form = false, $data = false) {
		foreach ($this->getOptionalElements($action) as $element) {
			$this->renderPartial(
					'_optional_element',
					array('element' => $element, 'data' => $data, 'form' => $form)
			);
		}
	}
	
	/**
	 * render the optional child elements for the given parent
	 *
	 */
	public function renderChildOptionalElements($parent, $action, $form = false, $data = false) {
		foreach ($this->getChildOptionalElements(get_class($parent), $action) as $element) {
			$this->renderPartial(
					'_optional_element',
					array('element' => $element, 'data' => $data, 'form' => $form)
			);
				
		}
	}
	
}