<?php

// @todo - by default, show the most recent episode for the firm's specialty. not the most recent episode, even after adding a new event. FIX THIS SHARPISH!
// @todo - add logging for deletion on an event in the admin area
// @todo - for that matter create an admin script for deleting events!
// @todo - if an event isn't created or updated succsessfully it doesn't display the form correctly again - there is no layout. Fix.
//	TEMPRORARILY - make diagnoses like procedures, in that only a valid diagnosis can be a diagnosis.
// @todo - what are we to do about the fact that episodes can't be closed? Operations will be put in the same episode forever!
// @todo - make fancyboxes appear a fixed distance from the top of the screeen? This will save having to scroll down when boxes expand,
//	e.g. booking an operation.
// @todo - login timeout can lead to the login screen being displayed within another. Fix this.

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
			// @todo - this is here until we decide where the best place to put it is.
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
				if (Yii::app()->params['use_pas'] && $eraId = $this->checkForReferral($eventId)) {
					$this->redirect(array('chooseReferral', 'id' => $eraId));
				} else {
					$this->redirect(array('patient/view',
						'id' => $this->patientId, 'tabId' => 1, 'eventId' => $eventId));
				}

				return;
			}

			// If we get here element validation and failed and the array of elements will
			// be displayed again in the call below
		}

		// Check to see if they need to choose a referral
		$referrals = $this->checkForReferrals($this->firm, $this->patientId);

		$this->renderPartial($this->getTemplateName('create', $eventTypeId), array(
			'elements' => $elements,
			'eventTypeId' => $eventTypeId,
			'specialties' => $specialties,
			'patient' => $patient,
			'referrals' => $referrals
			), false, true
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
				if (Yii::app()->params['use_pas'] && $eraId = $this->checkForReferral($event->id)) {
					$this->redirect(array('chooseReferral', 'id' => $eraId));
				} else {
					$this->logActivity('updated event');

					$eventTypeName = ucfirst($event->eventType->name);
					Yii::app()->user->setFlash('success', "{$eventTypeName} updated.");
					$this->redirect(array(
						'patient/view',
						'id' => $this->patientId,
						'tabId' => 1,
						'eventId' => $event->id));
				}
			}

			// If we get this far element validation has failed, so we render them again.
			// The validation process will have populated and error messages.
		}

		// @todo - add all the referral stuff from actionCreate to this method
		$this->renderPartial($this->getTemplateName('update', $event->event_type_id), array(
			'id' => $id,
			'elements' => $elements,
			'specialties' => $specialties,
			'patient' => $patient
			), false, true);
	}

	public function actionEpisodeSummary($id)
	{
		$episode = Episode::model()->findByPk($id);

		if (!isset($episode)) {
			throw new CHttpException(403, 'Invalid episode id.');
		}

		$this->renderPartial('episodeSummary', array('episode' => $episode), false, true);
	}

	public function actionSummary($id)
	{
		$episode = Episode::model()->findByPk($id);

		if (!isset($episode)) {
			throw new CHttpException(403, 'Invalid episode id.');
		}

		if (!isset($_GET['summary'])) {
			throw new CHttpException(403, 'No summary.');
		}

		$this->logActivity('viewed patient summary');

		$this->renderPartial('summary', array(
			'episode' => $episode,
			'summary' => $_GET['summary']
			), false, true
		);
	}

	public function actionChooseReferral($id)
	{
		$referralEpisode = ReferralEpisodeAssignment::model()->findByPk($id);

		if (!isset($referralEpisode)) {
			throw new CHttpException(403, 'Invalid referral episode assignment id.');
		}

		// @todo - check they have access to this episode and that the episode doesn't have
		// a referral already?

		$referrals = Referral::model()->findAll(array(
			'order' => 'refno DESC',
			'condition' => 'patient_id = :p AND closed = 0',
			'params' => array(':p' => $this->patientId)
			)
		);

		if ($_POST && $_POST['action'] == 'chooseReferral') {
			if (isset($_POST['referral_id'])) {
// @todo - check referral_id is in list of referrals
				$referralEpisode->referral_id = $_POST['referral_id'];

				if ($referralEpisode->save()) {
					$this->logActivity('chose a referral');

					$this->redirect(array('view', 'id' => $id));
					return;
				}
			}
		}

		// @todo - decide what to display in the drop down list. Referral id alone isn't very informative.
		$this->render('chooseReferral', array(
			'id' => $id,
			'referrals' => CHtml::listData($referrals, 'id', 'refno')
			)
		);
	}

	/**
	 * Checks to see if there the user needs to select a referral.
	 *
	 * This is calculated as follows:
	 *
	 * Check for an open episode for this patient and this firm's specialty
	 * If there is an open episode and it has a referral, no action required so return false
	 * If no episode or the episode has no referral, check to see if a referral can be chosen automatically
	 * If it can, it will be dealt with when creating or updating the event so no action required here, return false
	 * If it can't, return an array of referrals for the user to choose from
	 *
	 * @param $firm object
	 * @param $patientId id
	 *
	 * @return array
	 */
	public function checkForReferrals($firm, $patientId)
	{
		// If pas isn't in use there can't be any referrals
		if (!Yii::app()->params['use_pas']) {
			return false;
		}

		$referralService = new ReferralService;

		return $referralService->getReferralsList($firm, $patientId);
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
}
