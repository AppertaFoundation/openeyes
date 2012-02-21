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
http://www.openeyes.org.uk	 info@openeyes.org.uk
--
*/

Yii::import('application.controllers.*');

class PatientController extends BaseController
{
	public $layout = '//layouts/column2';
	public $model;
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
		parent::storeData();

		// Sample code to be used when RBAC is fully implemented.
//		if (!Yii::app()->user->checkAccess('admin')) {
//			throw new CHttpException(403, 'You are not authorised to perform this action.');
//		}

		$this->firm = Firm::model()->findByPk($this->selectedFirmId);

		if (!isset($this->firm)) {
			// No firm selected, reject
			throw new CHttpException(403, 'You are not authorised to view this page without selecting a firm.');
		}

		$this->service = new ClinicalService;

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

	/**
	 * Redirect to correct patient view by hospital number
	 * @param string $hos_num
	 * @throws CHttpException
	 */
	public function actionViewhosnum($hos_num) {
		$hos_num = (int) $hos_num;
		if(!$hos_num) {
			throw new CHttpException(400, 'Invalid hospital number');
		}
		$patient = Patient::model()->find('hos_num=:hos_num', array(':hos_num' => $hos_num));
		if($patient) {
			$this->redirect('/patient/view/'.$patient->id);
		} else {
			throw new CHttpException(404, 'Hospital number not found');
		}
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

			if ((!@$_POST['Patient']['hos_num'] || preg_match('/[^\d]/', $_POST['Patient']['hos_num'])) && (!@$_POST['Patient']['first_name'] || !@$_POST['Patient']['last_name'])) {
				header('Location: /patient/results/error');
				setcookie('patient-search-minimum-criteria','1',0,'/');
				Yii::app()->end();
			}

			if (@$_POST['Patient']['hos_num']) {
				$get_hos_num = $_POST['Patient']['hos_num'];

				if (Yii::app()->params['use_pas']) {
					$get_hos_num = str_pad($get_hos_num, 7, '0', STR_PAD_LEFT);
				}
			} else {
				$get_hos_num = '000000';
			}

			$get_first_name = (@$_POST['Patient']['first_name'] ? $_POST['Patient']['first_name'] : '0');
			$get_last_name = (@$_POST['Patient']['last_name'] ? $_POST['Patient']['last_name'] : '0');
			// Get rid of any dashes from nhs_num as PAS doesn't store them
			$get_nhs_num = (@$_POST['Patient']['nhs_num'] ? preg_replace('/-/', '', $_POST['Patient']['nhs_num']) : '0');
			$get_gender = (@$_POST['Patient']['gender'] ? $_POST['Patient']['gender'] : '0');
			$get_dob_day = (@$_POST['dob_day'] ? $_POST['dob_day'] : '0');
			$get_dob_month = (@$_POST['dob_month'] ? $_POST['dob_month'] : '0');
			$get_dob_year = (@$_POST['dob_year'] ? $_POST['dob_year'] : '0');

			header("Location: /patient/results/$get_hos_num/$get_first_name/$get_last_name/$get_nhs_num/$get_gender/$get_dob_day/$get_dob_month/$get_dob_year/1");
			setcookie('patient-search-minimum-criteria','1',0,'/');
			Yii::app()->end();
		}

		if (@$_GET['hos_num'] == '0' && (@$_GET['first_name'] == '0' || @$_GET['last_name'] == '0')) {
			header('Location: /patient/results/error');
			Yii::app()->end();
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
			Yii::app()->end();
		}

		if ($nr == 1) {
			foreach ($dataProvider->getData() as $item) {
				header('Location: /patient/view/'.$item->id);
				Yii::app()->end();
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

		$criteria = new CDbCriteria;
		$criteria->compare('patient_id', $patient->id);
		$criteria->order = 'start_date DESC';

		$dataProvider = new CActiveDataProvider('Episode', array(
			'criteria'=>$criteria));

		$this->renderPartial('_summary',
			array('model'=>$patient, 'address'=>$patient->address, 'episodes'=>$dataProvider));
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

	public function actionEpisodes()
	{
		$this->layout = '//layouts/patientMode/main';
		$this->service = new ClinicalService;
		$patient = $this->model = $this->loadModel($_GET['id']);

		$episodes = $patient->episodes;

		if (ctype_digit(@$_GET['event'])) {
			$event = Event::model()->findByPk($_GET['event']);

			// The eventType, firm and patient are fetched from the event object
			$elements = $this->service->getElements(
				null, null, null, $event->created_user_id, $event
			);

			// Decide whether to display the 'edit' button in the template
			if ($this->firm->serviceSpecialtyAssignment->specialty_id !=
				$event->episode->firm->serviceSpecialtyAssignment->specialty_id) {
				$editable = false;
			} else {
				$editable = true;
			}

			$event_template_name = $this->getTemplateName('view', $event->event_type_id);

			$this->logActivity('viewed event');
		}

		$eventTypes = EventType::model()->getAllPossible($this->firm->serviceSpecialtyAssignment->specialty_id);

		$site = Site::model()->findByPk(Yii::app()->request->cookies['site_id']->value);

		$this->render('episodes', array(
			'model' => $patient, 'episodes' => $episodes, 'event' => @$event, 'elements' => @$elements, 'editable' => @$editable, 'event_template_name' => @$event_template_name, 'eventTypes' => $eventTypes, 'site' => $site
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
