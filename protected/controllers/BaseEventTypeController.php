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

class BaseEventTypeController extends BaseController
{
	public $model;
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
	public $assetPath;
	public $episode;
	public $moduleNameCssClass = '';
	public $moduleStateCssClass = '';
	public $event_tabs = array();
	public $event_actions = array();
	public $print_css = true;
	public $successUri = 'default/view/';
	// String to set an issue when an event is created
	public $eventIssueCreate;
	public $extraViewProperties = array();
	public $jsVars = array();
	public $layout = '//layouts/events_and_episodes';
	public $current_episode;
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
		// Set asset path
		if (file_exists(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'))) {
			$this->assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'), false, -1, YII_DEBUG);
		}
		return parent::init();
	}

	/**
	 * Checks to see if current user can create an event type
	 *
	 * @param EventType $event_type
	 * @return bool
	 * @deprecated use BaseController::CanCreateEventType
	 */
	public function checkEventAccess($event_type)
	{
		return $this->canCreateEventType($event_type);

		$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
		if (!$firm->service_subspecialty_assignment_id) {
			if (!$event_type->support_services) {
				return false;
			}
		}

		if (BaseController::checkUserLevel(5)) {
			return true;
		}
		if (BaseController::checkUserLevel(4) && $event_type->class_name != 'OphDrPrescription') {
			return true;
		}
		return false;
	}

	/**
	 * Get the accessRules array for the controller
	 *
	 * @return array
	 */
	public function accessRules()
	{
		return array(
			// Level 2 can't change anything
			array('allow',
				'actions' => array('view'),
				'expression' => 'BaseController::checkUserLevel(2)',
			),
			array('allow',
				'actions' => $this->printActions(),
				'expression' => 'BaseController::checkUserLevel(3)',
			),
			// Level 4 or above can do anything
			array('allow',
				'expression' => 'BaseController::checkUserLevel(4)',
			),
			array('deny'),
		);
	}

	private $_event_type;
	/**
	 * The EventType class for this module
	 *
	 * @return EventType
	 */
	public function getEvent_type()
	{
		if (!$this->_event_type) {
			$this->_event_type = EventType::model()->find('class_name=?', array($this->getModule()->name));
		}
		return $this->_event_type;
	}

	/**
	 * Renders the metadata of the event with the standard template
	 * @param string $view
	 */
	public function renderEventMetadata($view='//patient/event_metadata')
	{
		$this->renderPartial($view);
	}

	public function actionIndex()
	{
		$this->render('index');
	}

	/**
	 * define the name of the actions that are print actions (for checking access based on print rules)
	 *
	 * @return array
	 */
	public function printActions()
	{
		return array('print');
	}

	/**
	 * setup base css/js etc requirements for the eventual action render.
	 *
	 * @param $action
	 * @return bool
	 * @throws CHttpException
	 * @see parent::beforeAction($action)
	 */
	protected function beforeAction($action)
	{
		if ($this->event_type->disabled) {
			// disabled module
			$this->redirectToPatientEpisodes();
		}

		// Set the module CSS class name.
		$this->moduleNameCssClass = strtolower(Yii::app()->getController()->module->id);

		// Automatic file inclusion unless it's an ajax call
		if ($this->assetPath && !Yii::app()->getRequest()->getIsAjaxRequest()) {

			if (in_array($action->id,$this->printActions())) {
				// Register print css
				if (file_exists(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets.css').'/print.css')) {
					$this->registerCssFile('module-print.css', $this->assetPath.'/css/print.css');
				}

			} else {
				// Register js
				if (file_exists(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets.js').'/module.js')) {
					Yii::app()->clientScript->registerScriptFile($this->assetPath.'/js/module.js');
				}

				// Register css
				if (file_exists(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets.css').'/module.css')) {
					$this->registerCssFile('module.css',$this->assetPath.'/css/module.css',10);
				}
			}
		}

		parent::storeData();

		$this->firm = Firm::model()->findByPk($this->selectedFirmId);

		if (!isset($this->firm)) {
			// No firm selected, reject
			throw new CHttpException(403, 'You are not authorised to view this page without selecting a firm.');
		}

		return parent::beforeAction($action);
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
							if ($action != 'update' || !$element_type->default) {
								for ($i=0; $i<count($value[$keys[0]]); $i++) {
									$element = new $element_class;
									$element->event_id = $event ? $event->id : null;

									foreach ($keys as $_key) {
										if ($_key != '_element_id') {
											$element[$_key] = $value[$_key][$i];
										}
									}

									$elements[] = $element;
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
	 * Get the optional elements for the current module's event type
	 *
	 * @return array
	 */
	public function getOptionalElements()
	{
		$open_et = array();
		foreach ($this->open_elements as $open) {
			$open_et[] = get_class($open);
		}
		$optional = array();
		foreach ($this->event_type->getAllElementTypes() as $element_type) {
			if (!in_array($element_type->class_name, $open_et)) {
				$optional[] = new $element_type->class_name;
			}
		}

		return $optional;
	}

	/**
	 * Redirect to the patient episodes when the controller determines the action cannot be carried out
	 *
	 */
	protected function redirectToPatientEpisodes()
	{
		$this->redirect(array("/patient/episodes/".$this->patient->id));
	}

	/**
	 * Set the default values on each of the open elements.
	 *
	 * @param string $action
	 */
	protected function setDefaultOptions()
	{
		foreach ($this->open_elements as $element) {
			$element->setDefaultOptions();
		}
	}

	/**
	 * Initialise the controller prior to a create action
	 *
	 * @throws CHttpException
	 */
	protected function createInit()
	{
		$this->moduleStateCssClass = 'edit';

		if (!$this->patient = Patient::model()->findByPk($_REQUEST['patient_id'])) {
			throw new CHttpException(403, 'Invalid patient_id.');
		}

		if (!$this->episode = $this->getEpisode($this->firm, $this->patient->id)) {
			$this->redirectToPatientEpisodes();
		}

		if (!$this->event_type->support_services && !$this->firm->getSubspecialty()) {
			// Can't create a non-support service event for a support-service firm
			$this->redirectToPatientEpisodes();
		}
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
		$this->createInit();

		if (!empty($_POST)) {
			// form has been submitted
			if (isset($_POST['cancel'])) {
				$this->redirectToPatientEpisodes();
			}

			// set and validate
			$errors = $this->setAndValidateElementsFromData($_POST);

			// creation
			if (empty($errors)) {
				$success = $this->saveEvent($_POST);

				if ($success) {
					$this->logActivity('created event.');

					if ($this->eventIssueCreate) {
						$this->event->addIssue($this->eventIssueCreate);
					}

					$audit_data = array('event' => $this->event->getAuditAttributes());

					//TODO: should this be simply handled by the audit wrapper of the event?
					foreach ($this->open_elements as $element) {
						$audit_data[get_class($element)] = $element->getAuditAttributes();
					}

					$this->event->audit('event','create',serialize($audit_data));

					Yii::app()->user->setFlash('success', "{$this->event_type->name} created.");
					$this->redirect(array($this->successUri.$this->event->id));
				}
				else {
					throw new Exception("could not save event");
				}
			}
		}
		else {
			$this->open_elements = $this->event_type->getDefaultElements();
			$this->setDefaultOptions();
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

		$this->processJsVars();

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
		$this->moduleStateCssClass = 'view';

		if (!$this->event = Event::model()->findByPk($id)) {
			throw new CHttpException(403, 'Invalid event id.');
		}
		$this->patient = $this->event->episode->patient;
		$this->episode = $this->event->episode;

		$this->open_elements = $this->event->getElements();

		// Decide whether to display the 'edit' button in the template
		if ($this->editable) {
			if (!BaseController::checkUserLevel(4) || (!$this->event->episode->firm && !$this->event->episode->support_services)) {
				$this->editable = false;
			} else {
				if ($this->firm->getSubspecialtyID() != $this->event->episode->getSubspecialtyID()) {
					$this->editable = false;
				}
			}
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
		if ($this->canDelete()) {
			$this->event_actions = array(
				EventAction::link('Delete',
					Yii::app()->createUrl($this->event->eventType->class_name.'/default/delete/'.$this->event->id),
					array('level' => 'delete')
				)
			);
		}

		$this->processJsVars();

		$viewData = array_merge(array(
			'elements' => $this->open_elements,
			'eventId' => $id,
		), $this->extraViewProperties);

		$this->render('view', $viewData);
	}

	/**
	 * initialise the controller prior to event update action
	 *
	 * @param $id
	 * @throws CHttpException
	 */
	protected function updateInit($id)
	{
		$this->moduleStateCssClass = 'edit';
		if (!$this->event = Event::model()->findByPk($id)) {
			throw new CHttpException(403, 'Invalid event id.');
		}

		$this->patient = $this->event->episode->patient;

		// Check the user's firm is of the correct subspecialty to have the
		// rights to update this event
		if ($this->firm->getSubspecialtyID() != $this->event->episode->getSubspecialtyID()) {
			//The firm you are using is not associated with the subspecialty of the episode
			$this->redirectToPatientEpisodes();
		}

		$this->episode = $this->event->episode;
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
		$this->updateInit();

		if (!empty($_POST)) {
			if (isset($_POST['cancel'])) {
				// Cancel button pressed, so just bounce to view
				$this->redirect(array('default/view/'.$this->event->id));
			}

			$errors = $this->setAndValidateElementsFromData($_POST);

			// creation
			if (empty($errors)) {
				//TODO: setup a transaction at this point.
				//TODO: should all the auditing be moved into the saving of the event
				$success = $this->saveEvent($_POST);

				if ($success) {

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

					$this->redirect(array('default/view/'.$this->event->id));
				}
				else {
					throw new Exception("Unable to save edits to event");
				}
			}
		}
		else {
			$this->open_elements = $this->event->getElements();
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

		$this->processJsVars();

		$this->render($this->action->id, array(
			'errors' => @$errors
		));
	}

	/**
	 * Stub method:
	 *
	 * Use this for any many to many relations defined on your elements. This is called prior to validation
	 * so should set values without actually touching the database. To do that, the createElements and updateElements
	 * methods should be extended to handle the POST values.
	 *
	 * @param BaseEventTypeElement $element
	 */
	protected function setPOSTManyToMany($element)
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

						$generic[$elementClassName] = $_POST[$elementClassName];

						for ($i = 0; $i < count($_POST[$elementClassName][$keys[0]]); $i++)
						{
							$element = new $elementClassName;

							foreach ($keys as $key) {
								if ($key != '_element_id') {
									$element->{$key} = array_shift($generic[$elementClassName][$key]);
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
	 * Stub method:
	 *
	 * Use this for any many to many relations defined on elements, and any other custom data setting outside of the
	 * standard applying attribute values. This is called prior to validation so should set values without actually
	 * touching the database.
	 *
	 * The $data attribute will typically be the $_POST structure, but can be any appropriately structured array
	 * The optional $index attribute is thecounter for multiple elements of the same type that might exist in source data.
	 *
	 * @param BaseEventTypeElement $element
	 * @param array $data
	 * @param integer $index
	 */
	protected function setElementComplexAttributesFromData($element, $data, $index = null)
	{
		// placeholder method
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

		// only process data for elements that are part of the element type set for the controller event type
		foreach ($this->event_type->getAllElementTypes() as $element_type) {
			$el_cls_name = $element_type->class_name;
			if (isset($data[$el_cls_name])) {
				$keys = array_keys($data[$el_cls_name]);

				if (is_array($data[$el_cls_name][$keys[0]])) {
					// there is more than one element of this type
					if ($this->event && !$data[$el_ls_name]['_element_id']) {
						throw new Exception("missing _element_id for multiple elements for editing an event");
					}

					// iterate through each to define the multiple instances we require
					for ($i=0; $i<count($data[$el_cls_name][$keys[0]]); $i++) {
						if ($el_id = $data[$el_cls_name]['_element_id'][$i]) {
							$element = $el_cls_name::model()->findByPk($el_id);
						}
						else {
							$element = new $el_cls_name;
						}

						$el_attrs = array();
						foreach ($keys as $key) {
							if ($key != '_element_id') {
								$el_attrs[$key] = $data[$el_cls_name][$key][$i];
							}
						}
						$element->attributes = Helper::convertNHS2MySQL($el_attrs);
						$this->setElementComplexAttributesFromData($element, $data, $i);

						if (!$element->validate()) {
							// FIXME: this is not suitably generic for the base controller, needs to be abstracted
							$proc_name = $element->procedure->term;
							foreach ($element->getErrors() as $errormsgs) {
								foreach ($errormsgs as $error) {
									$errors[$proc_name][] = $error;
								}
							}
						}
						$elements[] = $element;
					}
				}
				else {
					if (!$this->event
						|| !$element = $el_cls_name::model()->find('event_id=?',array($this->event->id))) {
						$element = new $el_cls_name;
					}
					$element->attributes = Helper::convertNHS2MySQL($data[$el_cls_name]);
					$this->setElementComplexAttributesFromData($element, $data);
					if (!$element->validate()) {
						$element_name = $element_type->name;
						foreach ($element->getErrors() as $errormsgs) {
							foreach ($errormsgs as $error) {
								$errors[$element_name][] = $error;
							}
						}
					}
					$elements[] = $element;
				}
			}
			elseif ($element_type->required) {
				$errors['Event'][] = $element_type->name . ' is required';
			}
		}

		$this->open_elements = $elements;

		return $errors;
	}

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
	 * Stub method to allow custom behaviour for managing the many to many data fields that have been submitted for the
	 * elements, and any other custom data setting outside of the standard applying attribute values.
	 *
	 * Sibling method to setElementComplexAttributesFromdata($element, $data)
	 *
	 * @param $data
	 */
	protected function saveEventComplexAttributesFromData($data)
	{
		//placeholder method
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
						$oe_ids[get_class($o_e)][] = $oe_id;
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
	 * Render the open elements for the controller state
	 *
	 * @param string $action
	 * @param BaseCActiveBaseEventTypeCActiveForm $form
	 * @param array $data
	 */
	public function renderOpenElements($action, $form = null, $data=null)
	{
		foreach ($this->open_elements as $element) {
			$view = ($element->{$action.'_view'}) ? $element->{$action.'_view'} : $element->getDefaultView();
			$this->renderPartial(
				$action . '_' . $view,
				array('element' => $element, 'data' => $data, 'form' => $form),
				false, false
			);
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

			$view = ($element->{$action.'_view'}) ? $element->{$action.'_view'} : $element->getDefaultView();
			$this->renderPartial(
				$action . '_' . $view,
				array('element' => $element, 'data' => $data, 'form' => $form),
				false, false
			);
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
				'ordered_episodes'=>$this->patient->getOrderedEpisodes(),
				'legacyepisodes'=>$this->patient->legacyepisodes,
				'supportserviceepisodes'=>$this->patient->supportserviceepisodes,
			);
		}
		return $this->episodes;
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

						$properties = array();

						foreach ($data[$elementClassName] as $key => $values) {
							if ($key != '_element_id') {
								$properties[$key] = array_shift($data[$elementClassName][$key]);
							}
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
			OELog::log("Failed to creat new event for episode_id=$episode->id, event_type_id=" . $this->event_type->id);
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
	 */
	protected function printInit($id)
	{
		if (!$this->event = Event::model()->findByPk($id)) {
			throw new CHttpException(403, 'Invalid event id.');
		}
		$this->patient = $this->event->episode->patient;
		$this->event_type = $this->event->eventType;
		$this->site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);
		$this->open_elements = $this->event->getElements();
	}

	/**
	 * Render HTML print layout
	 *
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


	public function canDelete()
	{
		if($this->event){
			return($this->event->canDelete());
		}
		return false;
	}

	/**
	 * Delete the event given by $id. Performs the soft delete action if it's been confirmed by $_POST
	 *
	 * @param $id
	 * @return bool
	 * @throws CHttpException
	 * @throws Exception
	 */
	public function actionDelete($id)
	{
		if (!$this->event = Event::model()->findByPk($id)) {
			throw new CHttpException(403, 'Invalid event id.');
		}

		// Only the event creator can delete the event, and only 24 hours after its initial creation
		if (!$this->canDelete()) {
			$this->redirect(array('default/view/'.$this->event->id));
			return false;
		}

		if (!empty($_POST)) {
			$this->event->softDelete();

			$this->event->audit('event','delete',false);

			if (Event::model()->count('episode_id=?',array($this->event->episode_id)) == 0) {
				$this->event->episode->deleted = 1;
				if (!$this->event->episode->save()) {
					throw new Exception("Unable to save episode: ".print_r($this->event->episode->getErrors(),true));
				}

				$this->event->episode->audit('episode','delete',false);

				header('Location: '.Yii::app()->createUrl('/patient/episodes/'.$this->event->episode->patient->id));
				return true;
			}

			Yii::app()->user->setFlash('success', "An event was deleted, please ensure the episode status is still correct.");

			header('Location: '.Yii::app()->createUrl('/patient/episode/'.$this->event->episode_id));
			return true;
		}

		$this->patient = $this->event->episode->patient;

		$this->event_type = EventType::model()->findByPk($this->event->event_type_id);

		$this->title = "Delete ".$this->event_type->name;
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

		return false;
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
}
