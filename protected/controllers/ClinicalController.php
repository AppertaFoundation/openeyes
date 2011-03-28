<?php

class ClinicalController extends BaseController
{
	public $layout = '//layouts/patientMode/column2';
	public $episodes;
	public $eventTypes;
	public $firm;
	public $service;

	protected function beforeAction(CAction $action)
	{
		// Sample code to be used when RBAC is fully implemented.
		// if (!Yii::app()->user->checkAccess('admin')) {
		// 	throw new CHttpException(403, 'You are not authorised to perform this action.');
		// }

		$this->checkPatientId();

		// @todo - this needs tidying
		$beforeActionResult = parent::beforeAction($action);


		// Get the firm currently associated with this user
		// @todo - user shouldn't be able to reach this page if they haven't selected a firm
		$this->firm = Firm::model()->findByPk($this->selectedFirmId);

		$this->service = new ClinicalService;

		// Displays the list of episodes and events for this patient
		$this->listEpisodesAndEventTypes();

		return $beforeActionResult;
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

		// Get all the site elements for this event's event type, in order
		$siteElementTypes = SiteElementType::model()->getAllPossible(
			$event->event_type_id,
			$this->firm->serviceSpecialtyAssignment->specialty_id,
			$event->episode_id
		);
		$elements = $this->service->getEventElementTypes($siteElementTypes, $event->id);

		$this->render('view', array('elements' => $elements));
	}

	public function actionIndex()
	{
		$this->render('index');
	}

	/**
	 * Creates a new event.
	 */
	public function actionCreate()
	{
		// @todo - check that this event type is permitted for this specialty
		if (!isset($_GET['event_type_id'])) {
			throw new CHttpException(403, 'No event_type_id specified.');
		}

		$eventType = EventType::model()->findByPk($_GET['event_type_id']);

		if (!isset($eventType)) {
			throw new CHttpException(403, 'Invalid event_type_id.');
		}

		$specialtyId = $this->firm->serviceSpecialtyAssignment->specialty->id;
		$episode = Episode::model()->getBySpecialtyAndPatient($specialtyId, $this->patientId);
		if (isset($episode)) {
			$episodeId = $episode->id;
		} else {
			$episodeId = null;
		}
		$siteElementTypes = SiteElementType::model()->getAllPossible($eventType->id, $specialtyId, $episodeId);

		if ($_POST && $_POST['action'] == 'create')
		{
			$results = $this->service->validateElements($siteElementTypes, $_POST);
			$valid = $results['valid'];
			// @todo - elements aren't displayed on failure of validation. Find a way of
			//	displaying them.
			$elements = $results['elements'];

			if ($valid) {
				/**
				 * Create the event. First check to see if there is currently an episode for this
				 * specialty for this patient. If so, add the new event to it. If not, create an
				 * episode and add it to that.
				 */
				$specialtyId = $this->firm->serviceSpecialtyAssignment->specialty->id;
				$episode = Episode::model()->getBySpecialtyAndPatient($specialtyId, $this->patientId);
				if (!$episode) {
					$episode = new Episode();
					$episode->patient_id = $this->patientId;
					$episode->firm_id = $this->firm->id;
					// @todo - this might not be DB independent
					$episode->start_date = date("Y-m-d H:i:s");

					if (!$episode->save()) {
						// @todo - what to do with error?
						exit('Cannot create episode.');
					}
				}

				$event = new Event();
				$event->episode_id = $episode->id;
				$event->user_id = Yii::app()->user->id;
				$event->event_type_id = $_GET['event_type_id'];
				$event->datetime = date("Y-m-d H:i:s");
				$event->save();

				// Create elements for the event
				foreach ($elements as $element) {
					$element->event_id = $event->id;
					// @todo - for some reason Yii likes to try and update here instead of create.
					//	Find out why.
					$element->setIsNewRecord(true);
					if (!$element->save(false)) { // No need to validate
						// @todo - what to do here? This shouldn't happen as the element
						// has already been validated.
						exit('Unable to create element (??)');
					}
				}

				$this->redirect(array('view', 'id' => $event->id));
			}
		}

		$this->render('create', array(
				'siteElementTypeObjects' => $siteElementTypes,
				'eventTypeId' => $_REQUEST['event_type_id']
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

		// Check the user's firm is of the correct specialty to have the
		// rights to update this event
		if ($this->firm->serviceSpecialtyAssignment->specialty_id !=
			$event->episode->firm->serviceSpecialtyAssignment->specialty_id) {
			// User's firm's specialty id doesn't match the specialty id for this event, they shouldn't be here!
			throw new CHttpException(403, 'The firm you are using is not associated with the specialty for this event.');
		}

		// Get an array of all the site elements for this event type
		$siteElementTypes = SiteElementType::model()->getAllPossible(
			$event->event_type_id,
			$this->firm->serviceSpecialtyAssignment->specialty_id
		);

		$elements = $this->service->getEventElementTypes($siteElementTypes, $event->id, true);

		// Loop through the elements and save them if need be
		if ($_POST && $_POST['action'] == 'update') {
			$success = $this->service->updateElements($elements, $_POST, $event->id);

			if ($success) {
				// Nothing has gone wrong with saving elements, go to the view page
				$this->redirect(array('view', 'id' => $event->id));
			}
		}

		$this->render('update', array(
				'id' => $id,
				'elements' => $elements
			)
		);
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax']==='episode-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	/**
	 * Sets arrays of episodes and eventTypes for use by the clinical base.php view.
	 */
	public function listEpisodesAndEventTypes()
	{
		$this->service = new ClinicalService;
		$patient = Patient::model()->findByPk($this->patientId);

		$this->episodes = $patient->episodes;

		$this->firm = Firm::model()->findByPk($this->selectedFirmId);
		$this->eventTypes = EventType::model()->getAllPossible($this->firm->serviceSpecialtyAssignment->specialty_id);
	}
}
