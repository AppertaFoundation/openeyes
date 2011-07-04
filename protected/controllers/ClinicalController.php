<?php

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
				'users'=>array('@')
			),
			// non-logged in can't view anything
			array('deny', 
				'users'=>array('?')
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

		$this->renderPartial('view', array('elements' => $elements), false, true);
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

		if ($_POST && $_POST['action'] == 'create')
		{
			// The user has submitted the form to create the event

			$eventId = $this->service->createElements(
				$elements, $_POST, $this->firm, $this->patientId, $this->getUserId(), $eventType->id
			);

			if ($eventId) {
				$this->redirect(array('view', 'id' => $eventId));
			}

			// If we get here element validation and failed and the array of elements will
			// be displayed again in the call below
		}

		$this->render('create', array(
				'elements' => $elements,
				'eventTypeId' => $eventTypeId,
				'specialties' => $specialties
			)
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

		if ($_POST && $_POST['action'] == 'update') {
			// The user has submitted the form to update the event

			$success = $this->service->updateElements($elements, $_POST, $event);

			if ($success) {
				// Nothing has gone wrong with updating elements, go to the view page
				$this->redirect(array('view', 'id' => $event->id));
			}

			// If we get this far element validation has failed, so we render them again.
			// The validation process will have populated and error messages.
		}

		$this->render('update', array(
				'id' => $id,
				'elements' => $elements,
				'specialties' => $specialties
			)
		);
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

		$this->render('summary', array(
				'episode' => $episode,
				'summary' => $_GET['summary']
			)
		);
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
}
