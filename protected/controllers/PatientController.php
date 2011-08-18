<?php

Yii::import('application.controllers.*');

class PatientController extends BaseController
{
	public $layout = '//layouts/column2';
	
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
//		if (!Yii::app()->user->checkAccess('admin')) {
//			throw new CHttpException(403, 'You are not authorised to perform this action.');
//		}

		return parent::beforeAction($action);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
// @todo - do actionViewByHosHum and actionView need to be separate methods? Is this method used directly any more?
		$patient = $this->loadModel($id);
		
		$tabId = !empty($_GET['tabId']) ? $_GET['tabId'] : 0;
		$eventId = !empty($_GET['eventId']) ? $_GET['eventId'] : 0;

		$this->layout = '//layouts/patientMode/main';

		$app = Yii::app();
		$app->session['patient_id'] = $patient->id;
		$app->session['patient_name'] = $patient->title . ' ' . $patient->first_name . ' ' . $patient->last_name;

		$this->logActivity('viewed patient');

		$this->render('view', array(
			'model' => $patient, 'tab' => $tabId, 'event' => $eventId, 
		));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider = new CActiveDataProvider('Patient');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	/**
	 * Display a form to use for searching models
	 */
	public function actionSearch()
	{
		if (isset($_POST['Patient'])) {
			$this->forward('results');
		} else {
			$model = new Patient;
			$this->render('search', array(
				'model' => $model,
			));
		}
	}

	/**
	 * Display results based on a search submission
	 */
	public function actionResults()
	{
		$model = new Patient;

		if (Yii::app()->params['use_pas']) {
			$service = new PatientService;
			$criteria = $service->search($this->collatePostData());

	       		$dataProvider = new CActiveDataProvider('Patient', array(
				'criteria' => $criteria
			));
		} else {
			$model->attributes = $this->collatePostData();
			$dataProvider = $model->search();
		}

		$results = $dataProvider->getData();

		$output = array();

		foreach ($results as $result) {
			$output[] = array($result['id'], $result['first_name'], $result['last_name']);
		}

		//echo CJavaScript::jsonEncode($output);
		echo CJavaScript::jsonEncode($results);
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model = new Patient('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['Patient']))
			$model->attributes = $_GET['Patient'];

		$this->render('admin', array(
			'model' => $model,
		));
	}
	
	public function actionSummary()
	{
		$patient = $this->loadModel($_GET['id']);
		$address = Address::model()->findByPk($patient->address_id);
		
		$criteria = new CDbCriteria;
		$criteria->compare('patient_id', $patient->id);
		$criteria->order = 'start_date DESC';
		$criteria->limit = 5;

		$dataProvider = new CActiveDataProvider('Episode', array(
			'criteria'=>$criteria));
		
		$this->renderPartial('_summary', 
			array('model'=>$patient, 'address'=>$address, 'episodes'=>$dataProvider));
	}
	
	public function actionEpisodes()
	{
		$patient = $this->loadModel($_GET['id']);
		$event = !empty($_GET['event']) ? $_GET['event'] : false;
		
		$firm = Firm::model()->findByPk($this->selectedFirmId);
		
		$specialtyId = $firm->serviceSpecialtyAssignment->specialty_id;
		$eventTypes = EventType::model()->getAllPossible($specialtyId);
		
		$typeGroups = $this->getEventTypeGrouping();
	
		foreach ($eventTypes as $eventType) {
			foreach ($typeGroups as $name => $group) {
				if (in_array($eventType->name, $group)) {
					$typeList[$name][] = $eventType;
				}
			}
		}
	
		$eventId = isset($_REQUEST['eventId']) ? $_REQUEST['eventId'] : null;
	
		$this->renderPartial('_episodes', 
			array('model'=>$patient, 'episodes'=>$patient->episodes, 
				'eventTypeGroups'=>$typeList, 'firm'=>$firm, 'event'=>$event), false, true);
	}
	
	public function actionContacts()
	{
		$patient = $this->loadModel($_GET['id']);
		$this->renderPartial('_contacts', array('model'=>$patient));
	}
	
	public function actionCorrespondence()
	{
		$patient = $this->loadModel($_GET['id']);
		$this->renderPartial('_correspondence', array('model'=>$patient));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = Patient::model()->findByPk((int) $id);
		if ($model === null)
			throw new CHttpException(404, 'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'patient-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	protected function getEventTypeGrouping()
	{
		return array(
			'Examination' => array('visual fields', 'examination', 'question', 'outcome'),
			'Imaging & Surgery' => array('oct', 'laser', 'operation'),
			'Correspondence' => array('letterin', 'letterout'),
			'Consent Forms' => array(''),
		);
	}

	/**
	 * Perform a search on a model and return the results
	 * (separate function for unit testing)
	 * 
	 * @param array $data   form data of search terms
	 * @return dataProvider
	 */
	public function getSearch($data)
	{
		$model = new Patient;
		$model->attributes = $data;
		return $model->search();
	}

	/**
	 * Returns the $_REQUIEST['Patient'] values plus the dob day, month and year appended together.
	 *
	 * @return array
	 */
	public function collatePostData()
	{
		$data = $_POST['Patient'];

		if (isset($_POST['dob_day']) && isset($_POST['dob_month']) && isset($_POST['dob_year']) && $_POST['dob_day'] && $_POST['dob_month'] && $_POST['dob_year']) {
			$data['dob'] = $_POST['dob_year'] . '-' . $_POST['dob_month'] . '-' . $_POST['dob_day'];
		}

		return $data;
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
