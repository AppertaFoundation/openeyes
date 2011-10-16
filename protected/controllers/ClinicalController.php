<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

class ClinicalController extends BaseController
{
	public $layout = '//layouts/patientMode/column2';
	public $episodes;
	public $eventTypes;
	public $service;
	public $firm;

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

	protected function beforeAction($action)
	{
		// Sample code to be used when RBAC is fully implemented.
		// if (!Yii::app()->user->checkAccess('admin')) {
		// 	throw new CHttpException(403, 'You are not authorised to perform this action.');
		// }

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

		// Check the patient id for this event is the same as the session patient id
		if ($event->episode->patient->id != Yii::app()->session['patient_id']) {
			$this->resetSessionPatient($event->episode->patient->id);
		}

		// The eventType, firm and patient are fetched from the event object
		$elements = $this->service->getElements(
			null, null, null, $this->getUserId(), $event
		);

		// Decide whether to display the 'edit' button in the template
		if ($this->firm->serviceSpecialtyAssignment->specialty_id !=
			$event->episode->firm->serviceSpecialtyAssignment->specialty_id) {
			$editable = false;
		} else {
			$editable = true;
		}

		$this->logActivity('viewed event');

		$this->renderPartial(
			$this->getTemplateName('view', $event->event_type_id), array(
			'elements' => $elements,
			'eventId' => $id,
			'editable' => $editable
			), false, true);
	}

	public function actionIndex()
	{
		$this->logActivity('viewed patient index');

		if (Yii::app()->params['use_pas']) {
			$patient = Patient::model()->findByPk($this->patientId);
			$referralService = new ReferralService;
			$referralService->search($patient->pas_key);
		}

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

		// Check the patient id for this new event is the same as the session patient id
		if (!empty($_REQUEST['patient_id']) && $_REQUEST['patient_id'] != Yii::app()->session['patient_id']) {
			$this->resetSessionPatient($_REQUEST['patient_id']);
		}

		if ($_POST && $_POST['action'] == 'create' && $_POST['firm_id'] != $this->firm->id) {
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

		$elements = $this->service->getElements(
			$eventType, $this->firm, $this->patientId, $this->getUserId()
		);

		if (!count($elements)) {
			throw new CHttpException(403, 'That combination event type and firm specialty is not defined.');
		}

		$specialties = Specialty::model()->findAll();

		$patient = Patient::model()->findByPk($this->patientId);

		if ($_POST && $_POST['action'] == 'create') {
			if (Yii::app()->getRequest()->getIsAjaxRequest()) {
				$valid = true;
				$elementList = array();
				foreach ($elements as $element) {
					$elementClassName = get_class($element);
					$element->attributes = $_POST[$elementClassName];
					$elementList[] = $element;
					if (!$element->validate()) {
						$valid = false;
					}
				}
				if (!$valid) {
					echo CActiveForm::validate($elementList);
					Yii::app()->end();
				}
			}

			// The user has submitted the form to create the event
			$eventId = $this->service->createElements(
				$elements, $_POST, $this->firm, $this->patientId, $this->getUserId(), $eventType->id
			);

			if ($eventId) {
				$this->assignReferralIfRequired($eventId, $this->firm, $this->patientId);

				$this->logActivity('created event.');

				$eventTypeName = ucfirst($eventType->name);
				Yii::app()->user->setFlash('success', "{$eventTypeName} created.");
				if (!empty($_POST['scheduleNow'])) {
					$operation = ElementOperation::model()->findByAttributes(array('event_id' => $eventId));
					$this->redirect(array('booking/schedule', 'operation' => $operation->id));
				} else {
					$this->redirect(array('view', 'id' => $eventId));
				}

				return;
			}

			// If we get here element validation and failed and the array of elements will
			// be displayed again in the call below
		}

		$params = array(
			'elements' => $elements,
			'eventTypeId' => $eventTypeId,
			'specialties' => $specialties,
			'patient' => $patient,
			'firm' => $this->firm
		);

		if ($eventType->name == 'operation') {
			$specialty = $this->firm->serviceSpecialtyAssignment->specialty;
			$subsections = SpecialtySubsection::model()->getList($specialty->id);
			$procedures = array();
			if (empty($subsections)) {
				$procedures = Procedure::model()->getListBySpecialty($specialty->id);
			}

			$params['specialty'] = $specialty;
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

		// Check the user's firm is of the correct specialty to have the
		// rights to update this event
		if ($this->firm->serviceSpecialtyAssignment->specialty_id !=
			$event->episode->firm->serviceSpecialtyAssignment->specialty_id) {
			throw new CHttpException(403, 'The firm you are using is not associated with the specialty for this event.');
		}

		// Check the patient id for this event is the same as the session patient id
		if ($event->episode->patient->id != Yii::app()->session['patient_id']) {
			$this->resetSessionPatient($event->episode->patient->id);
		}

		// eventType, firm and patientId are fetched from the event object.
		$elements = $this->service->getElements(null, null, null, $this->getUserId(), $event);

		if (!count($elements)) {
			throw new CHttpException(403, 'That combination event type and firm specialty is not defined.');
		}

		$specialties = Specialty::model()->findAll();

		$patient = Patient::model()->findByPk($this->patientId);

		if ($_POST && $_POST['action'] == 'update') {
			if (Yii::app()->getRequest()->getIsAjaxRequest()) {
				$valid = true;
				$elementList = array();
				foreach ($elements as $element) {
					$elementClassName = get_class($element);
					$element->attributes = $_POST[$elementClassName];
					$elementList[] = $element;
					if (!$element->validate()) {
						$valid = false;
					}
				}
				if (!$valid) {
					echo CActiveForm::validate($elementList);
					Yii::app()->end();
				}
			}

			$success = $this->service->updateElements($elements, $_POST, $event);

			if ($success) {
				$this->logActivity('updated event');

				$this->assignReferralIfRequired($event->id, $this->firm, $this->patientId);

				// Nothing has gone wrong with updating elements, go to the view page
				$eventTypeName = ucfirst($event->eventType->name);
				Yii::app()->user->setFlash('success', "{$eventTypeName} updated.");

				$this->redirect(array('view', 'id' => $event->id));
			}

			// If we get this far element validation has failed, so we render them again.
			// The validation process will have populated and error messages.
		}

		$params = array(
			'id' => $id,
			'elements' => $elements,
			'specialties' => $specialties,
			'patient' => $patient
		);

		if ($event->eventType->name == 'operation') {
			$specialty = $this->firm->serviceSpecialtyAssignment->specialty;
			$subsections = SpecialtySubsection::model()->getList($specialty->id);
			$procedures = array();
			if (empty($subsections)) {
				$procedures = Procedure::model()->getListBySpecialty($specialty->id);
			}

			$params['specialty'] = $specialty;
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
		if ($this->firm->serviceSpecialtyAssignment->specialty_id !=
			$episode->firm->serviceSpecialtyAssignment->specialty_id) {
			$editable = false;
		} else {
			$editable = true;
		}

		// Check the patient id for this episode is the same as the session patient id
		if ($episode->patient->id != Yii::app()->session['patient_id']) {
			$this->resetSessionPatient($episode->patient->id);
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
		if ($this->firm->serviceSpecialtyAssignment->specialty_id !=
			$episode->firm->serviceSpecialtyAssignment->specialty_id) {
			$editable = false;
		} else {
			$editable = true;
		}

		// Check the patient id for this episode is the same as the session patient id
		if ($episode->patient->id != Yii::app()->session['patient_id']) {
			$this->resetSessionPatient($episode->patient->id);
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
	 * Assigns the referral provided, if any, to the episode if one is required.
	 *
	 * @param $eventId int
	 * @param $firm object
	 * @param $patientId int
	 */
	public function assignReferralIfRequired($eventId, $firm, $patientId)
	{
		if (!Yii::app()->params['use_pas']) {
			// Not using referrals, do nothing
			return;
		}

		$referralService = new ReferralService;

		$referralService->assignReferral($eventId, $firm, $patientId);
	}

	/**
	 * Sets arrays of episodes and eventTypes for use by the clinical base.php view.
	 */
	public function listEpisodesAndEventTypes()
	{
		$this->service = new ClinicalService;
		$patient = Patient::model()->findByPk($this->patientId);

		$this->episodes = $patient->episodes;

		$specialtyId = $this->firm->serviceSpecialtyAssignment->specialty_id;
		$this->eventTypes = EventType::model()->getAllPossible($specialtyId);
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

		$this->checkPatientId();

		// Get the firm currently associated with this user
		$this->firm = Firm::model()->findByPk($this->selectedFirmId);

		if (!isset($this->firm)) {
			// No firm selected, reject
			throw new CHttpException(403, 'You are not authorised to view this page without selecting a firm.');
		}

		$this->service = new ClinicalService;

		// Displays the list of episodes and events for this patient
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
		if ($this->firm->serviceSpecialtyAssignment->specialty_id !=
			$episode->firm->serviceSpecialtyAssignment->specialty_id) {
			$editable = false;
		} else {
			$editable = true;
		}

		$episode->end_date = date('Y-m-d H:i:s');
		$episode->save(false);

		$this->renderPartial('episodeSummary', array('episode' => $episode, 'editable' => $editable), false, true);
	}
}
