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

class ClinicalController extends BaseController
{
	public $layout = '//layouts/patientMode/column2';
	public $episodes;
	public $eventTypes;
	public $service;
	public $firm;
	public $patient;
	public $nopost = false;
	public $editing;
	public $editable;
	public $event;
	public $title;

	public function init()
	{
		parent::init();
		// FIXME: this is a hack to enable things to continue working until we can call eg: /modulename/create
		foreach (EventType::model()->getEventTypeModules() as $module) {
			if ($module != 'OphTrOperation') {
				Yii::import('application.modules.'.$module->class_name.'.*');
				Yii::import('application.modules.'.$module->class_name.'.models.*');
				Yii::import('application.modules.'.$module->class_name.'.views.*');
			}
		}
	}

	public function filters()
	{
		return array('accessControl');
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'users' => array('@')
			),
			// non-logged in can't view anything
			array('deny',
				'users' => array('?')
			),
		);
	}

	protected function beforeAction($action) {
		// Prevent jquery + other js that might conflict getting loaded twice on ajax calls
		if (Yii::app()->getRequest()->getIsAjaxRequest()) {
			Yii::app()->clientScript->scriptMap = array(
				'jquery.js' => false,
				'jquery.min.js' => false,
				'jquery-ui.js' => false,
				'jquery-ui.min.js' => false,
			);
		}

		// Sample code to be used when RBAC is fully implemented.
		/*
		if (!Yii::app()->user->checkAccess('admin')) {
			throw new CHttpException(403, 'You are not authorised to perform this action.');
		}
		*/

		$this->storeData();

		return parent::beforeAction($action);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		if (!$this->event = Event::model()->findByPk($id)) {
			throw new CHttpException(403, 'Invalid event id.');
		}

		$elements = $this->service->getDefaultElements('view', $this->event);

		// Decide whether to display the 'edit' button in the template
		if (!$this->event->episode->firm) {
			$editable = false;
		} else {
			if ($this->firm->serviceSubspecialtyAssignment->subspecialty_id !=
				$this->event->episode->firm->serviceSubspecialtyAssignment->subspecialty_id) {
				$editable = false;
			} else {
				$editable = true;
			}
		}
		$currentSite = Site::model()->findByPk(Yii::app()->request->cookies['site_id']->value);

		$audit = new Audit;
		$audit->action = "view";
		$audit->target_type = "event (route 2)";
		$audit->event_id = $this->event->id;
		$audit->patient_id = $this->patient->id;
		$audit->episode_id = $this->episode->id;
		$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
		$audit->save();

		// this shouldn't get called
		$this->logActivity('viewed event');

		$this->renderPartial(
			$this->getTemplateName('view', $this->event->event_type_id), array(
			'elements' => $elements,
			'eventId' => $id,
			'editable' => $editable,
			'currentSite' => $currentSite
			), false, true);
	}

	public function actionIndex()
	{
		// this shouldn't get called
		$this->logActivity('viewed patient index');

		$this->render('index');
	}

	/**
	 * Creates a new event.
	 */
	public function actionCreate()
	{
		if (!isset($_GET['event_type_id'])) {
			throw new CHttpException(403, 'No event_type_id specified.');
		}

		$eventTypeId = $_GET['event_type_id'];

		$eventType = EventType::model()->findByPk($eventTypeId);

		if (!isset($eventType)) {
			throw new CHttpException(403, 'Invalid event_type_id.');
		}

		if (!$this->patient = Patient::model()->findByPk($_REQUEST['patient_id'])) {
			throw new CHttpException(403, 'Invalid patient_id.');
		}

		if ($_POST && $_POST['action'] == 'create' && !empty($_POST['firm_id']) && $_POST['firm_id'] != $this->firm->id) {
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
				throw new Exception('Invalid firm id on attempting to create event.');
			}
		}

		$elements = $this->service->getDefaultElements('create',false,$eventType->id);

		if (!count($elements)) {
			throw new CHttpException(403, 'That combination event type and firm subspecialty is not defined.');
		}

		$specialties = Subspecialty::model()->findAll();

		if ($_POST && $_POST['action'] == 'create') {
			if (isset($_POST['cancelOperation'])) {
				$this->redirect(array('patient/episodes/'.$this->patient->id));
				return;
			}

			$errors = array();
			$elementList = array();
			foreach ($elements as $element) {
				$elementClassName = get_class($element);
				$element->attributes = Helper::convertNHS2MySQL($_POST[$elementClassName]);
				$elementList[] = $element;
				if (!$element->validate()) {
					foreach ($element->getErrors() as $errormsgs) {
						foreach ($errormsgs as $error) {
							$index = preg_replace('/^Element/','',$elementClassName);
							$errors[$index][] = $error;
						}
					}
				}
			}

			if (empty($errors)) {
				// The user has submitted the form to create the event
				$eventId = $this->service->createElements(
					$elements, $_POST, $this->firm, $this->patient->id, $this->getUserId(), $eventType->id
				);

				$event_info = '';

				foreach ($elements as $element) {
					if ($element->infotext) {
						$event_info .= $element->infotext;
					}
				}

				if ($eventId) {
					$event = Event::model()->findByPk($eventId);

					if ($event_info) {
						$event->info = $event_info;
						if (!$event->save()) {
							throw new SystemException('Unable to create event: '.print_r($event->getErrors(),true));
						}
					}

					$this->logActivity('created event.');

					$audit_data = array('event' => $event->getAuditAttributes());

					foreach ($elements as $element) {
						$audit_data[get_class($element)] = $element->getAuditAttributes();
					}

					$audit = new Audit;
					$audit->action = "create";
					$audit->target_type = "event";
					$audit->patient_id = $this->patient->id;
					$audit->episode_id = $event->episode_id;
					$audit->event_id = $event->id;
					$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
					$audit->data = serialize($audit_data);
					$audit->save();

					$episode = Episode::model()->findByPk($event->episode_id);
					$episode->episode_status_id = 3;

					if (!$episode->save()) {
						throw new Exception('Unable to save episode status for episode '.$episode->id);
					}

					$eventTypeName = ucfirst($eventType->name);
					Yii::app()->user->setFlash('success', "{$eventTypeName} created.");
					if (isset($_POST['scheduleNow'])) {
						$operation = ElementOperation::model()->findByAttributes(array('event_id' => $eventId));
						$this->redirect(array('booking/schedule', 'operation' => $operation->id));
					} else {
						$this->redirect(array('patient/event/'.$eventId));
					}

					return;
				}
			}

			// If we get here element validation and failed and the array of elements will
			// be displayed again in the call below
		}

		$params = array(
			'elements' => $elements,
			'eventTypeId' => $eventTypeId,
			'eventType' => $eventType,
			'specialties' => $specialties,
			'firm' => $this->firm
		);

		if (isset($errors)) {
			$params['errors'] = $errors;
		}

		$this->editable = false;
		$this->title = 'Create';

		$this->renderPartial(
			$this->getTemplateName('create', $eventTypeId),
			$params,
			false,
			true
		);
	}

	/**
	 * Updates an event.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		if (!$event = Event::model()->findByPk($id)) {
			throw new CHttpException(403, 'Invalid event id.');
		}

		// Check the user's firm is of the correct subspecialty to have the
		// rights to update this event
		if ($this->firm->serviceSubspecialtyAssignment->subspecialty_id !=
			$event->episode->firm->serviceSubspecialtyAssignment->subspecialty_id) {
			throw new CHttpException(403, 'The firm you are using is not associated with the subspecialty for this event.');
		}

		$elements = $this->service->getDefaultElements('update',$event);

		if (!count($elements)) {
			throw new CHttpException(403, 'That combination event type and firm subspecialty is not defined.');
		}

		$specialties = Subspecialty::model()->findAll();

		$episode = Episode::model()->findByPk($event->episode_id);
		$this->patient = Patient::model()->findByPk($episode->patient_id);

		if ($_POST && $_POST['action'] == 'update') {
			if (isset($_POST['cancelOperation'])) {
				$this->redirect(array('patient/event/'.$event->id));
				return;
			}

			// TODO: This appears to overlap with the service->updateElements functionality
			// and probably needs rationalising
			$errors = array();
			$elementList = array();
			foreach ($elements as $element) {
				$elementClassName = get_class($element);
				$element->attributes = Helper::convertNHS2MySQL($_POST[$elementClassName]);
				$elementList[] = $element;
				if (!$element->validate()) {
					foreach ($element->getErrors() as $errormsgs) {
						foreach ($errormsgs as $error) {
							$index = preg_replace('/^Element/','',$elementClassName);
							$errors[$index][] = $error;
						}
					}
				}
			}

			if (empty($errors)) {
				$success = $this->service->updateElements($elements, $_POST, $event);

				if ($success) {
					$event_info = '';

					foreach ($elements as $element) {
						if ($element->infotext) {
							$event_info .= $element->infotext;
						}
					}

					if ($event_info) {
						$event->info = $event_info;
						if (!$event->save()) {
							throw new SystemException('Unable to update event: '.print_r($event->getErrors(),true));
						}
					}

					$this->logActivity('updated event');

					$audit_data = array('event' => $event->getAuditAttributes());

					foreach ($elements as $element) {
						$audit_data[get_class($element)] = $element->getAuditAttributes();
					}

					$audit = new Audit;
					$audit->action = "update";
					$audit->target_type = "event";
					$audit->patient_id = $this->patient->id;
					$audit->episode_id = $event->episode_id;
					$audit->event_id = $event->id;
					$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
					$audit->data = serialize($audit_data);
					$audit->save();

					// Update event to indicate user has made a change
					$event->datetime = date("Y-m-d H:i:s");
					$event->user = $this->getUserId();
					if (!$event->save()) {
						throw new SystemException('Unable to update event: '.print_r($event->getErrors(),true));
					}

					OELog::log("Updated event $event->id");

					$this->redirect(array('patient/event/'.$event->id));
					return;
				}
			}

			// If we get this far element validation has failed, so we render them again.
			// The validation process will have populated and error messages.
		}

		$params = array(
			'id' => $id,
			'elements' => $elements,
			'specialties' => $specialties,
			'event' => $event,
		);

		if (isset($errors)) {
			$params['errors'] = $errors;
		}

		$this->event = $event;
		$this->editing = TRUE;

		$this->title = 'Update';

		$this->renderPartial(
			$this->getTemplateName('update', $event->event_type_id),
			$params,
			false,
			true
		);
	}

	/**
	 * Displays the patient summary.
	 *
	 * @param int $id
	 */
	public function actionEpisodeSummary($id)
	{
		$episode = Episode::model()->findByPk($id);

		if (!isset($episode)) {
			throw new CHttpException(403, 'Invalid episode id.');
		}

		// Decide whether to display the 'edit' button in the template
		if (!$episode->firm) {
			$editable = false;
		} else {
			if ($this->firm->serviceSubspecialtyAssignment->subspecialty_id != $episode->firm->serviceSubspecialtyAssignment->subspecialty_id) {
				$editable = false;
			} else {
				$editable = true;
			}
		}
		$audit = new Audit;
		$audit->action = "view";
		$audit->target_type = "episode summary (route 2)";
		$audit->patient_id = $this->patient->id;
		$audit->episode_id = $episode->id;
		$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
		$audit->save();

		$this->renderPartial('episodeSummary', array('episode' => $episode, 'editable' => $editable), false, true);
	}

	/**
	 * Displays the extra episode summary data, if any, for the episode
	 *
	 * @param int $id
	 */
	public function actionSummary($id)
	{
		$episode = Episode::model()->findByPk($id);

		if (!isset($episode)) {
			throw new CHttpException(403, 'Invalid episode id.');
		}

		if (!isset($_GET['summary'])) {
			throw new CHttpException(403, 'No summary.');
		}

		// Decide whether to display the 'edit' button in the template
		if ($this->firm->serviceSubspecialtyAssignment->subspecialty_id !=
			$episode->firm->serviceSubspecialtyAssignment->subspecialty_id) {
			$editable = false;
		} else {
			$editable = true;
		}

		$this->logActivity('viewed patient summary');

		$audit = new Audit;
		$audit->action = "view";
		$audit->target_type = "episode summary (route 3)";
		$audit->patient_id = $episode->patient_id;
		$audit->episode_id = $episode->id;
		$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
		$audit->save();

		$this->renderPartial('summary', array(
			'episode' => $episode,
			'summary' => $_GET['summary'],
			'editable' => $editable
			), false, true
		);
	}

	/**
	 * Sets arrays of episodes and eventTypes for use by the clinical base.php view.
	 */
	public function listEpisodesAndEventTypes()
	{
		$this->service = new ClinicalService;
		if(isset($_REQUEST['patient_id'])) {
			$patient = Patient::model()->findByPk($_REQUEST['patient_id']);
			$this->episodes = $patient->episodes;
		}

		$this->eventTypes = EventType::model()->getEventTypeModules();
	}

	/**
	 * Returns the logged in user's id. Needed for unit tests.
	 *
	 * @return int
	 */
	public function getUserId()
	{
		return Yii::app()->user->id;
	}

	public function storeData()
	{
		parent::storeData();

		// Get the firm currently associated with this user
		$this->firm = Firm::model()->findByPk($this->selectedFirmId);

		if (!isset($this->firm)) {
			// No firm selected, reject
			throw new CHttpException(403, 'You are not authorised to view this page without selecting a firm.');
		}

		// Gets the list of episodes and events for this patient
		$this->listEpisodesAndEventTypes();
	}

	public function getTemplateName($action, $eventTypeId)
	{
		$template = 'eventTypeTemplates' . DIRECTORY_SEPARATOR . $action . DIRECTORY_SEPARATOR . $eventTypeId;

		if (!file_exists(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'clinical' . DIRECTORY_SEPARATOR . $template . '.php')) {
			$template = $action;
		}

		return $template;
	}

	public function actionCloseEpisode($id)
	{
		$episode = Episode::model()->findByPk($id);

		if (!isset($episode)) {
			throw new CHttpException(403, 'Invalid episode id.');
		}

		// Decide whether to display the 'edit' button in the template
		if ($this->firm->serviceSubspecialtyAssignment->subspecialty_id !=
			$episode->firm->serviceSubspecialtyAssignment->subspecialty_id) {
			$editable = false;
		} else {
			$editable = true;
		}

		$episode->end_date = date('Y-m-d H:i:s');
		if (!$episode->save(false)) {
			throw new SystemException('Unable to save episode: '.print_r($episode->getErrors(),true));
		}

		OELog::log("Closed episode $episode->id");

		$audit = new Audit;
		$audit->action = "close";
		$audit->target_type = "episode";
		$audit->patient_id = $episode->patient_id;
		$audit->episode_id = $episode->id;
		$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
		$audit->save();

		$this->renderPartial('episodeSummary', array('episode' => $episode, 'editable' => $editable), false, true);
	}

	public function header($passthru=false) {
		$ordered_episodes = $this->patient->getOrderedEpisodes();
		$legacyepisodes = $this->patient->legacyepisodes;

		$params = array(
			'ordered_episodes'=>$ordered_episodes,
			'legacyepisodes'=>$legacyepisodes,
			'eventTypes'=>EventType::model()->getEventTypeModules(),
			'title'=>'Create'
		);

		if (is_array($passthru)) {
			$params = array_merge($params,$passthru);
		}

		$this->renderPartial('//patient/event_header',$params);
	}

	public function footer($editable=false,$passthru=false) {
		$episodes = $this->patient->episodes;

		$params = array(
			'episodes'=>$episodes,
			'eventTypes'=>EventType::model()->getEventTypeModules(),
			'editable'=>$editable
		);

		if (is_array($passthru)) {
			$params = array_merge($params,$passthru);
		}

		$this->renderPartial('//patient/event_footer',$params);
	}

	public function actionDeleteevent($id) {
		$errors = array();

		if (!$event = Event::model()->findByPk($id)) {
			throw new CHttpException(500,'Event not found: '.$id);
		}

		// Only the event creator can delete the event, and only 24 hours after its initial creation
		if ($event->created_user_id != Yii::app()->session['user']->id || (time() - strtotime($event->created_date)) > 86400) {
			return $this->redirect(array('patient/event/'.$event->id));
		}

		if (!empty($_POST) && @$_POST['event_id'] == $event->id) {
			$event->deleted = 1;
			if (!$event->save()) {
				throw new Exception("Unable to mark event $event->id as deleted: ".print_r($event->getErrors(),true));
			}

			$audit = new Audit;
			$audit->action = "delete";
			$audit->target_type = "event";
			$audit->patient_id = $event->episode->patient->id;
			$audit->episode_id = $event->episode_id;
			$audit->event_id = $event->id;
			$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
			$audit->save();

			// If the episode has no other events, mark it as deleted
			if (empty($event->episode->events)) {
				$episode = $event->episode;
				$episode->deleted = 1;
				if (!$episode->save()) {
					throw new Exception("Unable to mark episode $episode->id as deleted: ".print_r($episode->getErrors(),true));
				}

				$audit = new Audit;
				$audit->action = "delete";
				$audit->target_type = "episode";
				$audit->patient_id = $episode->patient_id;
				$audit->episode_id = $episode->id;
				$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
				$audit->save();
			}

			$this->redirect(array('patient/episodes/'.$event->episode->patient->id));
		}

		$this->patient = $event->episode->patient;
		$this->title = 'Delete event';

		$this->renderPartial('/clinical/deleteEvent', array(
				'event' => $event,
				'errors' => $errors
			), false, true);
	}

	/**
	 * Get all the elements for a the current module's event type
	 *
	 * @param $event_type_id
	 * @return array
	 */
	public function getDefaultElements($action, $event_type_id=false, $event=false) {
		$etc = new BaseEventTypeController(1);
		$etc->event = $event;
		return $etc->getDefaultElements($action, $event_type_id);
	}

	/**
	 * Get the optional elements for the current module's event type
	 * This will be overriden by the module
	 *
	 * @param $event_type_id
	 * @return array
	 */
	public function getOptionalElements($action, $event=false) {
		return array();
	}
}
