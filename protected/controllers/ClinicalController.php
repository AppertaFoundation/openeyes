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
	public $model;
	public $nopost = false;

	public function init()
	{
		// FIXME: this is a hack to enable things to continue working until we can call eg: /modulename/create
		foreach (Yii::app()->params['enabled_modules'] as $module) {
			if ($module != 'OphTrOperation') {
				Yii::import('application.modules.'.$module.'.*');
				Yii::import('application.modules.'.$module.'.models.*');
				Yii::import('application.modules.'.$module.'.views.*');
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
		$event = Event::model()->findByPk($id);

		if (!isset($event)) {
			throw new CHttpException(403, 'Invalid event id.');
		}

		$elements = $this->service->getDefaultElements($event);

		// Decide whether to display the 'edit' button in the template
		if ($this->firm->serviceSubspecialtyAssignment->subspecialty_id !=
			$event->episode->firm->serviceSubspecialtyAssignment->subspecialty_id) {
			$editable = false;
		} else {
			$editable = true;
		}

		$currentSite = Site::model()->findByPk(Yii::app()->request->cookies['site_id']->value);

		$this->logActivity('viewed event');

		$this->renderPartial(
			$this->getTemplateName('view', $event->event_type_id), array(
			'elements' => $elements,
			'eventId' => $id,
			'editable' => $editable,
			'currentSite' => $currentSite
			), false, true);
	}

	public function actionIndex()
	{
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

		if (!$patient = Patient::model()->findByPk($_REQUEST['patient_id'])) {
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

		$elements = $this->service->getDefaultElements(false,$eventType->id);

		if (!count($elements)) {
			throw new CHttpException(403, 'That combination event type and firm subspecialty is not defined.');
		}

		$specialties = Subspecialty::model()->findAll();

		if ($_POST && $_POST['action'] == 'create') {
			if (isset($_POST['cancelOperation'])) {
				$this->redirect(array('patient/episodes/'.$patient->id));
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
					$elements, $_POST, $this->firm, $patient->id, $this->getUserId(), $eventType->id
				);

				if ($eventId) {
					$this->logActivity('created event.');

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
			'patient' => $patient,
			'firm' => $this->firm
		);

		if (isset($errors)) {
			$params['errors'] = $errors;
		}

		if ($eventType->name == 'Operation') {
			$subspecialty = $this->firm->serviceSubspecialtyAssignment->subspecialty;
			$subsections = SubspecialtySubsection::model()->getList($subspecialty->id);
			$procedures = array();
			if (empty($subsections)) {
				$procedures = Procedure::model()->getListBySubspecialty($subspecialty->id);
			}

			$params['subspecialty'] = $subspecialty;
			$params['subsections'] = $subsections;
			$params['procedures'] = $procedures;
		}

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
		$event = Event::model()->findByPk($id);

		if (!isset($event)) {
			throw new CHttpException(403, 'Invalid event id.');
		}

		// Check the user's firm is of the correct subspecialty to have the
		// rights to update this event
		if ($this->firm->serviceSubspecialtyAssignment->subspecialty_id !=
			$event->episode->firm->serviceSubspecialtyAssignment->subspecialty_id) {
			throw new CHttpException(403, 'The firm you are using is not associated with the subspecialty for this event.');
		}

		$elements = $this->service->getDefaultElements($event);

		if (!count($elements)) {
			throw new CHttpException(403, 'That combination event type and firm subspecialty is not defined.');
		}

		$specialties = Subspecialty::model()->findAll();

		$episode = Episode::model()->findByPk($event->episode_id);
		$patient = Patient::model()->findByPk($episode->patient_id);

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
					$this->logActivity('updated event');

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
			'patient' => $patient,
			'event' => $event,
		);

		if (isset($errors)) {
			$params['errors'] = $errors;
		}

		if ($event->eventType->name == 'Operation') {
			$subspecialty = $this->firm->serviceSubspecialtyAssignment->subspecialty;
			$subsections = SubspecialtySubsection::model()->getList($subspecialty->id);
			$procedures = array();
			if (empty($subsections)) {
				$procedures = Procedure::model()->getListBySubspecialty($subspecialty->id);
			}

			$params['subspecialty'] = $subspecialty;
			$params['subsections'] = $subsections;
			$params['procedures'] = $procedures;
		}

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
		if ($this->firm->serviceSubspecialtyAssignment->subspecialty_id !=
			$episode->firm->serviceSubspecialtyAssignment->subspecialty_id) {
			$editable = false;
		} else {
			$editable = true;
		}

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

		if (!Yii::app()->params['enabled_modules'] || !is_array(Yii::app()->params['enabled_modules'])) {
			$this->eventTypes = array();
		} else {
			$this->eventTypes = EventType::model()->findAll("class_name in ('".implode("','",Yii::app()->params['enabled_modules'])."')");
		}
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

		$this->renderPartial('episodeSummary', array('episode' => $episode, 'editable' => $editable), false, true);
	}

	public function header($editable=false, $passthru=false) {
		if (!$patient = $this->model = Patient::Model()->findByPk($_GET['patient_id'])) {
			throw new SystemException('Patient not found: '.$_GET['patient_id']);
		}
		$episodes = $patient->episodes;

		if (!Yii::app()->params['enabled_modules'] || !is_array(Yii::app()->params['enabled_modules'])) {
			$eventTypes = array();
		} else {
			$eventTypes = EventType::model()->findAll("class_name in ('".implode("','",Yii::app()->params['enabled_modules'])."')");
		}

		$params = array(
			'episodes'=>$episodes,
			'eventTypes'=>$eventTypes,
			'title'=>'Create',
			'model'=>$patient,
			'editable'=>$editable
		);

		if (is_array($passthru)) {
			$params = array_merge($params,$passthru);
		}

		$this->renderPartial('//patient/event_header',$params);
	}

	public function footer($editable=false,$passthru=false) {
		if (!$patient = $this->model = Patient::Model()->findByPk($_GET['patient_id'])) {
			throw new SystemException('Patient not found: '.$_GET['patient_id']);
		}
		$episodes = $patient->episodes;

		if (!Yii::app()->params['enabled_modules'] || !is_array(Yii::app()->params['enabled_modules'])) {
			$eventTypes = array();
		} else {
			$eventTypes = EventType::model()->findAll("class_name in ('".implode("','",Yii::app()->params['enabled_modules'])."')");
		}

		$params = array(
			'episodes'=>$episodes,
			'eventTypes'=>$eventTypes,
			'editable'=>$editable
		);

		if (is_array($passthru)) {
			$params = array_merge($params,$passthru);
		}

		$this->renderPartial('//patient/event_footer',$params);
	}

	/**
	 * Get all the elements for a the current module's event type
	 *
	 * @param $event_type_id
	 * @return array
	 */
	public function getDefaultElements($event=false, $event_type_id=false) {
		$etc = new BaseEventTypeController(1);
		return $etc->getDefaultElements($event, $event_type_id);
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
