<?php

Yii::import('application.controllers.*');

class PatientController extends BaseController
{
	public $layout = '//layouts/column2';
	public $model;

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
		$patient = $this->loadModel($id);

		$tabId = !empty($_GET['tabId']) ? $_GET['tabId'] : 0;
		$eventId = !empty($_GET['eventId']) ? $_GET['eventId'] : 0;

		$episodes = $patient->episodes;

		$this->layout = '//layouts/patientMode/main';
		$this->model = $patient;

		$app = Yii::app();
		$app->session['patient_id'] = $patient->id;
		$app->session['patient_name'] = $patient->title . ' ' . $patient->first_name . ' ' . $patient->last_name;

		$this->logActivity('viewed patient');

		$episodes_open = 0;
		$episodes_closed = 0;

		foreach ($episodes as $episode) {
			if ($episode->end_date === null) {
				$episodes_open++;
			} else {
				$episodes_closed++;
			}
		}

		$this->render('view', array(
			'model' => $patient, 'tab' => $tabId, 'event' => $eventId, 'episodes' => $episodes, 'episodes_open' => $episodes_open, 'episodes_closed' => $episodes_closed
		));
	}

	public function actionViewpas() {
		$patient = Patient::model()->find('PAS_Key=:PAS_Key', array(':PAS_Key'=>(integer)$_GET['pas_key']));
		header('Location: /patient/view/'.$patient->id);
		exit;
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
	public function actionResults($page=false)
	{
		if (!empty($_POST)) {
			if (!@$_POST['Patient']['hos_num'] && (!@$_POST['Patient']['first_name'] || !@$_POST['Patient']['last_name'])) {
				header('Location: /patient/results/error');
				setcookie('patient-search-minimum-criteria','1',0,'/');
				exit;
			}
			$get_hos_num = (@$_POST['Patient']['hos_num'] ? $_POST['Patient']['hos_num'] : '0');
			$get_first_name = (@$_POST['Patient']['first_name'] ? $_POST['Patient']['first_name'] : '0');
			$get_last_name = (@$_POST['Patient']['last_name'] ? $_POST['Patient']['last_name'] : '0');
			$get_nhs_num = (@$_POST['Patient']['nhs_num'] ? $_POST['Patient']['nhs_num'] : '0');
			$get_gender = (@$_POST['Patient']['gender'] ? $_POST['Patient']['gender'] : '0');
			$get_dob_day = (@$_POST['dob_day'] ? $_POST['dob_day'] : '0');
			$get_dob_month = (@$_POST['dob_month'] ? $_POST['dob_month'] : '0');
			$get_dob_year = (@$_POST['dob_year'] ? $_POST['dob_year'] : '0');

			header("Location: /patient/results/$get_hos_num/$get_first_name/$get_last_name/$get_nhs_num/$get_gender/$get_dob_day/$get_dob_month/$get_dob_year/1");
			setcookie('patient-search-minimum-criteria','1',0,'/');
			exit;
		}

		if (@$_GET['hos_num'] == '0' && (@$_GET['first_name'] == '0' || @$_GET['last_name'] == '0')) {
			header('Location: /patient/results/error');
			exit;
		}

		$model = new Patient;

		$items_per_page = 10;

		if (Yii::app()->params['use_pas']) {
			$service = new PatientService;
			$criteria = $service->search($this->collateGetData());

			$nr = Patient::model()->count($criteria);

			$dataProvider = new CActiveDataProvider('Patient', array(
				'criteria' => $criteria,
				'pagination' => array('pageSize' => $items_per_page, 'currentPage' => (integer)@$_GET['page_num']-1)
			));
		} else {
			$model->attributes = $this->collateGetData();
			$dataProvider = $model->search(array(
				'currentPage' => (integer)@$_GET['page_num']-1,
				'items_per_page' => $items_per_page
			));

			$nr = $model->search_nr();
		}

		if ($nr == 0) {
			header('Location: /patient/no-results');
			exit;
		}

		if ($nr == 1) {
			foreach ($dataProvider->getData() as $item) {
				header('Location: /patient/view/'.$item->id);
				exit;
			}
		}

		$pages = ceil($nr/$items_per_page);

		if (count($nr) == 0) {
			$this->render('index', array(
				'dataProvider' => $dataProvider
			));
		} else {
			$this->render('results', array(
				'dataProvider' => $dataProvider,
				'pages' => $pages,
				'items_per_page' => $items_per_page,
				'total_items' => $nr,
				'hos_num' => (integer)$_GET['hos_num'],
				'first_name' => $_GET['first_name'],
				'last_name' => $_GET['last_name'],
				'nhs_num' => (integer)$_GET['nhs_num'],
				'gender' => $_GET['gender'],
				'dob_day' => (integer)$_GET['dob_day'],
				'dob_month' => (integer)$_GET['dob_month'],
				'dob_year' => (integer)$_GET['dob_year'],
				'pagen' => (integer)$_GET['page_num']-1
			));
		}
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model = new Patient('search');
		$model->unsetAttributes();	// clear any default values
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

	/*public function actionEpisodes()
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
	}*/

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

	public function actionEpisodes()
	{
		$patient = $this->loadModel($_GET['id']);

		$episodes = $patient->episodes;

		/*foreach ($patient->episodes as $episode) {
			$speciality_name = $episode->firm->serviceSpecialtyAssignment->specialty->name;

			if (!isset($episodes[$speciality_name])) {
				$episodes[$speciality_name] = array();
			}

			$episodes[$speciality_name][] = $episode;
		}*/

		$this->render('episodes', array(
			'model' => $patient, 'episodes' => $episodes
		));
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
			'Treatments' => array('oct', 'laser', 'operation'),
			'Correspondence' => array('letterin', 'letterout'),
			'Consent Forms' => array(''),
		);
	}

	/**
	 * Perform a search on a model and return the results
	 * (separate function for unit testing)
	 *
	 * @param array $data		form data of search terms
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

	public function collateGetData()
	{
		$data = $_GET;

		if (isset($_GET['dob_day']) && isset($_GET['dob_month']) && isset($_GET['dob_year']) && $_GET['dob_day'] && $_GET['dob_month'] && $_GET['dob_year']) {
			$data['dob'] = $_GET['dob_year'] . '-' . $_GET['dob_month'] . '-' . $_GET['dob_day'];
		}

		foreach ($data as $key => $value) {
			if ($value == '0') {
				$data[$key] = '';
			}
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
