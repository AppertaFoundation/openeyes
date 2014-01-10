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
 * Class BaseEventTypeController
 *
 * BaseEventTypeController is the base controller for modules managing events within OpenEyes.
 *
 * It implements a standardised design pattern to provide the general CRUD interface for module events. The controller
 * is designed to be stateful. When an action is called, the state of the controller is determined from the POST and GET
 * attributes of the request. Properties on the controller are populated through a series of methods, and the response
 * is rendered based on these values, and returned to the user. The rationale behind this is that each of the methods
 * provide discrete hooks which can be overridden in module controllers to redefine what the controller properties
 * should be set to.
 *
 * The primary property of the controller to be manipulated is the {@link open_elements} which defines the elements of
 * the event to be displayed in whatever action is being performed.
 *
 * An abstract class in all but name, it should be used for all event based modules. Specific methods can be implemented
 * in module level controllers that will be called automatically by this base controller. Specifically setting defaults
 * on elements and setting complex attributes on individual elements can be handled in specific methods, as defined by
 * <ul>
 * <li>{@link setElementDefaultOptions}</li>
 * <li>{@link setElementComplexAttributesFromData}</li>
 * <li>{@link saveElementComplexAttributesFromData}</li>
 * </ul>
 *
 * It's worth noting that at the moment there is no class for Events at the module level. As a result, the controller
 * tends to contain certain business logic that should really be part of the event. Such behaviour should be written in
 * a way that it can be easily extracted into a separate class. The intention in the future is that this would be abstracted
 * into (at a minimum) a helper class, or ideally into an actual event class that would contain all business logic for
 * manipulating the event and its elements.
 *
 * Furthermore no $_POST, $_GET or session data should be utilised within the element models. Data should be extracted
 * by controllers and passed to methods on the element models. In the future, models may be instantiated in different
 * context where these globals would not be available.
 *
 */
class BaseEventTypeController extends BaseModuleController
{
	const ACTION_TYPE_CREATE = 'Create';
	const ACTION_TYPE_VIEW = 'View';
	const ACTION_TYPE_PRINT = 'Print';
	const ACTION_TYPE_EDIT = 'Edit';
	const ACTION_TYPE_DELETE = 'Delete';
	const ACTION_TYPE_FORM = 'Form';  // AJAX actions that are used during create and update but don't actually modify data themselves

	static private $base_action_types = array(
		'create' => self::ACTION_TYPE_CREATE,
		'view' => self::ACTION_TYPE_VIEW,
		'elementForm' => self::ACTION_TYPE_FORM,
		'viewPreviousElements' => self::ACTION_TYPE_FORM,
		'print' => self::ACTION_TYPE_PRINT,
		'update' => self::ACTION_TYPE_EDIT,
		'delete' => self::ACTION_TYPE_DELETE,
	);

	/**
	 * Override for custom actions
	 *
	 * @var array
	 */
	static protected $action_types = array();

	/* @var Firm */
	public $firm;
	/* @var Patient */
	public $patient;
	/* @var Site */
	public $site;
	/* @var Event */
	public $event;
	public $editable = true;
	public $editing;
	private $title;
	public $episode;
	public $moduleStateCssClass = '';
	public $event_tabs = array();
	public $event_actions = array();
	public $successUri = 'default/view/';
	// String to set an issue when an event is created
	public $eventIssueCreate;
	// defines additional variables to be available in view templates
	public $extraViewProperties = array();
	public $layout = '//layouts/events_and_episodes';
	private $action_type_map;
	private $episodes = array();
	public $renderPatientPanel = true;

	protected $open_elements;

	public function getTitle()
	{
		if(isset($this->title)){
			return $this->title;
		}
		if(isset($this->event_type)){
			return $this->event_type->name;
		}
		return '';
	}

	public function setTitle($title)
	{
		$this->title=$title;
	}

	public function init()
	{
		$this->action_type_map = array();
		foreach (self::$base_action_types as $action => $type) {
			$this->action_type_map[strtolower($action)] = $type;
		}
		foreach (static::$action_types as $action => $type) {
			$this->action_type_map[strtolower($action)] = $type;
		}

		return parent::init();
	}

	public function accessRules()
	{
		// Allow logged in users - the main authorisation check happens later in verifyActionAccess
		return array(array('allow', 'users' => array('@')));
	}

	/**
	 * Wrapper around the episode property on this controller - current_episode is used in patient layouts
	 *
	 * @return Episode
	 */
	public function getCurrent_episode()
	{
		return $this->episode;
	}

	/**
	 * Return an ACTION_TYPE_ constant representing the type of an action for authorisation purposes
	 *
	 * @param string $action
	 * @throws Exception
	 * @return string
	 */
	public function getActionType($action)
	{
		if (!isset($this->action_type_map[strtolower($action)])) {
			throw new Exception("Action '{$action}' has no type associated with it");
		}
		return $this->action_type_map[strtolower($action)];
	}

	/**
	 * Abstraction of getting the elements for the event being controlled to allow more complex overrides (such as workflow)
	 * where required.
	 *
	 * This should be overridden if the standard elements for the event are affected by the controller state.
	 *
	 * @return BaseEventTypeElement[]
	 */
	protected function getEventElements()
	{
		if ($this->event) {
			return $this->event->getElements();
		}
		else {
			return $this->event_type->getDefaultElements();
		}
	}

	/**
	 * based on the current state of the controller, sets the open_elements property, which is the array of relevant
	 * open elements for the controller
	 */
	protected function setOpenElementsFromCurrentEvent($action)
	{
		$this->open_elements = $this->getEventElements();
		$this->setElementOptions($action);
	}

	/**
	 * Renders the metadata of the event with the standard template
	 * @param string $view
	 */
	public function renderEventMetadata($view='//patient/event_metadata')
	{
		$this->renderPartial($view);
	}
	/**
	 * Get the open elements for the event that are not children
	 *
	 * @return array
	 */
	public function getElements()
	{
		$elements = array();
		foreach ($this->open_elements as $element) {
			if (!$element->getElementType()->isChild()) {
				$elements[] = $element;
			}
		}
		return $elements;
	}

	/**
	 * Get the open child elements for the given ElementType
	 *
	 * @param ElementType $parent_type
	 * @return BaseEventTypeElement[] $open_elements
	 */
	public function getChildElements($parent_type)
	{
		$open_child_elements = array();
		foreach ($this->open_elements as $open) {
			$et = $open->getElementType();
			if ($et->isChild() && $et->parent_element_type->class_name == $parent_type->class_name) {
				$open_child_elements[] = $open;
			}
		}

		return $open_child_elements;
	}

	/**
	 * Get the optional elements for the current module's event type (that are not children)
	 *
	 * @return BaseEventTypeElement[] $elements
	 */
	public function getOptionalElements()
	{
		$open_et = array();
		foreach ($this->open_elements as $open) {
			$open_et[] = get_class($open);
		}

		$optional = array();
		foreach ($this->event_type->getAllElementTypes() as $element_type) {
			if (!in_array($element_type->class_name, $open_et) && !$element_type->isChild()) {
				$optional[] = $element_type->getInstance();
			}
		}
		return $optional;
	}

	/**
	 * Get the child optional elements for the given element type
	 *
	 * @param ElementType $parent_type
	 * @return BaseEventTypeElement[] $optional_elements
	 */
	public function getChildOptionalElements($parent_type)
	{
		$open_et = array();
		foreach ($this->open_elements as $open) {
			$et = $open->getElementType();
			if ($et->isChild() && $et->parent_element_type->class_name == $parent_type->class_name) {
				$open_et[] = $et->class_name;
			}
		}
		$optional = array();
		foreach ($parent_type->child_element_types as $child_type) {
			if (!in_array($child_type->class_name, $open_et)) {
				$optional[] = $child_type->getInstance();
			}
		}

		return $optional;
	}

	/**
	 * Override to use $action_types
	 *
	 * @param string $action
	 * @return boolean
	 */
	public function isPrintAction($action)
	{
		return self::getActionType($action) == self::ACTION_TYPE_PRINT;
	}

	/**
	 * Setup base css/js etc requirements for the eventual action render.
	 *
	 * @param $action
	 * @return bool
	 * @throws CHttpException
	 * @see parent::beforeAction($action)
	 */
	protected function beforeAction($action)
	{
		// Automatic file inclusion unless it's an ajax call
		if ($this->assetPath && !Yii::app()->getRequest()->getIsAjaxRequest()) {
			if (!$this->isPrintAction($action->id)) {
				// nested elements behaviour
				//TODO: possibly put this into standard js library for events
				Yii::app()->getClientScript()->registerScript('nestedElementJS', 'var moduleName = "' . $this->getModule()->name . '";', CClientScript::POS_HEAD);
				Yii::app()->getClientScript()->registerScriptFile(Yii::app()->createUrl('js/nested_elements.js'));
			}
		}

		$this->firm = Firm::model()->findByPk(Yii::app()->session->get('selected_firm_id'));

		if (!isset($this->firm)) {
			// No firm selected, reject
			throw new CHttpException(403, 'You are not authorised to view this page without selecting a firm.');
		}

		$this->initAction($action->id);

		$this->verifyActionAccess($action);

		return parent::beforeAction($action);
	}

	/**
	 * Redirect to the patient episodes when the controller determines the action cannot be carried out
	 */
	protected function redirectToPatientEpisodes()
	{
		$this->redirect(array("/patient/episodes/".$this->patient->id));
	}

	/**
	 * set the defaults on the given BaseEventTypeElement
	 *
	 * Looks for a methods based on the class name of the element:
	 * setElementDefaultOptions_[element class name]
	 *
	 * This method is passed the element and action, which allows for controller methods to manipulate the default
	 * values of the element (if the controller state is required for this)
	 *
	 * @param BaseEventTypeElement $element
	 * @param string $action
	 */
	protected function setElementDefaultOptions($element, $action)
	{
		if ($action == 'create') {
			$element->setDefaultOptions();
		} elseif ($action == 'update') {
			$element->setUpdateOptions();
		}
		$el_method = 'setElementDefaultOptions_' . get_class($element);
		if (method_exists($this, $el_method)) {
			$this->$el_method($element, $action);
		}
	}

	/**
	 * Set the default values on each of the open elements.
	 *
	 * @param string $action
	 */
	protected function setElementOptions($action)
	{
		foreach ($this->open_elements as $element) {
			$this->setElementDefaultOptions($element, $action);
		}
	}


	/**
	 * Are there one or more previous instances of an element?
	 * @param ElementType $element_type
	 * @param integer $exclude_event_id
	 * @return boolean
	 */
	public function hasPrevious($element_type, $exclude_event_id = null)
	{
		if ($episode = $this->episode) {
			return count($episode->getElementsOfType($element_type, $exclude_event_id)) > 0;
		} else {
			return false;
		}
	}

	/**
	 * Can an element can be copied from a previous version
	 *
	 * @param BaseEventTypeElement $element
	 * @return boolean
	 */
	public function canCopy($element)
	{
		return ($element->canCopy() && $this->hasPrevious($element->getElementType(), $element->event_id));
	}

	/**
	 * Can we view the previous version of the element
	 *
	 * @param BaseEventTypeElement $element
	 * @return boolean
	 */
	public function canViewPrevious($element)
	{
		return $this->hasPrevious($element->getElementType(), $element->event_id);
	}

	/**
	 * Initialise an element of $element_type for returning as an individual form. If the $previous_id is provided,
	 * then the default values of the element will be overridden with the properties of the previous intance of the
	 * element. Similarly, $additional allows specific values to be set on the element.
	 *
	 * Abstracted to allow overrides in specific module controllers
	 *
	 * @param ElementType $element_type
	 * @param integer $previous_id
	 * @param array() $additional - additional attributes for the element
	 * @return \BaseEventTypeElement
	 */
	protected function getElementForElementForm($element_type, $previous_id = 0, $additional)
	{
		$element_class = $element_type->class_name;
		$element = $element_type->getInstance();
		$this->setElementDefaultOptions($element, "create");

		if ($previous_id && $element->canCopy()) {
			$previous_element = $element_class::model()->findByPk($previous_id);
			$element->loadFromExisting($previous_element);
		}
		if ($additional) {
			foreach (array_keys($additional) as $add) {
				if ($element->isAttributeSafe($add)) {
					$element->$add = $additional[$add];
				}
			}
		}
		return $element;
	}

	/**
	 * Runs initialisation of the controller based on the action. Looks for a method name of
	 *
	 * initAction[$action]
	 *
	 * and calls it.
	 *
	 * @param string $action
	 */
	protected function initAction($action)
	{
		$init_method = "initAction" . ucfirst($action);
		if (method_exists($this, $init_method)) {
			$this->$init_method();
		}
	}

	/**
	 * Initialise the controller prior to a create action
	 *
	 * @throws CHttpException
	 */
	protected function initActionCreate()
	{
		$this->moduleStateCssClass = 'edit';

		if (!$this->patient = Patient::model()->findByPk($_REQUEST['patient_id'])) {
			throw new CHttpException(404, 'Invalid patient_id.');
		}

		if (!$this->episode = $this->getEpisode()) {
			$this->redirectToPatientEpisodes();
		}
	}

	/**
	 * Intialise controller property based off the event id
	 *
	 * @param $id
	 * @throws CHttpException
	 */
	protected function initWithEventId($id)
	{
		$criteria = new CDbCriteria();
		$criteria->addCondition('event_type_id = ?');
		$criteria->params = array($this->event_type->id);
		if (!$id || !$this->event = Event::model()->findByPk($id, $criteria)) {
			throw new CHttpException(404, 'Invalid event id.');
		}

		$this->patient = $this->event->episode->patient;
		$this->episode = $this->event->episode;
	}

	/**
	 * Sets the the css state
	 */
	protected function initActionView()
	{
		$this->moduleStateCssClass = 'view';

		$this->initWithEventId(@$_GET['id']);
	}

	/**
	 * initialise the controller prior to event update action
	 *
	 * @throws CHttpException
	 */
	protected function initActionUpdate()
	{
		$this->moduleStateCssClass = 'edit';

		$this->initWithEventId(@$_GET['id']);
	}

	/**
	 * initialise the controller with the event id
	 */
	protected function initActionDelete()
	{
		$this->initWithEventId(@$_GET['id']);
	}

	/**
	 * @param CAction $action
	 */
	protected function verifyActionAccess(CAction $action)
	{
		$actionType = $this->getActionType($action->id);
		$method = "check{$actionType}Access";

		if (!method_exists($this, $method)) {
			throw new Exception("No access check method found for action type '{$actionType}'");
		}

		if (!$this->$method()) {
			switch ($actionType) {
				case self::ACTION_TYPE_CREATE:
				case self::ACTION_TYPE_EDIT:
					$this->redirectToPatientEpisodes();
					break;
				default:
					throw new CHttpException(403);
			}
		}
	}

	/**
	 * @return boolean
	 */
	public function checkCreateAccess()
	{
		return $this->checkAccess('OprnCreateEvent', $this->firm, $this->episode, $this->event_type);
	}

	/**
	 * @return boolean
	 */
	public function checkViewAccess()
	{
		return $this->checkAccess('OprnViewClinical');
	}

	/**
	 * @return boolean
	 */
	public function checkPrintAccess()
	{
		return $this->checkAccess('OprnPrint');
	}

	/**
	 * @return boolean
	 */
	public function checkEditAccess()
	{
		return $this->checkAccess('OprnEditEvent', $this->firm, $this->event);
	}

	/**
	 * @return boolean
	 */
	public function checkDeleteAccess()
	{
		return $this->checkAccess('OprnDeleteEvent', Yii::app()->session['user'], $this->firm, $this->event);
	}

	/**
	 * @return boolean
	 */
	public function checkFormAccess()
	{
		return $this->checkAccess('OprnViewClinical');
	}

	/**
	 * Carries out the base create action
	 *
	 * @return bool|string
	 * @throws CHttpException
	 * @throws Exception
	 */
	public function actionCreate()
	{
		if (!empty($_POST)) {
			// form has been submitted
			if (isset($_POST['cancel'])) {
				$this->redirectToPatientEpisodes();
			}

			// set and validate
			$errors = $this->setAndValidateElementsFromData($_POST);

			// creation
			if (empty($errors)) {
				$transaction = Yii::app()->db->beginTransaction();

				try {
					$success = $this->saveEvent($_POST);

					if ($success) {
						//TODO: should this be in the save event as pass through?
						if ($this->eventIssueCreate) {
							$this->event->addIssue($this->eventIssueCreate);
						}
						//TODO: should not be passing event?
						$this->afterCreateElements($this->event);

						$this->logActivity('created event.');

						$audit_data = array('event' => $this->event->getAuditAttributes());

						//TODO: should this be simply handled by the audit wrapper of the event?
						foreach ($this->open_elements as $element) {
							$audit_data[get_class($element)] = $element->getAuditAttributes();
						}

						$this->event->audit('event','create',serialize($audit_data));

						Yii::app()->user->setFlash('success', "{$this->event_type->name} created.");

						$transaction->commit();

						$this->redirect(array($this->successUri.$this->event->id));
					}
					else {
						throw new Exception("could not save event");
					}

				}
				catch (Exception $e) {
					$transaction->rollback();
					throw $e;
				}
			}
		}
		else {
			$this->setOpenElementsFromCurrentEvent('create');
		}

		$this->editable = false;
		$this->event_tabs = array(
				array(
						'label' => 'Create',
						'active' => true,
				),
		);

		$cancel_url = ($this->episode) ? '/patient/episode/'.$this->episode->id : '/patient/episodes/'.$this->patient->id;
		$this->event_actions = array(
				EventAction::link('Cancel',
						Yii::app()->createUrl($cancel_url),
						array('level' => 'cancel')
				)
		);

		$this->render('create', array(
			'errors' => @$errors
		));
	}

	/**
	 * View the event specified by $id
	 *
	 * @param $id
	 * @throws CHttpException
	 */
	public function actionView($id)
	{
		$this->setOpenElementsFromCurrentEvent('view');
		// Decide whether to display the 'edit' button in the template
		if ($this->editable) {
			$this->editable = $this->checkEditAccess($this->event);
		}
		// Allow elements to override the editable status
		if ($this->editable) {
			foreach ($this->open_elements as $element) {
				if (!$element->isEditable()) {
					$this->editable = false;
					break;
				}
			}
		}

		$this->logActivity('viewed event');

		$this->event->audit('event','view',false);

		$this->event_tabs = array(
			array(
				'label' => 'View',
				'active' => true,
			)
		);
		if ($this->editable) {
			$this->event_tabs[] = array(
				'label' => 'Edit',
				'href' => Yii::app()->createUrl($this->event->eventType->class_name.'/default/update/'.$this->event->id),
			);
		}
		if ($this->checkDeleteAccess($this->event)) {
			$this->event_actions = array(
				EventAction::link('Delete',
					Yii::app()->createUrl($this->event->eventType->class_name.'/default/delete/'.$this->event->id),
					array('level' => 'delete')
				)
			);
		}

		$viewData = array_merge(array(
			'elements' => $this->open_elements,
			'eventId' => $id,
		), $this->extraViewProperties);

		$this->render('view', $viewData);
	}

	/**
	 * The update action for the given event id
	 *
	 * @param $id
	 * @throws CHttpException
	 * @throws SystemException
	 * @throws Exception
	 */
	public function actionUpdate($id)
	{
		if (!empty($_POST)) {
			// somethings been submitted
			if (isset($_POST['cancel'])) {
				// Cancel button pressed, so just bounce to view
				$this->redirect(array('default/view/'.$this->event->id));
			}

			$errors = $this->setAndValidateElementsFromData($_POST);

			// update the event
			if (empty($errors)) {
				$transaction = Yii::app()->db->beginTransaction();

				try {
					//TODO: should all the auditing be moved into the saving of the event
					$success = $this->saveEvent($_POST);

					if ($success) {
						//TODO: should not be pasing event?
						$this->afterUpdateElements($this->event);
						$this->logActivity('updated event');

						$audit_data = array('event' => $this->event->getAuditAttributes());

						foreach ($this->open_elements as $element) {
							$audit_data[get_class($element)] = $element->getAuditAttributes();
						}

						$this->event->audit('event','update',serialize($audit_data));

						$this->event->user = Yii::app()->user->id;

						if (!$this->event->save()) {
							throw new SystemException('Unable to update event: '.print_r($this->event->getErrors(),true));
						}

						OELog::log("Updated event {$this->event->id}");
						$transaction->commit();
						$this->redirect(array('default/view/'.$this->event->id));
					}
					else {
						throw new Exception("Unable to save edits to event");
					}
				}
				catch (Exception $e) {
					$transaction->rollback();
					throw $e;
				}
			}
		}
		else {
			// get the elements
			$this->setOpenElementsFromCurrentEvent('update');
		}

		$this->editing = true;
		$this->event_tabs = array(
				array(
						'label' => 'View',
						'href' => Yii::app()->createUrl($this->event->eventType->class_name.'/default/view/'.$this->event->id),
				),
				array(
						'label' => 'Edit',
						'active' => true,
				),
		);

		$this->event_actions = array(
				EventAction::link('Cancel',
						Yii::app()->createUrl($this->event->eventType->class_name.'/default/view/'.$this->event->id),
						array('level' => 'cancel')
				)
		);

		$this->render($this->action->id, array(
			'errors' => @$errors
		));
	}

	/**
	 * Ajax method for loading an individual element (and its children)
	 *
	 * @param integer $id
	 * @param integer $patient_id
	 * @param integer $previous_id
	 * @throws CHttpException
	 * @throws Exception
	 * @internal param int $import_previous
	 */
	public function actionElementForm($id, $patient_id, $previous_id = null)
	{
		// first prevent invalid requests
		$element_type = ElementType::model()->findByPk($id);
		if (!$element_type) {
			throw new CHttpException(404, 'Unknown ElementType');
		}
		$patient = Patient::model()->findByPk($patient_id);
		if (!$patient) {
			throw new CHttpException(404, 'Unknown Patient');
		}

		// Clear script requirements as all the base css and js will already be on the page
		Yii::app()->clientScript->reset();

		$this->patient = $patient;
		$session = Yii::app()->session;
		$this->firm = Firm::model()->findByPk($session['selected_firm_id']);

		$this->episode = $this->getEpisode();

		// allow additional parameters to be defined by module controllers
		// TODO: Should valid additional parameters be a property of the controller?
		$additional = array();
		foreach (array_keys($_GET) as $key) {
			if (!in_array($key, array('id', 'patient_id', 'previous_id'))) {
				$additional[$key] = $_GET[$key];
			}
		}

		// retrieve the element
		$element = $this->getElementForElementForm($element_type, $previous_id, $additional);
		$this->open_elements = array($element);

		$form = Yii::app()->getWidgetFactory()->createWidget($this,'BaseEventTypeCActiveForm',array(
				'id' => 'clinical-create',
				'enableAjaxValidation' => false,
				'htmlOptions' => array('class' => 'sliding'),
			));

		// Render called with processOutput
		// TODO: use renderElement for this if we can
		try {
			// look for element specific view file
			$this->renderPartial('create_' . $element->create_view, array(
					'element' => $element,
					'data' => null,
					'form' => $form,
					'child' => null,
					'previous_parent_id' => $previous_id,
				), false, true);
		} catch (Exception $e) {
			if (strpos($e->getMessage(), "cannot find the requested view") === false) {
				// it's a different, unexpected problem
				throw $e;
			}
			// use the default view file
			$this->renderPartial('_form', array(
					'element' => $element,
					'data' => null,
					'form' => $form,
					'child' => ($element_type->parent_element_type_id > 0),
					'previous_parent_id' => $previous_id,
				), false, true);
		}
	}

	/**
	 * Ajax method for viewing previous elements
	 * @param integer $element_type_id
	 * @param integer $patient_id
	 * @throws CHttpException
	 */
	public function actionViewPreviousElements($element_type_id, $patient_id)
	{
		$element_type = ElementType::model()->findByPk($element_type_id);
		if (!$element_type) {
			throw new CHttpException(404, 'Unknown ElementType');
		}
		$this->patient = Patient::model()->findByPk($patient_id);
		if (!$this->patient) {
			throw new CHttpException(404, 'Unknown Patient');
		}

		// Clear script requirements as all the base css and js will already be on the page
		Yii::app()->clientScript->reset();

		$this->episode = $this->getEpisode();

		$elements = $this->episode->getElementsOfType($element_type);

		$this->renderPartial('_previous', array(
			'elements' => $elements,
		), false, true // Process output to deal with script requirements
		);
	}

	/**
	 * Set the validation scenario for the element if necessary
	 *
	 * @param $element
	 */
	protected function setValidationScenarioForElement($element)
	{
		if ($child_types = $element->getElementType()->child_element_types) {
			$ct_cls_names = array();
			foreach ($child_types as $ct) {
				$ct_cls_names[] = $ct->class_name;
			}

			$has_children = false;
			foreach ($this->open_elements as $open) {
				$et = $open->getElementType();
				if ($et->isChild() && in_array($et->class_name, $ct_cls_names)) {
					$has_children = true;
					break;
				}
			}
			$element->scenario = $has_children ? "formHasChildren" : "formHasNoChildren";
		}
	}

	/**
	 * Looks for custom methods to set many to many data defined on elements. This is called prior to validation so should set values without actually
	 * touching the database.
	 *
	 * The $data attribute will typically be the $_POST structure, but can be any appropriately structured array
	 * The optional $index attribute is the counter for multiple elements of the same type that might exist in source data.
	 *
	 * The convention for the method name for the element setting is:
	 *
	 * setComplexAttributes_[element_class_name]($element, $data, $index)
	 *
	 * @param BaseEventTypeElement $element
	 * @param array $data
	 * @param integer $index
	 */
	protected function setElementComplexAttributesFromData($element, $data, $index = null)
	{
		$element_method = "setComplexAttributes_" . get_class($element);
		if (method_exists($this, $element_method)) {
			$this->$element_method($element, $data, $index);
		}
	}

	/**
	 * Set the attributes of the given $elements from the given structured array.
	 * Returns any validation errors that arise.
	 *
	 * @param array $data
	 * @throws Exception
	 * @return array $errors
	 */
	protected function setAndValidateElementsFromData($data)
	{
		$errors = array();
		$elements = array();

		// only process data for elements that are part of the element type set for the controller event type
		foreach ($this->event_type->getAllElementTypes() as $element_type) {
			$el_cls_name = $element_type->class_name;
			if (isset($data[$el_cls_name])) {
				$keys = array_keys($data[$el_cls_name]);

				if (is_array($data[$el_cls_name][$keys[0]])) {
					// there is more than one element of this type
					if ($this->event && !$data[$el_cls_name]['_element_id']) {
						throw new Exception("missing _element_id for multiple elements for editing an event");
					}

					// iterate through each to define the multiple instances we require
					for ($i=0; $i<count($data[$el_cls_name][$keys[0]]); $i++) {
						if ($el_id = $data[$el_cls_name]['_element_id'][$i]) {
							$element = $el_cls_name::model()->findByPk($el_id);
						}
						else {
							$element = new $element_type->getInstance();
						}

						$el_attrs = array();
						foreach ($keys as $key) {
							if ($key != '_element_id') {
								$el_attrs[$key] = $data[$el_cls_name][$key][$i];
							}
						}
						$element->attributes = Helper::convertNHS2MySQL($el_attrs);
						$this->setElementComplexAttributesFromData($element, $data, $i);
						$elements[] = $element;
					}
				}
				else {
					if (!$this->event
						|| !$element = $el_cls_name::model()->find('event_id=?',array($this->event->id))) {
						$element = $element_type->getInstance();
					}
					$element->attributes = Helper::convertNHS2MySQL($data[$el_cls_name]);
					$this->setElementComplexAttributesFromData($element, $data);

					$elements[] = $element;
				}
			}
			elseif ($element_type->required) {
				$errors['Event'][] = $element_type->name . ' is required';
				$elements[] = $element_type->getInstance();
			}
		}
		if (!count($elements)) {
			$errors['Event'][] = 'Cannot create an event without at least one element';
		}

		// assign
		$this->open_elements = $elements;

		// validate
		foreach ($this->open_elements as $element) {
			$this->setValidationScenarioForElement($element);
			if (!$element->validate()) {
				$name = $element->getElementTypeName();
				foreach ($element->getErrors() as $errormsgs) {
					foreach ($errormsgs as $error) {
						$errors[$name][] = $error;
					}
				}
			}
 		}

		return $errors;
	}

	/**
	 * Generates the info text for controller event from the current elements, sets it on the event and saves it
	 */
	protected function updateEventInfo()
	{
		$info_text = '';
		foreach ($this->open_elements as $element) {
			if ($element->infotext) {
				$info_text .= $element->infotext;
			}
		}
		$this->event->info = $info_text;
		$this->event->save();
	}

	/**
	 * Iterates through the open elements and calls the custom methods for saving complex data attributes to them.
	 *
	 * Custom method is of the name format saveComplexAttributes_[element_class_name]($element, $data, $index)
	 *
	 * @param $data
	 */
	protected function saveEventComplexAttributesFromData($data)
	{
		$counter_by_cls = array();

		foreach ($this->open_elements as $element) {
			$el_cls_name = get_class($element);
			$element_method = "saveComplexAttributes_" . get_class($element);
			if (method_exists($this, $element_method)) {
				// there's custom behaviour for setting additional relations on this element class
				if (!isset($counter_by_cls[$el_cls_name])) {
					$counter_by_cls[$el_cls_name] = 0;
				}
				else {
					$counter_by_cls[$el_cls_name]++;
				}
				$this->$element_method($element, $data, $counter_by_cls[$el_cls_name]);
			}

		}
	}

	/**
	 * Save the event for this controller - will create or update the event, create and update the elements, delete any
	 * elements that are no longer required. Note that $data is provided for the purposes of any extensions to this
	 * behaviour that might be required.
	 *
	 * @param $data
	 * @return bool
	 * @throws Exception
	 */
	public function saveEvent($data)
	{
		if ($this->event) {
			// this is an edit, so need to work out what we are deleting
			$oe_ids = array();
			foreach ($this->open_elements as $o_e) {
				if ($o_e->id) {
					if (isset($oe_ids[get_class($o_e)])) {
						$oe_ids[get_class($o_e)][] = $o_e->id;
					}
					$oe_ids[get_class($o_e)] = array($o_e->id);
				}
			}
			// delete any elements that are no longer required for the event
			foreach ($this->event->getElements() as $curr_element) {
				if (!isset($oe_ids[get_class($curr_element)])
					|| !in_array($curr_element->id, $oe_ids[get_class($curr_element)])) {
					$curr_element->delete();
				}
			}
		}
		else {
			$this->event = $this->createEvent($this->getOrCreateEpisode());
		}

		foreach ($this->open_elements as $element) {
			$element->event_id = $this->event->id;
			// No need to validate as it has already been validated and the event id was just generated.
			if (!$element->save(false)) {
				throw new Exception('Unable to save element ' . get_class($element) . '.');
			}
		}

		// ensure any complex data is saved to the elements
		$this->saveEventComplexAttributesFromData($data);

		// update the information attribute on the event
		$this->updateEventInfo();

		return true;
	}

	/**
	 * Render the individual element based on the action provided
	 *
	 * @param BaseEventTypeElement $element
	 * @param string $action
	 * @param BaseCActiveBaseEventTypeCActiveForm $form
	 * @param array $data
	 * @throws Exception
	 */
	protected function renderElement($element, $action, $form, $data)
	{
		try {
			// look for an action/element specific view file
			$view = (property_exists($element, $action.'_view')) ? $element->{$action.'_view'} : $element->getDefaultView();
			$this->renderPartial(
				$action . '_' . $view,
				array(
					'element' => $element,
					'data' => $data,
					'form' => $form,
					'child' => $element->getElementType()->isChild()
				)
			);
		} catch (Exception $e) {
			if (strpos($e->getMessage(), "cannot find the requested view") === false) {
				throw $e;
			}
			// otherwise use the default layout
			$this->renderPartial(
				'_'.$action,
				array(
					'element' => $element,
					'data' => $data,
					'form' => $form,
					'child' => $element->getElementType()->isChild()
				)
			);
		}
	}

	/**
	 * Render an optional element based on the action provided
	 *
	 * @param BaseEventTypeElement $element
	 * @param string $action
	 * @param BaseCActiveBaseEventTypeCActiveForm $form
	 * @param array $data
	 * @throws Exception
	 */
	protected function renderOptionalElement($element, $action, $form, $data)
	{
		try {
			$this->renderPartial(
				'_optional_'  . get_class($element),
				array(
					'element' => $element,
					'data' => $data,
					'form' => $form
				),
				false, false
			);
		} catch (Exception $e) {
			if (strpos($e->getMessage(), "cannot find the requested view") === false) {
				throw $e;
			}
			$this->renderPartial(
				'_optional_element',
				array(
					'element' => $element,
					'data' => $data,
					'form' => $form
				),
				false, false
			);
		}


	}
	/**
	 * Render the open elements for the controller state
	 *
	 * @param string $action
	 * @param BaseCActiveBaseEventTypeCActiveForm $form
	 * @param array $data
	 * @throws Exception
	 */
	public function renderOpenElements($action, $form=null, $data=null)
	{
		foreach ($this->getElements() as $element) {
			$this->renderElement($element, $action, $form, $data);
		}
	}

	/**
	 * Render the open elements that are children of the given parent element type
	 *
	 * @param BaseEventTypeElement $parent_element
	 * @param string $action
	 * @param BaseCActiveBaseEventTypeCActiveForm $form
	 * @param array $data
	 * @throws Exception
	 */
	public function renderChildOpenElements($parent_element, $action, $form=null, $data=null)
	{
		foreach ($this->getChildElements($parent_element->getElementType()) as $element) {
			$this->renderElement($element, $action, $form, $data);
		}
	}

	/**
	 * Render the optional elements for the controller state
	 *
	 * @param string $action
	 * @param bool $form
	 * @param bool $data
	 */
	public function renderOptionalElements($action, $form=null,$data=null)
	{
		foreach ($this->getOptionalElements() as $element) {
			$this->renderOptionalElement($element, $action, $form, $data);
		}
	}

	/**
	 * Render the optional child elements for the given parent element type
	 *
	 * @param BaseEventTypeElement $parent_element
	 * @param string $action
	 * @param BaseCActiveBaseEventTypeCActiveForm $form
	 * @param array $data
	 * @throws Exception
	 */
	public function renderChildOptionalElements($parent_element, $action, $form=null, $data=null)
	{
		foreach ($this->getChildOptionalElements($parent_element->getElementType()) as $element) {
			$this->renderOptionalElement($element, $action, $form, $data);
		}
	}

	/**
	 * Get all the episodes for the current patient
	 *
	 * @return array
	 */
	public function getEpisodes()
	{
		if (empty($this->episodes)) {
			$this->episodes = array(
				'ordered_episodes' => $this->patient->getOrderedEpisodes(),
				'legacyepisodes' => $this->patient->legacyepisodes,
				'supportserviceepisodes' => $this->patient->supportserviceepisodes,
			);
		}
		return $this->episodes;
	}

	/**
	 * Called after event (and elements) has been updated
	 * @param Event $event
	 * @TODO: change the call for this?
	 */
	protected function afterUpdateElements($event)
	{
	}

	/**
	 * Called after event (and elements) have been created
	 * @param Event $event
	 * @TODO: change the call for this?
	 */
	protected function afterCreateElements($event)
	{
	}

	/**
	 * Get the current episode for the firm and patient
	 *
	 * @return Episode
	 */
	public function getEpisode()
	{
		return Episode::model()->getCurrentEpisodeByFirm($this->patient->id, $this->firm);
	}

	/**
	 * Create an episode for the firm and patient if it doesn't already exist. Return the episode.
	 *
	 * @return Episode
	 */
	public function getOrCreateEpisode()
	{
		return $this->patient->getOrCreateEpisodeForFirm($this->firm);
	}

	/**
	 * Create the event instance of the given type, based on the elements to process and the user id given.
	 *
	 * @param Episode $episode
	 * @return Event
	 * @throws Exception
	 */
	public function createEvent($episode)
	{
		$event = new Event();
		$event->episode_id = $episode->id;
		$event->event_type_id = $this->event_type->id;

		if (!$event->save()) {
			OELog::log("Failed to create new event for episode_id=$episode->id, event_type_id=" . $this->event_type->id);
			throw new Exception('Unable to save event.');
		}

		OELog::log("Created new event for episode_id=$episode->id, event_type_id=" . $this->event_type->id);

		return $event;
	}


	/**
	 * Render the given errors with the standard template
	 *
	 * @param $errors
	 * @param boolean $bottom
	 */
	public function displayErrors($errors, $bottom=false)
	{
		$this->renderPartial('//elements/form_errors',array(
			'errors'=>$errors,
			'bottom'=>$bottom
		));
	}

	/**
	 * Print action
	 *
	 * @param integer $id event id
	 */
	public function actionPrint($id)
	{
		$this->printInit($id);

		$pdf = (isset($_GET['pdf']) && $_GET['pdf']);
		$this->printLog($id, $pdf);
		//TODO: check on whether we need to pass the elements to the print after all
		if ($pdf) {
			$this->printPDF($id, $this->open_elements);
		} else {
			$this->printHTML($id, $this->open_elements);
		}
	}

	/**
	 * Initialise print action
	 *
	 * @param integer $id event id
	 * @throws CHttpException
	 * @TODO: standardise printInit function as per init naming convention
	 */
	protected function printInit($id)
	{
		if (!$this->event = Event::model()->findByPk($id)) {
			throw new CHttpException(403, 'Invalid event id.');
		}
		$this->patient = $this->event->episode->patient;
		$this->site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);
		$this->setOpenElementsFromCurrentEvent('print');
	}

	/**
	 * Render HTML print layout
	 *
	 * @TODO: are we still doing html printing at all?
	 * @param integer $id event id
	 * @param BaseEventTypeElement[] $elements
	 * @param string $template
	 */
	protected function printHTML($id, $elements, $template='print')
	{
		$this->layout = '//layouts/print';

		$this->render($template, array(
			'elements' => $elements,
			'eventId' => $id,
		));
	}

	/**
	 * Render PDF print layout
	 *
	 * @param integer $id event id
	 * @param BaseEventTypeElement[] $elements
	 * @param string $template
	 * @param array $params
	 */
	protected function printPDF($id, $elements, $template='print', $params=array())
	{
		// Remove any existing css
		Yii::app()->getClientScript()->reset();

		$this->layout = '//layouts/pdf';
		$pdf_print = new OEPDFPrint('Openeyes', 'PDF', 'PDF');
		$oeletter = new OELetter();
		$oeletter->setBarcode('E:'.$id);
		$body = $this->render($template, array_merge($params,array(
			'elements' => $elements,
			'eventId' => $id,
		)), true);
		$oeletter->addBody($body);
		$pdf_print->addLetter($oeletter);
		$pdf_print->output();
	}

	/**
	 * Log print action
	 * @param integer $id event id
	 * @param boolean $pdf
	 */
	protected function printLog($id, $pdf)
	{
		$this->logActivity("printed event (pdf=$pdf)");
		$this->event->audit('event','print',false);
	}

	/**
	 * Delete the event given by $id. Performs the soft delete action if it's been confirmed by $_POST
	 *
	 * @param $id
	 * @throws CHttpException
	 * @throws Exception
	 */
	public function actionDelete($id)
	{
		if (!empty($_POST)) {
			$transaction = Yii::app()->db->beginTransaction();
			try {
				$this->event->softDelete();

				$this->event->audit('event','delete',false);

				if (Event::model()->count('episode_id=?',array($this->event->episode_id)) == 0) {
					$this->event->episode->deleted = 1;
					if (!$this->event->episode->save()) {
						throw new Exception("Unable to save episode: ".print_r($this->event->episode->getErrors(),true));
					}

					$this->event->episode->audit('episode','delete',false);

					$transaction->commit();
					$this->redirect(array('/patient/episodes/'.$this->event->episode->patient->id));

				}

				Yii::app()->user->setFlash('success', "An event was deleted, please ensure the episode status is still correct.");
				$transaction->commit();
				$this->redirect(array('/patient/episode/'.$this->event->episode_id));
			}
			catch (Exception $e) {
				$transaction->rollback();
				throw $e;
			}
		}

		$this->title = "Delete " . $this->event_type->name;

		$this->event_tabs = array(
				array(
						'label' => 'View',
						'active' => true,
				)
		);
		if ($this->editable) {
			$this->event_tabs[] = array(
					'label' => 'Edit',
					'href' => Yii::app()->createUrl($this->event->eventType->class_name.'/default/update/'.$this->event->id),
			);
		}

		$this->processJsVars();

		$episodes = $this->getEpisodes();
		$viewData = array_merge(array(
			'eventId' => $id,
		), $episodes);

		$this->render('delete', $viewData);
	}

	/**
	 * set base js vars for use in the standard scripts for the controller
	 */
	public function processJsVars()
	{
		if ($this->patient) {
			$this->jsVars['OE_patient_id'] = $this->patient->id;
		}
		if ($this->event) {
			$this->jsVars['OE_event_id'] = $this->event->id;
			$this->jsVars['OE_print_url'] = Yii::app()->createUrl($this->getModule()->name."/default/print/".$this->event->id);
		}
		$this->jsVars['OE_asset_path'] = $this->assetPath;
		$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
		$subspecialty_id = $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;
		$this->jsVars['OE_subspecialty_id'] = $subspecialty_id;

		parent::processJsVars();
	}

	/** START OF DEPRECATED METHODS */

	/**
	 * Whether the current user is allowed to call print actions
	 *
	 * @return boolean
	 *
	 * @deprecated Use checkPrintAccess
	 */
	public function canPrint()
	{
		return $this->checkPrintAccess();
	}

	/**
	 * Get all the elements for an event, the current module or an event_type
	 *
	 * @param string $action
	 * @param int $event_type_id
	 * @param Event $event
	 * @return BaseEventTypeElement[]
	 *
	 * @deprecated use open_elements attribute instead
	 */
	public function getDefaultElements($action, $event_type_id = null, $event = null)
	{
		return $this->open_elements;

		if (!$event && isset($this->event)) {
			$event = $this->event;
		}

		if (isset($event->event_type_id)) {
			$event_type = EventType::model()->find('id = ?',array($event->event_type_id));
		} elseif ($event_type_id) {
			$event_type = EventType::model()->find('id = ?',array($event_type_id));
		} else {
			$event_type = EventType::model()->find('class_name = ?',array($this->getModule()->name));
		}

		$criteria = new CDbCriteria;
		$criteria->compare('event_type_id',$event_type->id);
		$criteria->order = 'display_order asc';

		$elements = array();

		if (empty($_POST)) {
			if (isset($event->event_type_id)) {
				foreach (ElementType::model()->findAll($criteria) as $element_type) {
					$element_class = $element_type->class_name;

					foreach ($element_class::model()->findAll(array('condition'=>'event_id=?','params'=>array($event->id),'order'=>'id asc')) as $element) {
						$elements[] = $element;
					}
				}
			} else {
				$criteria->compare('`default`',1);

				foreach (ElementType::model()->findAll($criteria) as $element_type) {
					$element_class = $element_type->class_name;
					$elements[] = new $element_class;
				}
			}
		} else {
			foreach ($_POST as $key => $value) {
				if (preg_match('/^Element|^OEElement/',$key)) {
					if ($element_type = ElementType::model()->find('class_name=?',array($key))) {
						$element_class = $element_type->class_name;

						$keys = array_keys($value);

						if (is_array($value[$keys[0]])) {
							if (isset($event->event_type_id)) {
								foreach ($element_class::model()->findAll(array('condition'=>'event_id=?','params'=>array($event->id),'order'=>'id asc')) as $element) {
									$elements[] = $element;
								}
							} else {
								if ($action != 'update' || !$element_type->default) {
									for ($i=0; $i<count($value[$keys[0]]); $i++) {
										$element = new $element_class;

										foreach ($keys as $_key) {
											if ($_key != '_element_id') {
												$element[$_key] = $value[$_key][$i];
											}
										}

										$elements[] = $element;
									}
								}
							}
						} else {
							if (isset($event->event_type_id) && ($element = $element_class::model()->find('event_id = ?',array($event->id)))) {
								$elements[] = $element;
							} else {
								if ($action != 'update' || !$element_type->default) {
									$elements[] = new $element_class;
								}
							}
						}
					}
				}
			}
		}

		return $elements;
	}

	/**
	 * Stub method:
	 *
	 * Use this for any many to many relations defined on your elements. This is called prior to validation
	 * so should set values without actually touching the database. To do that, the createElements and updateElements
	 * methods should be extended to handle the POST values.
	 *
	 * @param BaseEventTypeElement $element
	 * @deprecated - use setElementComplexAttributesFromData instead
	 */
	final function setPOSTManyToMany($element)
	{
		// placeholder function
	}

	/**
	 * Uses the POST values to define elements and their field values without hitting the db, and then performs validation
	 *
	 * @param BaseEventTypeElement[] - $elements
	 * @return array - $errors
	 * @deprecated - use setAndValidateElementsFromData($data)
	 */
	protected function validatePOSTElements($elements)
	{
		$this->setAndValidateElementsFromData($_POST);
		$errors = array();
		foreach ($elements as $element) {
			$elementClassName = get_class($element);

			if ($element->required || isset($_POST[$elementClassName])) {
				if (isset($_POST[$elementClassName])) {
					$keys = array_keys($_POST[$elementClassName]);

					if (is_array($_POST[$elementClassName][$keys[0]])) {
						for ($i=0; $i<count($_POST[$elementClassName][$keys[0]]); $i++) {
							$element = new $elementClassName;

							foreach ($keys as $key) {
								if ($key != '_element_id') {
									$element->{$key} = $_POST[$elementClassName][$key][$i];
								}
							}

							$this->setPOSTManyToMany($element);

							if (!$element->validate()) {
								$proc_name = $element->procedure->term;
								$elementName = $element->getElementType()->name;
								foreach ($element->getErrors() as $errormsgs) {
									foreach ($errormsgs as $error) {
										$errors[$proc_name][] = $error;
									}
								}
							}
						}
					}
					else
					{
						$element->attributes = Helper::convertNHS2MySQL($_POST[$elementClassName]);
						$this->setPOSTManyToMany($element);
						if (!$element->validate()) {
							$elementName = $element->getElementType()->name;
							foreach ($element->getErrors() as $errormsgs) {
								foreach ($errormsgs as $error) {
									$errors[$elementName][] = $error;
								}
							}
						}
					}
				}
			}

		}

		return $errors;
	}

	/**
	 * Render the default elements for the controller state
	 *
	 * @param $action
	 * @param bool $form
	 * @param bool $data
	 *
	 * @deprecated - use renderOpenElements($action, $form, $data)
	 */
	public function renderDefaultElements($action, $form=false, $data=false)
	{
		foreach ($this->getDefaultElements($action) as $element) {
			if ($action == 'create' && empty($_POST)) {
				$element->setDefaultOptions();
			}

			$view = ($element->{$action.'_view'}) ? $element->{$action.'_view'} : $element->getDefaultView();
			$this->renderPartial(
				$action . '_' . $view,
				array('element' => $element, 'data' => $data, 'form' => $form),
				false, false
			);

		}
	}

	/**
	 * Create the elements for an event with the given data. Returns false if there are errors, otherwise
	 * returns the event that is created for the elements
	 *
	 * @param $elements
	 * @param $data
	 * @param $firm
	 * @param $patientId
	 * @param $userId
	 * @param $eventTypeId
	 * @return bool|string
	 * @throws Exception
	 *
	 * @deprecated - use saveEvent($data)
	 */
	public function createElements($elements, $data, $firm, $patientId, $userId, $eventTypeId)
	{
		$valid = true;
		$elementsToProcess = array();

		// Go through the array of elements to see which the user is attempting to
		// create, which are required and whether they pass validation.
		foreach ($elements as $element) {
			$elementClassName = get_class($element);

			if ($element->required || isset($data[$elementClassName])) {
				if (isset($data[$elementClassName])) {
					$keys = array_keys($data[$elementClassName]);

					if (is_array($data[$elementClassName][$keys[0]])) {
						for ($i=0; $i<count($data[$elementClassName][$keys[0]]); $i++) {
							$element = new $elementClassName;

							foreach ($keys as $key) {
								if ($key != '_element_id') {
									$element->{$key} = $data[$elementClassName][$key][$i];
								}
							}

							$this->setPOSTManyToMany($element);

							if (!$element->validate()) {
								$valid = false;
							} else {
								$elementsToProcess[] = $element;
							}
						}
					} else {
						$element->attributes = Helper::convertNHS2MySQL($data[$elementClassName]);

						$this->setPOSTManyToMany($element);

						if (!$element->validate()) {
							$valid = false;
						} else {
							$elementsToProcess[] = $element;
						}
					}
				}
			}
		}

		if (!$valid) {
			return false;
		}

		/**
		 * Create the event. First check to see if there is currently an episode for this
		 * subspecialty for this patient. If so, add the new event to it. If not, create an
		 * episode and add it to that.
		 */
		$episode = $this->getOrCreateEpisode($firm, $patientId);
		$event = $this->createEvent($episode, $userId, $eventTypeId, $elementsToProcess);

		// Create elements for the event
		foreach ($elementsToProcess as $element) {
			$element->event_id = $event->id;
			// No need to validate as it has already been validated and the event id was just generated.
			if (!$element->save(false)) {
				throw new Exception('Unable to save element ' . get_class($element) . '.');
			}
		}

		$this->afterCreateElements($event);

		return $event->id;
	}

	/**
	 * Update elements based on arrays passed over from $_POST data
	 *
	 * @param BaseEventTypeElement[] $elements
	 * @param array $data $_POST data to update
	 * @param Event $event the associated event
	 *
	 * @throws SystemException
	 * @return bool true if all elements succeeded, false otherwise
	 *
	 * @deprecated - use saveEvent($data)
	 */
	public function updateElements($elements, $data, $event)
	{
		$success = true;
		$toDelete = array();
		$toSave = array();

		foreach ($elements as $element) {
			$elementClassName = get_class($element);
			$needsValidation = false;

			if (isset($data[$elementClassName])) {
				$keys = array_keys($data[$elementClassName]);

				if (is_array($data[$elementClassName][$keys[0]])) {
					if (!$element->id || in_array($element->id,$data[$elementClassName]['_element_id'])) {
						$i = array_search($element->id,$data[$elementClassName]['_element_id']);

						$properties = array();
						foreach ($data[$elementClassName] as $key => $values) {
							$properties[$key] = $values[$i];
						}
						$element->attributes = Helper::convertNHS2MySQL($properties);

						$toSave[] = $element;
						$needsValidation = true;
					} else {
						$toDelete[] = $element;
					}
				} else {
					$element->attributes = Helper::convertNHS2MySQL($data[$elementClassName]);
					$toSave[] = $element;
					$needsValidation = true;
				}
			} elseif ($element->required) {
				// The form has failed to provide an array of data for a required element.
				// This isn't supposed to happen - a required element should at least have the
				// $data[$elementClassName] present, even if there's nothing in it.
				$success = false;
			} elseif ($element->event_id) {
				// This element already exists, isn't required and has had its data deleted.
				// Therefore it needs to be deleted.
				$toDelete[] = $element;
			}

			if ($needsValidation) {
				$this->setPOSTManyToMany($element);
				if (!$element->validate()) {
					$success = false;
				}
			}
		}

		if (!$success) {
			// An element failed validation or a required element didn't have an
			// array of data provided for it.
			return false;
		}

		foreach ($toSave as $element) {
			if (!isset($element->event_id)) {
				$element->event_id = $event->id;
			}

			if (!$element->save()) {
				OELog::log("Unable to save element: $element->id ($elementClassName): ".print_r($element->getErrors(),true));
				throw new SystemException('Unable to save element: '.print_r($element->getErrors(),true));
			}
		}

		foreach ($toDelete as $element) {
			$element->delete();
		}

		$this->afterUpdateElements($event);

		return true;
	}
}
