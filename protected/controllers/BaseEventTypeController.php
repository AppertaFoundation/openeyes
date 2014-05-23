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
	const ACTION_TYPE_REQUESTDELETE = 'RequestDelete';
	const ACTION_TYPE_FORM = 'Form';	// AJAX actions that are used during create and update but don't actually modify data themselves

	static private $base_action_types = array(
		'create' => self::ACTION_TYPE_CREATE,
		'view' => self::ACTION_TYPE_VIEW,
		'elementForm' => self::ACTION_TYPE_FORM,
		'viewPreviousElements' => self::ACTION_TYPE_FORM,
		'print' => self::ACTION_TYPE_PRINT,
		'update' => self::ACTION_TYPE_EDIT,
		'delete' => self::ACTION_TYPE_DELETE,
		'requestDeletion' => self::ACTION_TYPE_REQUESTDELETE,
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
	 * Sets the firm property on the controller from the session
	 *
	 * @throws HttpException
	 */
	protected function setFirmFromSession()
	{
		if (!$firm_id = Yii::app()->session->get('selected_firm_id')) {
			throw new HttpException('Firm not selected');
		}
		if (!$this->firm || $this->firm->id != $firm_id) {
			$this->firm = Firm::model()->findByPk($firm_id);
		}
	}

	/**
	 * Sets the patient object on the controller
	 *
	 * @param $patient_id
	 * @throws CHttpException
	 */
	protected function setPatient($patient_id)
	{
		if (!$this->patient = Patient::model()->findByPk($patient_id)) {
			throw new CHttpException(404, 'Invalid patient_id.');
		}
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
		if ($this->event && !$this->event->isNewRecord) {
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
				Yii::app()->assetManager->registerScriptFile('js/nested_elements.js');
			}
		}

		$this->setFirmFromSession();

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

		$el_method = 'setElementDefaultOptions_' . Helper::getNSShortname($element);
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
		return $element->canViewPrevious() && $this->hasPrevious($element->getElementType(), $element->event_id);
	}

	/**
	 * Is this a required element?
	 * @param  BaseEventTypeElement  $element
	 * @return boolean
	 */
	public function isRequired(BaseEventTypeElement $element)
	{
		return $element->isRequired();
	}

	/**
	 * Is this element required in the UI? (Prevents the user from being able
	 * to remove the element.)
	 * @param  BaseEventTypeElement  $element
	 * @return boolean
	 */
	public function isRequiredInUI(BaseEventTypeElement $element)
	{
		return $element->isRequiredInUI();
	}

	/**
	 * Is this element to be hidden in the UI? (Prevents the elements from
	 * being displayed on page load.)
	 * @param  BaseEventTypeElement  $element
	 * @return boolean
	 */
	public function isHiddenInUI(BaseEventTypeElement $element)
	{
		return $element->isHiddenInUI();
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

		$this->setPatient($_REQUEST['patient_id']);

		if (!$this->episode = $this->getEpisode()) {
			$this->redirectToPatientEpisodes();
		}

		// we instantiate an event object for use with validation rules that are dependent
		// on episode and patient status
		$this->event = new Event();
		$this->event->episode_id = $this->episode->id;
		$this->event->event_type_id = $this->event_type->id;
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
	public function checkRequestDeleteAccess()
	{
		return $this->checkAccess('OprnRequestEventDeletion', $this->firm, $this->event);
	}

	/**
	 * @return boolean
	 */
	public function checkFormAccess()
	{
		return $this->checkAccess('OprnViewClinical');
	}

	/**
	 * @return boolean
	 */
	public function checkAdminAccess()
	{
		return $this->checkAccess('admin');
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

						$this->event->audit('event','create');

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
			$this->editable = $this->checkEditAccess();
		}

		$this->logActivity('viewed event');

		$this->event->audit('event','view');

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

		if ($this->checkDeleteAccess()) {
			$this->event_actions = array(
				EventAction::link('Delete',
					Yii::app()->createUrl($this->event->eventType->class_name.'/default/delete/'.$this->event->id),
					array('level' => 'delete')
				)
			);
		} elseif ($this->checkRequestDeleteAccess()) {
			$this->event_actions = array(
				EventAction::link('Delete',
					Yii::app()->createUrl($this->event->eventType->class_name.'/default/requestDeletion/'.$this->event->id),
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

						$this->event->audit('event','update');

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
		Yii::app()->assetManager->reset();

		$this->patient = $patient;

		$this->setFirmFromSession();

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

		$this->renderElement($element, 'create', $form, null, array(
			'previous_parent_id' => $previous_id
		), false, true);
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
		Yii::app()->assetManager->reset();

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
		$element_method = "setComplexAttributes_" . Helper::getNSShortname($element);
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
			$f_key = CHtml::modelName($el_cls_name);
			if (isset($data[$f_key])) {
				$keys = array_keys($data[$f_key]);

				if (is_array($data[$f_key][$keys[0]]) && !count(array_filter(array_keys($data[$f_key]), 'is_string'))) {
					// there is more than one element of this type
					$pk_field = $el_cls_name::model()->tableSchema->primaryKey;
					foreach ($data[$f_key] as $i => $attrs) {
						if (!$this->event->isNewRecord && !isset($attrs[$pk_field])) {
							throw new Exception("missing primary key field for multiple elements for editing an event");
						}
						if ($pk = @$attrs[$pk_field]) {
							$element = $el_cls_name::model()->findByPk($pk);
						}
						else {
							$element = $element_type->getInstance();
						}
						$element->attributes = Helper::convertNHS2MySQL($attrs);
						$this->setElementComplexAttributesFromData($element, $data, $i);
						$element->event = $this->event;
						$elements[] = $element;
					}
				}
				else {
					if ($this->event->isNewRecord
						|| !$element = $el_cls_name::model()->find('event_id=?',array($this->event->id))) {
						$element = $element_type->getInstance();
					}
					$element->attributes = Helper::convertNHS2MySQL($data[$f_key]);
					$this->setElementComplexAttributesFromData($element, $data);
					$element->event = $this->event;
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
			$element_method = "saveComplexAttributes_" . Helper::getNSShortname($element);
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
		if(isset($data['Event']['accomplished_date'])){
			$this->event->accomplished_date = Helper::convertNHS2MySQL($data['Event']['accomplished_date']);
		}

		if (!$this->event->isNewRecord) {
			// this is an edit, so need to work out what we are deleting
			$oe_ids = array();
			foreach ($this->open_elements as $o_e) {
				if ($o_e->id) {
					if (isset($oe_ids[get_class($o_e)])) {
						$oe_ids[get_class($o_e)][] = $o_e->id;
					} else {
						$oe_ids[get_class($o_e)] = array($o_e->id);
					}
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
			if (!$this->event->save()) {
				OELog::log("Failed to create new event for episode_id={$this->episode->id}, event_type_id=" . $this->event_type->id);
				throw new Exception('Unable to save event.');
			}
			OELog::log("Created new event for episode_id={$this->episode->id}, event_type_id=" . $this->event_type->id);
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
	 * Get the prefix name of this controller, used for path calculations for element views
	 *
	 * @return string
	 */
	protected function getControllerPrefix()
	{
		return strtolower(str_replace('Controller', '', Helper::getNSShortname($this)));
	}

	/**
	 * Return the path alias for the module the element belongs to based on its namespace
	 * (assumes elements exist in a namespace below the module namespace)
	 *
	 * @param BaseEventTypeElement $element
	 * @return string
	 */
	public function getElementModulePathAlias(\BaseEventTypeElement $element)
	{
		$r = new ReflectionClass($element);

		if ($r->inNamespace()) {
			$ns_parts = explode('\\',$r->getNamespaceName());
			return implode('.',array_slice($ns_parts,0,count($ns_parts)-1));
		}
		return $this->modulePathAlias;
	}

	/**
	 * Return the asset path for the given element (by interrogating namespace)
	 *
	 * @param BaseEventTypeElement $element
	 * @return string
	 */
	public function getAssetPathForElement(\BaseEventTypeElement $element) {
		if ($alias = $this->getElementModulePathAlias($element)) {
			return Yii::app()->assetManager->getPublishedPathOfAlias($alias.'.assets');
		}
		else {
			return $this->assetPath;
		}
	}
	/**
	 * calculate the alias dot notated path to an element view
	 *
	 * @param BaseEventTypeElement $element
	 * @return string
	 */
	protected function getElementViewPathAlias(\BaseEventTypeElement $element)
	{
		if ($alias = $this->getElementModulePathAlias($element)) {
			return $alias . '.views.' . $this->getControllerPrefix() . '.';
		}
		return '';
	}

	/**
	 * Extend the parent method to support inheritance of modules (and rendering the element views from the parent module)
	 *
	 * @param string $view
	 * @param null $data
	 * @param bool $return
	 * @param bool $processOutput
	 * @return string
	 */
	public function renderPartial($view,$data=null,$return=false,$processOutput=false)
	{
		if ($this->getViewFile($view) === false) {
			foreach ($this->getModule()->getModuleInheritanceList() as $mod) {
				// assuming that any inheritance maintains the controller name here.
				$view_path = implode('.', array($mod->id, 'views', $this->getControllerPrefix(), $view));
				if ($this->getViewFile($view_path)) {
					$view = $view_path;
					break;
				}
			}
		}
		return parent::renderPartial($view, $data, $return, $processOutput);
	}

	/**
	 * Render the individual element based on the action provided. Note that view names
	 * for the associated actions are set in the model.
	 *
	 * @param BaseEventTypeElement $element
	 * @param string $action
	 * @param BaseCActiveBaseEventTypeCActiveForm $form
	 * @param array $data
	 * @param array $view_data Data to be passed to the view.
	 * @param boolean $return Whether the rendering result should be returned instead of being displayed to end users.
	 * @param boolean $processOutput Whether the rendering result should be postprocessed using processOutput.
	 * @throws Exception
	 */
	protected function renderElement($element, $action, $form, $data, $view_data=array(), $return=false, $processOutput=false)
	{
		// Get the view names from the model.
		$view = isset($element->{$action.'_view'})
			? $element->{$action.'_view'}
			: $element->getDefaultView();

		$container_view = isset($element->{'container_'.$action.'_view'})
			? $element->{'container_'.$action.'_view'}
			: $element->getDefaultContainerView();

		$use_container_view = ($element->useContainerView && $container_view);

		$view_data = array_merge(array(
			'element' => $element,
			'data' => $data,
			'form' => $form,
			'child' => $element->getElementType()->isChild(),
			'container_view' => $container_view
		), $view_data);

		// Render the view.
		($use_container_view) && $this->beginContent($container_view, $view_data);
		$this->renderPartial($this->getElementViewPathAlias($element) . $view, $view_data, $return, $processOutput);
		($use_container_view) && $this->endContent();
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
		if($form && (($action == strtolower (self::ACTION_TYPE_CREATE) ) || $this->checkAdminAccess()) ){
			echo $form->datePicker($this->event, 'accomplished_date', array(), array(), array(
				'label' => $form->layoutColumns['label'],
				'field' => 3
			));
		}

		foreach ($this->getElements() as $element) {
			$this->renderElement($element, $action, $form, $data);
		}
	}

	/**
	* Render an optional element
	*
	* @param BaseEventTypeElement $element
	* @param string $action
	* @param BaseCActiveBaseEventTypeCActiveForm $form
	* @param array $data
	* @throws Exception
	*/
	protected function renderOptionalElement($element, $action, $form, $data)
	{
		$el_view = $this->getElementViewPathAlias($element) . '_optional_'	. $element->getDefaultView();
		$view = $this->getViewFile($el_view)
			? $el_view
			: $this->getElementViewPathAlias($element) . '_optional_element';

		$this->renderPartial($view, array(
			'element' => $element,
			'data' => $data,
			'form' => $form
		), false, false);
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
	 * Get the current episode for the firm and patient
	 *
	 * @return Episode
	 */
	public function getEpisode()
	{
		return Episode::model()->getCurrentEpisodeByFirm($this->patient->id, $this->firm);
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
		// Remove any existing assets that have been pre-registered.
		Yii::app()->assetManager->reset();

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
		if (isset($_POST['et_canceldelete'])) {
			return $this->redirect(array('/'.$this->event_type->class_name.'/default/view/'.$id));
		}

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
	 * Called after event (and elements) has been updated
	 * @param Event $event
	 */
	protected function afterUpdateElements($event)
	{
	}

	/**
	 * Called after event (and elements) have been created
	 * @param Event $event
	 */
	protected function afterCreateElements($event)
	{
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

	/**
	 * Sets the the css state
	 */
	protected function initActionRequestDeletion()
	{
		$this->moduleStateCssClass = 'view';

		$this->initWithEventId(@$_GET['id']);
	}

	/**
	 * Action to process delete requests for an event
	 *
	 * @param $id
	 * @return bool|void
	 * @throws CHttpException
	 */
	public function actionRequestDeletion($id)
	{
		if (!$this->event = Event::model()->findByPk($id)) {
			throw new CHttpException(403, 'Invalid event id.');
		}

		if (isset($_POST['et_canceldelete'])) {
			return $this->redirect(array('/'.$this->event->eventType->class_name.'/default/view/'.$id));
		}

		$this->patient = $this->event->episode->patient;

		$errors = array();

		if (!empty($_POST)) {
			if (!@$_POST['delete_reason']) {
				$errors = array('Reason' => array('Please enter a reason for deleting this event'));
			} else {
				$this->event->requestDeletion($_POST['delete_reason']);

				if (Yii::app()->params['admin_email']) {
					mail(Yii::app()->params['admin_email'],"Request to delete an event","A request to delete an event has been submitted.  Please log in to the admin system to review the request.","From: OpenEyes");
				}

				Yii::app()->user->setFlash('success', "Your request to delete this event has been submitted.");

				header('Location: '.Yii::app()->createUrl('/'.$this->event_type->class_name.'/default/view/'.$this->event->id));
				return true;
			}
		}

		$this->title = "Delete ".$this->event_type->name;
		$this->event_tabs = array(array(
				'label' => 'View',
				'active' => true,
		));

		$this->render('request_delete', array(
			'errors' => $errors,
		));
	}
}
