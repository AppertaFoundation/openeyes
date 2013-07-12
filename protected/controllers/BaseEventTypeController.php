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
	public $firm;
	public $patient;
	public $site;
	public $editable = true;
	public $editing;
	public $event;
	public $event_type;
	public $title;
	public $assetPath;
	public $episode;
	public $event_tabs = array();
	public $event_actions = array();
	public $print_css = true;
	public $successUri = 'default/view/';
	public $eventIssueCreate = false;
	public $extraViewProperties = array();
	public $jsVars = array();

	/**
	 * Checks to see if current user can create an event type
	 * @param EventType $event_type
	 */
	public function checkEventAccess($event_type)
	{
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

	public function accessRules()
	{
		return array(
			// Level 2 can't change anything
			array('allow',
				'actions' => array('view'),
				'expression' => 'BaseController::checkUserLevel(2)',
			),
			// Level 3 or above can do anything
			array('allow',
				'expression' => 'BaseController::checkUserLevel(4)',
			),
			array('deny'),
		);
	}

	/**
	 * Whether the current user is allowed to call print actions
	 * @return boolean
	 */
	public function canPrint()
	{
		return BaseController::checkUserLevel(3);
	}

	public function renderEventMetadata()
	{
		$this->renderPartial('//patient/event_metadata');
	}

	public function actionIndex()
	{
		$this->render('index');
	}

	public function printActions()
	{
		return array('print');
	}

	protected function beforeAction($action)
	{
		// Set asset path
		if (file_exists(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'))) {
			$this->assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'), false, -1, YII_DEBUG);
		}

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
				if (file_exists(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets.js').'/'.get_class($this).'.js')) {
					Yii::app()->clientScript->registerScriptFile($this->assetPath.'/js/'.get_class($this).'.js');
				}

				// Register css
				if (file_exists(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets.css').'/module.css')) {
					$this->registerCssFile('module.css',$this->assetPath.'/css/module.css',10);
				}
				if (file_exists(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets.css').'/css/'.get_class($this).'.css')) {
					$this->registerCssFile(get_class($this).'.css',$this->assetPath.'/css/'.get_class($this).'.css',10);
				}
			}
		}

		parent::storeData();

		$this->firm = Firm::model()->findByPk($this->selectedFirmId);

		if (!isset($this->firm)) {
			// No firm selected, reject
			throw new CHttpException(403, 'You are not authorised to view this page without selecting a firm.');
		}

		// Clear js for ajax calls
		if (Yii::app()->getRequest()->getIsAjaxRequest()) {
			$scriptMap = Yii::app()->clientScript->scriptMap;
			$scriptMap['jquery.js'] = false;
			$scriptMap['jquery.min.js'] = false;
			$scriptMap['jquery-ui.js'] = false;
			$scriptMap['jquery-ui.min.js'] = false;
			$scriptMap['module.js'] = false;
			Yii::app()->clientScript->scriptMap = $scriptMap;
		}

		return parent::beforeAction($action);;
	}

	/**
	 * Get all the elements for an event, the current module or an event_type
	 *
	 * @return array
	 */
	public function getDefaultElements($action, $event_type_id=false, $event=false)
	{
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

					if ($element = $element_class::model()->find('event_id = ?',array($event->id))) {
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

		return $elements;
	}

	/**
	 * Get the optional elements for the current module's event type
	 * This will be overriden by the module
	 *
	 * @return array
	 */
	public function getOptionalElements($action)
	{
		switch ($action) {
			case 'create':
			case 'view':
			case 'print':
				return array();
			case 'update':
				$event_type = EventType::model()->findByPk($this->event->event_type_id);

				$criteria = new CDbCriteria;
				$criteria->compare('event_type_id',$event_type->id);
				$criteria->compare('`default`',1);
				$criteria->order = 'display_order asc';

				$elements = array();
				$element_classes = array();

				foreach (ElementType::model()->findAll($criteria) as $element_type) {
					$element_class = $element_type->class_name;
					if (!$element_class::model()->find('event_id = ?',array($this->event->id))) {
						$elements[] = new $element_class;
					}
				}

				return $elements;
		}
	}

	public function actionCreate()
	{
		$this->event_type = EventType::model()->find('class_name=?', array($this->getModule()->name));
		if (!$this->patient = Patient::model()->findByPk($_REQUEST['patient_id'])) {
			throw new CHttpException(403, 'Invalid patient_id.');
		}

		if (is_array(Yii::app()->params['modules_disabled']) && in_array($this->event_type->class_name,Yii::app()->params['modules_disabled'])) {
			return $this->redirect(array('/patient/episodes/'.$this->patient->id));
		}

		$session = Yii::app()->session;
		$firm = Firm::model()->findByPk($session['selected_firm_id']);
		$this->episode = $this->getEpisode($firm, $this->patient->id);

		if (!$this->event_type->support_services && !$firm->serviceSubspecialtyAssignment) {
			throw new Exception("Can't create a non-support service event for a support-service firm");
		}

		// firm changing sanity
		if (!empty($_POST) && !empty($_POST['firm_id']) && $_POST['firm_id'] != $this->firm->id) {
			// The firm id in the firm is not the same as the session firm id, e.g. they've changed
			// firms in a different tab. Set the session firm id to the provided firm id.

			$firms = $session['firms'];
			$firmId = intval($_POST['firm_id']);

			if ($firms[$firmId]) {
				$session['selected_firm_id'] = $firmId;
				$this->selectedFirmId = $firmId;
				$this->firm = Firm::model()->findByPk($this->selectedFirmId);
			} else {
				// They've supplied a firm id in the post to which they are not entitled??
				throw new Exception('Invalid firm id on attempting to create event.');
			}
		}
		$elements = $this->getDefaultElements('create', $this->event_type->id);

		if (empty($_POST) && !count($elements)) {
			throw new CHttpException(403, 'Gadzooks!	I got me no elements!');
		}

		if (!empty($_POST) && isset($_POST['cancel'])) {
			$this->redirect(array('/patient/view/'.$this->patient->id));
			return;
		} elseif (!empty($_POST) && !count($elements)) {
			$errors['Event'][] = 'No elements selected';
		} elseif (!empty($_POST)) {

			$elements = array();
			$element_names = array();

			foreach (ElementType::model()->findAll('event_type_id=?',array($this->event_type->id)) as $element_type) {
				if (isset($_POST[$element_type->class_name])) {
					$elements[] = new $element_type->class_name;
					$element_names[$element_type->class_name] = $element_type->name;
				}
			}

			$elementList = array();

			// validation
			$errors = $this->validatePOSTElements($elements);


			// creation
			if (empty($errors)) {
				// The user has submitted the form to create the event
				$eventId = $this->createElements(
					$elements, $_POST, $this->firm, $this->patient->id, Yii::app()->user->id, $this->event_type->id
				);

				if ($eventId) {
					$this->logActivity('created event.');

					$event = Event::model()->findByPk($eventId);

					if ($this->eventIssueCreate) {
						$event->addIssue($this->eventIssueCreate);
					}

					$audit_data = array('event' => $event->getAuditAttributes());

					foreach ($elements as $element) {
						$audit_data[get_class($element)] = $element->getAuditAttributes();
					}

					$event->audit('event','create',serialize($audit_data));

					Yii::app()->user->setFlash('success', "{$this->event_type->name} created.");
					$this->redirect(array($this->successUri.$eventId));
					return $eventId;
				}
			}
		}

		$this->editable = false;
		$this->title = 'Create';
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
						array('colour' => 'red', 'level' => 'secondary')
				)
		);

		$this->processJsVars();
		$this->renderPartial(
			'create',
			array('elements' => $this->getDefaultElements('create'), 'eventId' => null, 'errors' => @$errors),
			// processOutput is true so that the css/javascript from the event_header.php are processed when rendering the view
			false, true
		);

	}

	public function actionView($id)
	{
		if (!$this->event = Event::model()->findByPk($id)) {
			throw new CHttpException(403, 'Invalid event id.');
		}
		$this->patient = $this->event->episode->patient;
		$this->event_type = EventType::model()->findByPk($this->event->event_type_id);
		$this->episode = $this->event->episode;

		$elements = $this->getDefaultElements('view');

		// Decide whether to display the 'edit' button in the template
		if ($this->editable) {
			if (!BaseController::checkUserLevel(4) || (!$this->event->episode->firm && !$this->event->episode->support_services)) {
				$this->editable = false;
			} else {
				if ($this->firm->serviceSubspecialtyAssignment) {
					if ($this->event->episode->firm && $this->firm->serviceSubspecialtyAssignment->subspecialty_id != $this->event->episode->firm->serviceSubspecialtyAssignment->subspecialty_id) {
						$this->editable = false;
					}
				} else {
					if ($this->event->episode->firm !== null) {
						$this->editable = false;
					}
				}
			}
		}
		// Allow elements to override the editable status
		if ($this->editable) {
			foreach ($elements as $element) {
				if (!$element->isEditable()) {
					$this->editable = false;
					break;
				}
			}
		}

		$this->logActivity('viewed event');

		$this->event->audit('event','view',false);

		$this->title = $this->event_type->name;
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
		if ($this->event->canDelete()) {
			$this->event_actions = array(
					EventAction::link('Delete',
							Yii::app()->createUrl($this->event->eventType->class_name.'/default/delete/'.$this->event->id),
							array('colour' => 'red', 'level' => 'secondary'),
							array('class' => 'trash')
					)
			);
		}

		$this->processJsVars();
		$this->renderPartial(
			'view', array_merge(array(
			'elements' => $elements,
			'eventId' => $id,
			), $this->extraViewProperties), false, true);
	}

	public function actionUpdate($id)
	{
		if (!$this->event = Event::model()->findByPk($id)) {
			throw new CHttpException(403, 'Invalid event id.');
		}

		// Check the user's firm is of the correct subspecialty to have the
		// rights to update this event
		if ($this->firm->serviceSubspecialtyAssignment && $this->firm->serviceSubspecialtyAssignment->subspecialty_id != $this->event->episode->firm->serviceSubspecialtyAssignment->subspecialty_id) {
			throw new CHttpException(403, 'The firm you are using is not associated with the subspecialty for this event.');
		} elseif (!$this->firm->serviceSubspecialtyAssignment && $this->event->episode->firm !== null) {
			throw new CHttpException(403, 'The firm you are using is not a support services firm.');
		}

		$this->event_type = EventType::model()->findByPk($this->event->event_type_id);
		$this->patient = $this->event->episode->patient;
		$this->episode = $this->event->episode;

		// firm changing sanity
		if (!empty($_POST) && !empty($_POST['firm_id']) && $_POST['firm_id'] != $this->firm->id) {
			// The firm id in the firm is not the same as the session firm id, e.g. they've changed
			// firms in a different tab. Set the session firm id to the provided firm id.

			$session = Yii::app()->session;

			$firms = $session['firms'];
			$firmId = intval($_POST['firm_id']);

			if ($firms[$firmId]) {
				$session['selected_firm_id'] = $firmId;
				$this->selectedFirmId = $firmId;
				$this->firm = Firm::model()->findByPk($this->selectedFirmId);
			} else {
				// They've supplied a firm id in the post to which they are not entitled??
				throw new Exception('Invalid firm id on attempting to update event.');
			}
		}

		if (empty($_POST) && !count($this->getDefaultElements($this->action->id))) {
			throw new CHttpException(403, 'Gadzooks!	I got me no elements!');
		}

		if (!empty($_POST) && isset($_POST['cancel'])) {
			// Cancel button pressed, so just bounce to view
			$this->redirect(array('default/view/'.$this->event->id));
			return;
		} elseif (!empty($_POST) && !count($this->getDefaultElements($this->action->id))) {
			$errors['Event'][] = 'No elements selected';
		} elseif (!empty($_POST)) {

			$elements = array();
			$to_delete = array();
			foreach (ElementType::model()->findAll('event_type_id=?',array($this->event_type->id)) as $element_type) {
				$class_name = $element_type->class_name;
				if (isset($_POST[$class_name])) {
					if ($element = $class_name::model()->find('event_id=?',array($this->event->id))) {
						// Add existing element to array
						$elements[] = $element;
					} else {
						// Add new element to array
						$elements[] = new $class_name;
					}
				} elseif ($element = $class_name::model()->find('event_id=?',array($this->event->id))) {
					// Existing element is not posted, so we need to delete it
					$to_delete[] = $element;
				}
			}

			// validation
			$errors = $this->validatePOSTElements($elements);


			// creation
			if (empty($errors)) {

				// Need to pass through _all_ elements to updateElements (those not in _POST will be deleted)
				$all_elements = array_merge($elements, $to_delete);
				$success = $this->updateElements($all_elements, $_POST, $this->event);

				if ($success) {
					$info_text = '';
					foreach ($elements as $element) {
						if ($element->infotext) {
							$info_text .= $element->infotext;
						}
					}

					$this->logActivity('updated event');

					$audit_data = array('event' => $this->event->getAuditAttributes());

					foreach ($elements as $element) {
						$audit_data[get_class($element)] = $element->getAuditAttributes();
					}

					$this->event->audit('event','update',serialize($audit_data));

					// Update event to indicate user has made a change
					// $this->event->datetime = date("Y-m-d H:i:s");
					$this->event->user = Yii::app()->user->id;
					$this->event->info = $info_text;

					if (!$this->event->save()) {
						throw new SystemException('Unable to update event: '.print_r($this->event->getErrors(),true));
					}

					OELog::log("Updated event {$this->event->id}");

					$this->redirect(array('default/view/'.$this->event->id));
					return;
				}
			}
		}

		$this->editing = true;
		$this->title = 'Update';
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
						array('colour' => 'red', 'level' => 'secondary')
				)
		);

		$this->processJsVars();
		$this->renderPartial(
			$this->action->id,
			array(
				'elements' => $this->getDefaultElements($this->action->id),
				'errors' => @$errors
			),
			// processOutput is true so that the css/javascript from the event_header.php are processed when rendering the view
			false, true
		);
	}

	/*
	 * Use this for any many to many relations defined on your elements. This is called prior to validation
	 * so should set values without actually touching the database. To do that, the createElements and updateElements
	 * methods should be extended to handle the POST values.
	 */
	protected function setPOSTManyToMany($element)
	{
		// placeholder function
	}

	/*
	 * Uses the POST values to define elements and their field values without hitting the db, and then performs validation
	 */
	protected function validatePOSTElements($elements)
	{
		$errors = array();
		foreach ($elements as $element) {
			$elementClassName = get_class($element);
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

		return $errors;
	}

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

	public function renderOptionalElements($action, $form=false,$data=false)
	{
		foreach ($this->getOptionalElements($action) as $element) {
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

	public function header($editable=null)
	{
		$episodes = $this->patient->episodes;
		$ordered_episodes = $this->patient->getOrderedEpisodes();
		/*
		$ordered_episodes = array();
		foreach ($episodes as $ep) {
			$ordered_episodes[$ep->firm->serviceSubspecialtyAssignment->subspecialty->specialty->name][] = $ep;
		}
		*/
		$legacyepisodes = $this->patient->legacyepisodes;
		$supportserviceepisodes = $this->patient->supportserviceepisodes;

		if ($editable === null) {
			if (isset($this->event)) {
				$editable = $this->event->editable;
			} else {
				$editable = false;
			}
		}

		$this->renderPartial('//patient/event_header',array(
			'ordered_episodes'=>$ordered_episodes,
			'legacyepisodes'=>$legacyepisodes,
			'supportserviceepisodes'=>$supportserviceepisodes,
			'eventTypes'=>EventType::model()->getEventTypeModules(),
			'model'=>$this->patient,
			'editable'=>$editable,
		));
	}

	public function footer()
	{
		$episodes = $this->patient->episodes;
		$legacyepisodes = $this->patient->legacyepisodes;
		$supportserviceepisodes = $this->patient->supportserviceepisodes;

		$this->renderPartial('//patient/event_footer',array(
			'episodes'=>$episodes,
			'legacyepisodes'=>$legacyepisodes,
			'supportserviceepisodes'=>$supportserviceepisodes,
			'eventTypes'=>EventType::model()->getEventTypeModules()
		));
	}

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
					$element->attributes = Helper::convertNHS2MySQL($data[$elementClassName]);
				}

				$this->setPOSTManyToMany($element);

				if (!$element->validate()) {
					$valid = false;
				} else {
					$elementsToProcess[] = $element;
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
	 * @param array		$elements		array of SiteElementTypes
	 * @param array		$data			$_POST data to update
	 * @param object $event				the associated event
	 *
	 * @return boolean $success		true if all elements suceeded, false otherwise
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
				$element->attributes = Helper::convertNHS2MySQL($data[$elementClassName]);

				$toSave[] = $element;

				$needsValidation = true;
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

	public function getEpisode($firm, $patientId)
	{
		if ($firm->service_subspecialty_assignment_id) {
			$subspecialtyId = $firm->serviceSubspecialtyAssignment->subspecialty->id;
			return Episode::model()->getBySubspecialtyAndPatient($subspecialtyId, $patientId);
		}
		return Episode::model()->find('patient_id=? and support_services=?',array($patientId,1));
	}

	public function getOrCreateEpisode($firm, $patientId)
	{
		if (!$episode = $this->getEpisode($firm, $patientId)) {
			$episode = Patient::model()->findByPk($patientId)->addEpisode($firm);
		}

		return $episode;
	}

	public function createEvent($episode, $userId, $eventTypeId, $elementsToProcess)
	{
		$info_text = '';

		foreach ($elementsToProcess as $element) {
			if ($element->infotext) {
				$info_text .= $element->infotext;
			}
		}

		$event = new Event();
		$event->episode_id = $episode->id;
		$event->event_type_id = $eventTypeId;
		$event->datetime = date("Y-m-d H:i:s");
		$event->info = $info_text;

		if (!$event->save()) {
			OELog::log("Failed to creat new event for episode_id=$episode->id, event_type_id=$eventTypeId, datetime='$event->datetime'");
			throw new Exception('Unable to save event.');
		}

		OELog::log("Created new event for episode_id=$episode->id, event_type_id=$eventTypeId, datetime='$event->datetime'");

		return $event;
	}

	public function displayErrors($errors)
	{
		$this->renderPartial('//elements/form_errors',array('errors'=>$errors));
	}

	/**
	 * Print action
	 * @param integer $id event id
	 */
	public function actionPrint($id)
	{
		$this->printInit($id);
		$elements = $this->getDefaultElements('print');
		$pdf = (isset($_GET['pdf']) && $_GET['pdf']);
		$this->printLog($id, $pdf);
		if ($pdf) {
			$this->printPDF($id, $elements);
		} else {
			$this->printHTML($id, $elements);
		}
	}

	/**
	 * Initialise print action
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
		$this->title = $this->event_type->name;
	}

	/**
	 * Render HTML
	 * @param integer $id event id
	 * @param array $elements
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
	 * Render PDF
	 * @param integer $id event id
	 * @param array $elements
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

	public function actionDelete($id)
	{
		if (!$this->event = Event::model()->findByPk($id)) {
			throw new CHttpException(403, 'Invalid event id.');
		}

		// Only the event creator can delete the event, and only 24 hours after its initial creation
		if (!$this->event->canDelete()) {
			$this->redirect(array('default/view/'.$this->event->id));
			return false;
		}

		if (!empty($_POST)) {
			$this->event->deleted = 1;
			if (!$this->event->save()) {
				throw new Exception("Unable to mark event deleted: ".print_r($this->event->getErrors(),true));
			}

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
		$this->renderPartial(
			'delete', array(
			'eventId' => $id,
			), false, true);

		return false;
	}

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

		return parent::processJsVars();
	}
}
