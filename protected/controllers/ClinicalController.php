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
//		if (!Yii::app()->user->checkAccess('admin')) {
//			throw new CHttpException(403, 'You are not authorised to perform this action.');
//		}

		$this->checkPatientId();

		// Displays the list of episodes and events for this patient
		$this->listEpisodesAndEventTypes();

		// @todo - this needs tidying
		$beforeActionResult = parent::beforeAction($action);

		// Get the firm currently associated with this user
		// @todo - user shouldn't be able to reach this page if they haven't selected a firm
		$this->firm = Firm::model()->findByPk($this->selectedFirmId);

		$this->service = new ClinicalService;

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
		$siteElementTypes = $this->service->getSiteElementTypeObjects(
			$event->event_type_id, $this->firm);

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

		$siteElementTypes = $this->service->getSiteElementTypeObjects(
			$eventType->id, $this->firm);

		if ($_POST && $_POST['action'] == 'create')
		{
			$results = $this->service->validateElements($siteElementTypes, $_POST);
			$valid = $results['valid'];
			$elements = $results['elements'];

			if ($valid) {
				/**
				 * Create the event. First check to see if there is currently an episode for this
				 * specialty for this patient. If so, add the new event to it. If not, create an
				 * episode and add it to that.
				 */
				$specialty = $this->firm->serviceSpecialtyAssignment->specialty;

				$episode = Episode::modelBySpecialtyIdAndPatientId(
					$specialty->id,
					$this->patientId
				);

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

					if (!$element->save()) {
						// @todo - what to do here? This shouldn't happen as the element
						// has already been validated.
						exit('Unable to create element (??)');
					}
				}

				$this->redirect(array('view', 'id' => $event->id));
			}
		}

		$this->render('create', array(
				'siteElementTypeObjects' => $siteElementTypeObjects,
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
		if ($this->firm->serviceSpecialtyAssignment->specialty_id != $event->episode->firm->serviceSpecialtyAssignment->specialty_id) {
			// User's firm's specialty id doesn't match the specialty id for this event, they shouldn't be here!
			throw new CHttpException(403, 'The firm you are using is not associated with the specialty for this event.');
		}

		// Get an array of all the site elements for this event type
		$siteElementTypeObjects = ClinicalService::getSiteElementTypeObjects(
			$event->event_type_id,
			$this->firm
		);

		$elements = array();

		// Get all elements that actually exist for this event
		foreach ($siteElementTypeObjects as $siteElementType) {
			$elementClassName = $siteElementType->possibleElementType->elementType->class_name;

			$element = $elementClassName::model()->find('event_id = ?', array($event->id));

			$preExisting = true;

			if (!$element) {
				$element = new $elementClassName;
				$preExisting = false;
			}

			$elements[] = array(
				'element' => $element,
				'siteElementType' => $siteElementType,
				'preExisting' => $preExisting,
			);
		}

		// Loop through the elements and save them if need be
		if ($_POST && $_POST['action'] == 'update') {
			$saveError = false;

			foreach ($elements as $element) {
				$elementClassName = get_class($element['element']);

				if ($_POST[$elementClassName]) {
					// The user has entered information for this element
					// Check if it's a pre-existing element
					if (!$element['preExisting']) {
						// It's not pre-existing so give it an event id
						$element['element']->event_id = $event->id;
					}

					// @todo - is there a risk they could change the event id here?
					$element['element']->attributes = $_POST[$elementClassName];
				}

				if (!$element['element']->save()) {
					$saveError = true;
				}
			}

			if (!$saveError) {
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
	protected function listEpisodesAndEventTypes()
	{
		$patient = Patient::Model()->findByPk($this->patientId);

		$this->episodes = $patient->episodes;

		// @todo - change to only list event types that have at least one element defined?
		$this->eventTypes = EventType::Model()->findAll();
	}
}
