<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

Yii::import('application.controllers.*');

class PatientController extends BaseController
{
	public $layout = '//layouts/column2';
	public $patient;
	public $firm;
	public $editable;
	public $editing;
	public $event;
	public $event_type;
	public $title;
	public $event_type_id;
	public $episode;
	public $event_tabs = array();
	public $event_actions = array();

	/**
	 * Checks to see if current user can create an event type
	 * @param EventType $event_type
	 */
	public function checkEventAccess($event_type)
	{
		if (BaseController::checkUserLevel(5)) {
			return true;
		}
		if (BaseController::checkUserLevel(4) && $event_type->class_name != 'OphDrPrescription') {
			return true;
		}
		return false;
	}

	public function accessRules()
	{
		return array(
			// Level 1 can view patient demographics
			array('allow',
				'actions' => array('search','view','hideepisode','showepisode'),
				'expression' => 'BaseController::checkUserLevel(1)',
			),
			// Level 2 can't change anything
			array('allow',
				'actions' => array('episode','event', 'episodes'),
				'expression' => 'BaseController::checkUserLevel(2)',
			),
			// Level 3 or above can do anything
			array('allow',
				'expression' => 'BaseController::checkUserLevel(4)',
			),
			// Deny anything else (default rule allows authenticated users)
			array('deny'),
		);
	}

	public function printActions()
	{
		return array(
				'printadmissionletter',
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

		return parent::beforeAction($action);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		Yii::app()->getClientScript()->registerScriptFile(Yii::app()->createUrl('/js/patientSummary.js'));

		$this->patient = $this->loadModel($id);

		$tabId = !empty($_GET['tabId']) ? $_GET['tabId'] : 0;
		$eventId = !empty($_GET['eventId']) ? $_GET['eventId'] : 0;

		$episodes = $this->patient->episodes;
		// TODO: verify if ordered_episodes complete supercedes need for unordered $episodes
		$ordered_episodes = $this->patient->getOrderedEpisodes();

		$legacyepisodes = $this->patient->legacyepisodes;
		$supportserviceepisodes = $this->patient->supportserviceepisodes;

		$this->layout = '//layouts/patientMode/main';

		Audit::add('patient summary','view');

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

		$this->jsVars['currentContacts'] = $this->patient->currentContactIDS();

		$this->breadcrumbs=array(
			$this->patient->first_name.' '.$this->patient->last_name. '('.$this->patient->hos_num.')',
		);

		$this->render('view', array(
			'tab' => $tabId, 'event' => $eventId, 'episodes' => $episodes, 'ordered_episodes' => $ordered_episodes, 'legacyepisodes' => $legacyepisodes, 'episodes_open' => $episodes_open, 'episodes_closed' => $episodes_closed, 'firm' => Firm::model()->findByPk(Yii::app()->session['selected_firm_id']), 'supportserviceepisodes' => $supportserviceepisodes,
		));
	}

	/**
	 * Redirect to correct patient view by hospital number
	 * @param string $hos_num
	 * @throws CHttpException
	 */
	public function actionViewhosnum($hos_num)
	{
		$hos_num = (int) $hos_num;
		if (!$hos_num) {
			throw new CHttpException(400, 'Invalid hospital number');
		}
		$patient = Patient::model()->find('hos_num=:hos_num', array(':hos_num' => $hos_num));
		if ($patient) {
			$this->redirect(array('/patient/view/'.$patient->id));
		} else {
			throw new CHttpException(404, 'Hospital number not found');
		}
	}

	public function actionSearch()
	{
		// Check that we have a valid set of search criteria
		$search_terms = array(
				'hos_num' => null,
				'nhs_num' => null,
				'first_name' => null,
				'last_name' => null,
		);
		foreach ($search_terms as $search_term => $search_value) {
			if (isset($_GET[$search_term]) && $search_value = trim($_GET[$search_term])) {

				// Pad hos_num
				if ($search_term == 'hos_num') {
					$search_value = sprintf('%07s',$search_value);
				}

				$search_terms[$search_term] = $search_value;
			}
		}
		// if we are on a dev environment, this allows more flexible search terms (i.e. just a first name or surname - useful for testing
		// the multiple search results view. If we are live, enforces controls over search terms.
		if (!YII_DEBUG && !$search_terms['hos_num'] && !$search_terms['nhs_num'] && !($search_terms['first_name'] && $search_terms['last_name'])) {
			Yii::app()->user->setFlash('warning.invalid-search', 'Please enter a valid search.');
			$this->redirect(Yii::app()->homeUrl);
		}

		 $search_terms = CHtml::encodeArray($search_terms);

		switch (@$_GET['sort_by']) {
			case 0:
				$sort_by = 'hos_num*1';
				break;
			case 1:
				$sort_by = 'title';
				break;
			case 2:
				$sort_by = 'first_name';
				break;
			case 3:
				$sort_by = 'last_name';
				break;
			case 4:
				$sort_by = 'dob';
				break;
			case 5:
				$sort_by = 'gender';
				break;
			case 6:
				$sort_by = 'nhs_num*1';
				break;
			default:
				$sort_by = 'hos_num*1';
		}

		$sort_dir = (@$_GET['sort_dir'] == 0 ? 'asc' : 'desc');
		$page_num = (integer) @$_GET['page_num'];
		$page_size = 20;

		$model = new Patient();
		$model->hos_num = $search_terms['hos_num'];
		$model->nhs_num = $search_terms['nhs_num'];
		$dataProvider = $model->search(array(
			'currentPage' => $page_num,
			'pageSize' => $page_size,
			'sortBy' => $sort_by,
			'sortDir'=> $sort_dir,
			'first_name' => CHtml::decode($search_terms['first_name']),
			'last_name' => CHtml::decode($search_terms['last_name']),
		));
		$nr = $model->search_nr(array(
			'first_name' => CHtml::decode($search_terms['first_name']),
			'last_name' => CHtml::decode($search_terms['last_name']),
		));

		if ($nr == 0) {
			Audit::add('search','search-results',implode(',',$search_terms) ." : No results");

			$message = 'Sorry, no results ';
			if ($search_terms['hos_num']) {
				$message .= 'for Hospital Number <strong>"'.$search_terms['hos_num'].'"</strong>';
			} elseif ($search_terms['nhs_num']) {
				$message .= 'for NHS Number <strong>"'.$search_terms['nhs_num'].'"</strong>';
			} elseif ($search_terms['first_name'] && $search_terms['last_name']) {
				$message .= 'for Patient Name <strong>"'.$search_terms['first_name'] . ' ' . $search_terms['last_name'].'"</strong>';
			} else {
				$message .= 'found for your search.';
			}
			Yii::app()->user->setFlash('warning.no-results', $message);
			
			$this->redirect(Yii::app()->homeUrl);
			
		} elseif ($nr == 1) {
			foreach ($dataProvider->getData() as $item) {
				$this->redirect(array('patient/view/' . $item->id));
			}
		} else {
			$pages = ceil($nr/$page_size);
			$this->render('results', array(
				'data_provider' => $dataProvider,
				'pages' => $pages,
				'page_num' => $page_num,
				'items_per_page' => $page_size,
				'total_items' => $nr,
				'search_terms' => $search_terms,
				'sort_by' => (integer) @$_GET['sort_by'],
				'sort_dir' => (integer) @$_GET['sort_dir']
			));
		}

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
			array('model'=>$patient, 'address'=>$patient->contact->address, 'episodes'=>$dataProvider));
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
		$this->patient = $this->loadModel($_GET['id']);

		$episodes = $this->patient->episodes;
		// TODO: verify if ordered_episodes complete supercedes need for unordered $episodes
		$ordered_episodes = $this->patient->getOrderedEpisodes();
		$legacyepisodes = $this->patient->legacyepisodes;
		$supportserviceepisodes = $this->patient->supportserviceepisodes;
		$site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);

		if (!$current_episode = $this->patient->getEpisodeForCurrentSubspecialty()) {
			$current_episode = empty($episodes) ? false : $episodes[0];
			if (!empty($legacyepisodes)) {
				$criteria = new CDbCriteria;
				$criteria->compare('episode_id',$legacyepisodes[0]->id);
				$criteria->order = 'created_date desc';

				foreach (Event::model()->findAll($criteria) as $event) {
					if (in_array($event->eventType->class_name,Yii::app()->modules) && (!$event->eventType->disabled)) {
						$this->redirect(array($event->eventType->class_name.'/default/view/'.$event->id));
						Yii::app()->end();
					}
				}
			}
		} elseif ($current_episode->end_date == null) {
			$criteria = new CDbCriteria;
			$criteria->compare('episode_id',$current_episode->id);
			$criteria->order = 'created_date desc';

			if ($event = Event::model()->find($criteria)) {
				$this->redirect(array($event->eventType->class_name.'/default/view/'.$event->id));
				Yii::app()->end();
			}
		} else {
			$current_episode = null;
		}

		$this->title = 'Episode summary';
		$this->render('events_and_episodes', array(
			'title' => empty($episodes) ? '' : 'Episode summary',
			'episodes' => $episodes,
			'ordered_episodes' => $ordered_episodes,
			'legacyepisodes' => $legacyepisodes,
			'supportserviceepisodes' => $supportserviceepisodes,
			'eventTypes' => EventType::model()->getEventTypeModules(),
			'site' => $site,
			'current_episode' => $current_episode,
		));
	}

	public function actionEpisode($id)
	{
		$this->layout = '//layouts/patientMode/main';

		if (!$this->episode = Episode::model()->findByPk($id)) {
			throw new SystemException('Episode not found: '.$id);
		}

		$this->patient = $this->episode->patient;

		$episodes = $this->patient->episodes;
		// TODO: verify if ordered_episodes complete supercedes need for unordered $episodes
		$ordered_episodes = $this->patient->getOrderedEpisodes();
		$legacyepisodes = $this->patient->legacyepisodes;
		$supportserviceepisodes = $this->patient->supportserviceepisodes;

		$site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);

		$this->title = 'Episode summary';
		$this->event_tabs = array(
				array(
						'label' => 'View',
						'active' => true,
				)
		);
		if (BaseController::checkUserLevel(4) && $this->episode->editable
				&& $this->firm->serviceSubspecialtyAssignment->subspecialty_id == $this->episode->firm->serviceSubspecialtyAssignment->subspecialty_id) {
			$this->event_tabs[] = array(
					'label' => 'Edit',
					'href' => Yii::app()->createUrl('/patient/updateepisode/'.$this->episode->id),
			);
		}

		$status = Yii::app()->session['episode_hide_status'];
		$status[$id] = true;
		Yii::app()->session['episode_hide_status'] = $status;

		$this->render('events_and_episodes', array(
			'title' => empty($episodes) ? '' : 'Episode summary',
			'episodes' => $episodes,
			'ordered_episodes' => $ordered_episodes,
			'legacyepisodes' => $legacyepisodes,
			'supportserviceepisodes' => $supportserviceepisodes,
			'eventTypes' => EventType::model()->getEventTypeModules(),
			'site' => $site,
			'current_episode' => $this->episode
		));
	}

	public function actionUpdateepisode($id)
	{
		$this->layout = '//layouts/patientMode/main';

		if (!$this->episode = Episode::model()->findByPk($id)) {
			throw new SystemException('Episode not found: '.$id);
		}

		if (!$this->episode->editable || isset($_POST['episode_cancel'])) {
			$this->redirect(array('patient/episode/'.$this->episode->id));
			return;
		}

		if (!empty($_POST)) {
			if ((@$_POST['eye_id'] && !@$_POST['DiagnosisSelection']['disorder_id'])) {
				$error = "Please select a disorder for the principal diagnosis";
			} elseif (!@$_POST['eye_id'] && @$_POST['DiagnosisSelection']['disorder_id']) {
				$error = "Please select an eye for the principal diagnosis";
			} else {
				if (@$_POST['eye_id'] && @$_POST['DiagnosisSelection']['disorder_id']) {
					if ($_POST['eye_id'] != $this->episode->eye_id || $_POST['DiagnosisSelection']['disorder_id'] != $this->episode->disorder_id) {
						$this->episode->setPrincipalDiagnosis($_POST['DiagnosisSelection']['disorder_id'],$_POST['eye_id']);
					}
				}

				if ($_POST['episode_status_id'] != $this->episode->episode_status_id) {
					$this->episode->episode_status_id = $_POST['episode_status_id'];

					if (EpisodeStatus::model()->findByPk($_POST['episode_status_id'])->name == 'Discharged') {
						if ($this->episode->end_date === null) {
							$this->episode->end_date = date('Y-m-d H:i:s');
						}
					} elseif ($this->episode->end_date !== null) {
						$this->episode->end_date = null;
					}

					if (!$this->episode->save()) {
						throw new Exception('Unable to update status for episode '.$this->episode->id.' '.print_r($this->episode->getErrors(),true));
					}
				}

				$this->redirect(array('patient/episode/'.$this->episode->id));
			}
		}

		$this->patient = $this->episode->patient;

		$episodes = $this->patient->episodes;
		// TODO: verify if ordered_episodes complete supercedes need for unordered $episodes
		$ordered_episodes = $this->patient->getOrderedEpisodes();
		$legacyepisodes = $this->patient->legacyepisodes;
		$supportserviceepisodes = $this->patient->supportserviceepisodes;

		$site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);

		$this->title = 'Episode summary';
		$this->event_tabs = array(
				array(
						'label' => 'View',
						'href' => Yii::app()->createUrl('/patient/episode/'.$this->episode->id),
				),
				array(
						'label' => 'Edit',
						'active' => true,
				),
		);

		$status = Yii::app()->session['episode_hide_status'];
		$status[$id] = true;
		Yii::app()->session['episode_hide_status'] = $status;

		$this->editing = true;

		$this->render('events_and_episodes', array(
			'title' => empty($episodes) ? '' : 'Episode summary',
			'episodes' => $episodes,
			'ordered_episodes' => $ordered_episodes,
			'legacyepisodes' => $legacyepisodes,
			'supportserviceepisodes' => $supportserviceepisodes,
			'eventTypes' => EventType::model()->getEventTypeModules(),
			'site' => $site,
			'current_episode' => $this->episode,
			'error' => @$error,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
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
	 * @param CModel $model the model to be validated
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
	 * @param array $data form data of search terms
	 * @return CDataProvider
	 */
	public function getSearch($data)
	{
		$model = new Patient;
		$model->attributes = $data;
		return $model->search();
	}

	public function getTemplateName($action, $eventTypeId)
	{
		$template = 'eventTypeTemplates' . DIRECTORY_SEPARATOR . $action . DIRECTORY_SEPARATOR . $eventTypeId;

		if (!file_exists(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'clinical' . DIRECTORY_SEPARATOR . $template . '.php')) {
			$template = $action;
		}

		return $template;
	}

	/**
	 * Get all the elements for a the current module's event type
	 *
	 * @param $event_type_id
	 * @return array
	 */
	public function getDefaultElements($action, $event_type_id=false, $event=false)
	{
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
	public function getOptionalElements($action, $event=false)
	{
		return array();
	}

	public function actionPossiblecontacts()
	{
		$term = strtolower(trim($_GET['term'])).'%';

		switch (@$_GET['filter']) {
			case 'staff':
				$contacts = User::model()->findAsContacts($term);
				break;
			case 'nonspecialty':
				if (!$specialty = Specialty::model()->find('code=?',array(Yii::app()->params['institution_specialty']))) {
					throw new Exception("Unable to find specialty: ".Yii::app()->params['institution_specialty']);
				}
				$contacts = Contact::model()->findByLabel($term, $specialty->default_title, true);
				break;
			default:
				$contacts = Contact::model()->findByLabel($term, @$_GET['filter']);
		}

		sort($contacts);

		echo CJavaScript::jsonEncode($contacts);
	}

	public function actionAssociatecontact()
	{
		if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
			throw new Exception('Patient not found: '.@$_GET['patient_id']);
		}

		if (@$_GET['contact_location_id']) {
			if (!$location = ContactLocation::model()->findByPk(@$_GET['contact_location_id'])) {
				throw new Exception("Can't find contact location: ".@$_GET['contact_location_id']);
			}
			$contact = $location->contact;
		} else {
			if (!$contact = Contact::model()->findByPk(@$_GET['contact_id'])) {
				throw new Exception("Can't find contact: ".@$_GET['contact_id']);
			}
		}

		// Don't assign the patient's own GP
		if ($contact->label == 'General Practitioner') {
			if ($gp = Gp::model()->find('contact_id=?',array($contact->id))) {
				if ($gp->id == $patient->gp_id) {
					return;
				}
			}
		}

		if (isset($location)) {
			if (!$pca = PatientContactAssignment::model()->find('patient_id=? and location_id=?',array($patient->id,$location->id))) {
				$pca = new PatientContactAssignment;
				$pca->patient_id = $patient->id;
				$pca->location_id = $location->id;

				if (!$pca->save()) {
					throw new Exception("Unable to save patient contact assignment: ".print_r($pca->getErrors(),true));
				}
			}
		} else {
			if (!$pca = PatientContactAssignment::model()->find('patient_id=? and contact_id=?',array($patient->id,$contact->id))) {
				$pca = new PatientContactAssignment;
				$pca->patient_id = $patient->id;
				$pca->contact_id = $contact->id;

				if (!$pca->save()) {
					throw new Exception("Unable to save patient contact assignment: ".print_r($pca->getErrors(),true));
				}
			}
		}

		$this->renderPartial('_patient_contact_row',array('pca'=>$pca));
	}

	public function actionUnassociatecontact()
	{
		if (!$pca = PatientContactAssignment::model()->findByPk(@$_GET['pca_id'])) {
			throw new Exception("Patient contact assignment not found: ".@$_GET['pca_id']);
		}

		if (!$pca->delete()) {
			echo "0";
		} else {
			$pca->patient->audit('patient','unassociate-contact',$pca->getAuditAttributes());
			echo "1";
		}
	}

	/**
	 * Add patient/allergy assignment
	 * @param integer $patient_id
	 * @param integer $allergy_id
	 * @throws Exception
	 */
	public function actionAddAllergy()
	{
		if (!empty($_POST)) {
			if (!isset($_POST['patient_id']) || !$patient_id = $_POST['patient_id']) {
				throw new Exception('Patient ID required');
			}
			if (!$patient = Patient::model()->findByPk($patient_id)) {
				throw new Exception('Patient not found: '.$patient_id);
			}
			if (!isset($_POST['allergy_id']) || !$allergy_id = $_POST['allergy_id']) {
				throw new Exception('Allergy ID required');
			}
			if (!$allergy = Allergy::model()->findByPk($allergy_id)) {
				throw new Exception('Allergy not found: '.$allergy_id);
			}
			$patient->addAllergy($allergy_id);
		}

		$this->redirect(array('patient/view/'.$patient->id));
	}

	/**
	 * Remove patient/allergy assignment
	 * @param integer $patient_id
	 * @param integer $allergy_id
	 * @throws Exception
	 */
	public function actionRemoveAllergy()
	{
		if (!isset($_GET['patient_id']) || !$patient_id = $_GET['patient_id']) {
			throw new Exception('Patient ID required');
		}
		if (!$patient = Patient::model()->findByPk($patient_id)) {
			throw new Exception('Patient not found: '.$patient_id);
		}
		if (!isset($_GET['allergy_id']) || !$allergy_id = $_GET['allergy_id']) {
			throw new Exception('Allergy ID required');
		}
		if (!$allergy = Allergy::model()->findByPk($allergy_id)) {
			throw new Exception('Allergy not found: '.$allergy_id);
		}
		$patient->removeAllergy($allergy_id);

		echo 'success';
	}

	/**
	 * List of allergies
	 */
	public function allergyList()
	{
		$allergy_ids = array();
		foreach ($this->patient->allergies as $allergy) {
			$allergy_ids[] = $allergy->id;
		}
		$criteria = new CDbCriteria;
		!empty($allergy_ids) && $criteria->addNotInCondition('id',$allergy_ids);
		$criteria->order = 'name asc';
		return Allergy::model()->findAll($criteria);
	}

	public function actionHideepisode()
	{
		$status = Yii::app()->session['episode_hide_status'];

		if (isset($_GET['episode_id'])) {
			$status[$_GET['episode_id']] = false;
		}

		Yii::app()->session['episode_hide_status'] = $status;
	}

	public function actionShowepisode()
	{
		$status = Yii::app()->session['episode_hide_status'];

		if (isset($_GET['episode_id'])) {
			$status[$_GET['episode_id']] = true;
		}

		Yii::app()->session['episode_hide_status'] = $status;
	}

	private function processDiagnosisDate()
	{
		$date = $_POST['fuzzy_year'];

		if ($_POST['fuzzy_month']) {
			$date .= '-'.str_pad($_POST['fuzzy_month'],2,'0',STR_PAD_LEFT);
		} else {
			$date .= '-00';
		}

		if ($_POST['fuzzy_day']) {
			$date .= '-'.str_pad($_POST['fuzzy_day'],2,'0',STR_PAD_LEFT);
		} else {
			$date .= '-00';
		}

		return $date;
	}

	public function actionAdddiagnosis()
	{
		if (isset($_POST['DiagnosisSelection']['ophthalmic_disorder_id'])) {
			$disorder = Disorder::model()->findByPk(@$_POST['DiagnosisSelection']['ophthalmic_disorder_id']);
		} else {
			$disorder = Disorder::model()->findByPk(@$_POST['DiagnosisSelection']['systemic_disorder_id']);
		}

		if (!$disorder) {
			throw new Exception('Unable to find disorder: '.@$_POST['DiagnosisSelection']['ophthalmic_disorder_id'].' / '.@$_POST['DiagnosisSelection']['systemic_disorder_id']);
		}

		if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
			throw new Exception('Unable to find patient: '.@$_POST['patient_id']);
		}

		$date = $this->processDiagnosisDate();

		if (!$_POST['diagnosis_eye']) {
			if (!SecondaryDiagnosis::model()->find('patient_id=? and disorder_id=? and date=?',array($patient->id,$disorder->id,$date))) {
				$patient->addDiagnosis($disorder->id,null,$date);
			}
		} elseif (!SecondaryDiagnosis::model()->find('patient_id=? and disorder_id=? and eye_id=? and date=?',array($patient->id,$disorder->id,$_POST['diagnosis_eye'],$date))) {
			$patient->addDiagnosis($disorder->id, $_POST['diagnosis_eye'], $date);
		}

		$this->redirect(array('patient/view/'.$patient->id));
	}

	public function actionValidateAddDiagnosis()
	{
		$errors = array();

		if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
			throw new Exception("Patient not found: ".@$_POST['patient_id']);
		}

		if (isset($_POST['DiagnosisSelection']['ophthalmic_disorder_id'])) {
			$disorder_id = $_POST['DiagnosisSelection']['ophthalmic_disorder_id'];
		} elseif (isset($_POST['DiagnosisSelection']['systemic_disorder_id'])) {
			$disorder_id = $_POST['DiagnosisSelection']['systemic_disorder_id'];
		}

		$sd = new SecondaryDiagnosis;
		$sd->patient_id = $patient->id;
		$sd->date = @$_POST['fuzzy_year'].'-'.str_pad(@$_POST['fuzzy_month'],2,'0',STR_PAD_LEFT).'-'.str_pad(@$_POST['fuzzy_day'],2,'0',STR_PAD_LEFT);
		$sd->disorder_id = @$disorder_id;
		$sd->eye_id = @$_POST['diagnosis_eye'];

		$errors = array();

		if (!$sd->validate()) {
			foreach ($sd->getErrors() as $field => $_errors) {
				$errors[$field] = $_errors[0];
			}
		}

		// Check the diagnosis isn't currently set at the episode level for this patient
		foreach ($patient->episodes as $episode) {
			if ($episode->disorder_id == $sd->disorder_id && ($episode->eye_id == $sd->eye_id || $episode->eye_id == 3 || $sd->eye_id == 3)) {
				$errors['disorder_id'] = "The disorder is already set at the episode level for this patient";
			}
		}

		// Check that the date isn't in the future
		if (@$_POST['fuzzy_year'] == date('Y')) {
			if (@$_POST['fuzzy_month'] > date('n')) {
				$errors['date'] = "The date cannot be in the future.";
			} elseif (@$_POST['fuzzy_month'] == date('n')) {
				if (@$_POST['fuzzy_day'] > date('j')) {
					$errors['date'] = "The date cannot be in the future.";
				}
			}
		}

		// Check that the date is valid
		$v = new OEFuzzyDateValidator;
		$v->validateAttribute($sd,'date');

		echo json_encode($errors);
	}

	public function actionRemovediagnosis()
	{
		if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
			throw new Exception('Unable to find patient: '.@$_GET['patient_id']);
		}

		$patient->removeDiagnosis(@$_GET['diagnosis_id']);

		echo "success";
	}
	
	public function actionEditOphInfo() 
	{
		$cvi_status = PatientOphInfoCviStatus::model()->findByPk(@$_POST['PatientOphInfo']['cvi_status_id']);

		if (!$cvi_status) {
			throw new Exception('invalid cvi status selection:' . @$_POST['PatientOphInfo']['cvi_status_id']);
		}

		if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
			throw new Exception('Unable to find patient: '.@$_POST['patient_id']);
		}

		$cvi_status_date = $this->processDiagnosisDate();

		if (Yii::app()->request->isAjaxRequest) {
			$test = new PatientOphInfo();
			$test->attributes = array(
					'cvi_status_date' => $cvi_status_date,
					'cvi_status_id' => $cvi_status->id,
					);

			echo CActiveForm::validate($test, null, false);
			Yii::app()->end();
		} else {
			$patient->editOphInfo($cvi_status, $cvi_status_date);

			$this->redirect(array('patient/view/'.$patient->id));
		}
	}

	public function reportDiagnoses($params)
	{
		$patients = array();

		$where = '';
		$select = "p.id as patient_id, p.hos_num, c.first_name, c.last_name";

		if (empty($params['selected_diagnoses'])) {
			return array('patients'=>array());
		}

		$command = Yii::app()->db->createCommand()
			->from("patient p")
			->join("contact c","p.contact_id = c.id");

		if (!empty($params['principal'])) {
			foreach ($params['principal'] as $i => $disorder_id) {
				$command->join("episode e$i","e$i.patient_id = p.id");
				$command->join("eye eye_e_$i","eye_e_$i.id = e$i.eye_id");
				$command->join("disorder disorder_e_$i","disorder_e_$i.id = e$i.disorder_id");
				if ($i>0) $where .= ' and ';
				$where .= "e$i.disorder_id = $disorder_id ";
				$select .= ", e$i.last_modified_date as episode{$i}_date, eye_e_$i.name as episode{$i}_eye, disorder_e_$i.term as episode{$i}_disorder";
			}
		}

		foreach ($params['selected_diagnoses'] as $i => $disorder_id) {
			if (empty($params['principal']) || !in_array($disorder_id,$params['principal'])) {
				$command->join("secondary_diagnosis sd$i","sd$i.patient_id = p.id");
				$command->join("eye eye_sd_$i","eye_sd_$i.id = sd$i.eye_id");
				$command->join("disorder disorder_sd_$i","disorder_sd_$i.id = sd$i.disorder_id");
				if ($where) $where .= ' and ';
				$where .= "sd$i.disorder_id = $disorder_id ";
				$select .= ", sd$i.date as sd{$i}_date, sd$i.eye_id as sd{$i}_eye_id, eye_sd_$i.name as sd{$i}_eye, disorder_sd_$i.term as sd{$i}_disorder";
			}
		}

		$results = array();

		foreach ($command->select($select)->where($where)->queryAll() as $row) {
			$date = $this->reportEarliestDate($row);

			while (isset($results[$date['timestamp']])) {
				$date['timestamp']++;
			}

			$results['patients'][$date['timestamp']] = array(
				'patient_id' => $row['patient_id'],
				'hos_num' => $row['hos_num'],
				'first_name' => $row['first_name'],
				'last_name' => $row['last_name'],
				'date' => $date['date'],
				'diagnoses' => array(),
			);

			foreach ($row as $key => $value) {
				if (preg_match('/^episode([0-9]+)_eye$/',$key,$m)) {
					$results['patients'][$date['timestamp']]['diagnoses'][] = array(
						'eye' => $value,
						'diagnosis' => $row['episode'.$m[1].'_disorder'],
					);
				}
				if (preg_match('/^sd([0-9]+)_eye$/',$key,$m)) {
					$results['patients'][$date['timestamp']]['diagnoses'][] = array(
						'eye' => $value,
						'diagnosis' => $row['sd'.$m[1].'_disorder'],
					);
				}
			}
		}

		ksort($results['patients'], SORT_NUMERIC);

		return $results;
	}

	public function reportEarliestDate($row)
	{
		$dates = array();

		foreach ($row as $key => $value) {
			$value = substr($value,0,10);

			if (preg_match('/_date$/',$key) && !in_array($value,$dates)) {
				$dates[] = $value;
			}
		}

		sort($dates, SORT_STRING);

		if (preg_match('/-00-00$/',$dates[0])) {
			return array(
				'date' => substr($dates[0],0,4),
				'timestamp' => strtotime(substr($dates[0],0,4).'-01-01'),
			);
		} elseif (preg_match('/-00$/',$dates[0])) {
			$date = Helper::getMonthText(substr($dates[0],5,2)).' '.substr($dates[0],0,4);
			return array(
				'date' => $date,
				'timestamp' => strtotime($date),
			);
		}

		return array(
			'date' => date('j M Y',strtotime($dates[0])),
			'timestamp' => strtotime($dates[0]),
		);
	}

	public function actionAddPreviousOperation()
	{
		if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
			throw new Exception("Patient not found:".@$_POST['patient_id']);
		}

		if (!isset($_POST['previous_operation'])) {
			throw new Exception("Missing previous operation text");
		}

		if (@$_POST['edit_operation_id']) {
			if (!$po = PreviousOperation::model()->findByPk(@$_POST['edit_operation_id'])) {
				throw new Exception("Previous operation not found: ".@$_POST['edit_operation_id']);
			}
			$po->side_id = @$_POST['previous_operation_side'] ? @$_POST['previous_operation_side'] : null;
			$po->operation = @$_POST['previous_operation'];
			$po->date = str_pad(@$_POST['fuzzy_year'],4,'0',STR_PAD_LEFT).'-'.str_pad(@$_POST['fuzzy_month'],2,'0',STR_PAD_LEFT).'-'.str_pad(@$_POST['fuzzy_day'],2,'0',STR_PAD_LEFT);
			if (!$po->save()) {
				throw new Exception("Unable to save previous operation: ".print_r($po->getErrors(),true));
			}
		} else {
			$patient->addPreviousOperation(@$_POST['previous_operation'],@$_POST['previous_operation_side'],str_pad(@$_POST['fuzzy_year'],4,'0',STR_PAD_LEFT).'-'.str_pad(@$_POST['fuzzy_month'],2,'0',STR_PAD_LEFT).'-'.str_pad(@$_POST['fuzzy_day'],2,'0',STR_PAD_LEFT));
		}

		$this->redirect(array('/patient/view/'.$patient->id));
	}

	public function actionAddMedication()
	{
		if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
			throw new Exception("Patient not found:".@$_POST['patient_id']);
		}

		if (!$drug = Drug::model()->findByPk(@$_POST['selectedMedicationID'])) {
			throw new Exception("Drug not found: ".@$_POST['selectedMedicationID']);
		}

		if (!$route = DrugRoute::model()->findByPk(@$_POST['route_id'])) {
			throw new Exception("Route not found: ".@$_POST['route_id']);
		}

		if (!empty($route->options)) {
			if (!$option = DrugRouteOption::model()->findByPk(@$_POST['option_id'])) {
				throw new Exception("Route option not found: ".@$_POST['option_id']);
			}
		}

		if (!$frequency = DrugFrequency::model()->findByPk(@$_POST['frequency_id'])) {
			throw new Exception("Frequency not found: ".@$_POST['frequency_id']);
		}

		if (!strtotime(@$_POST['start_date'])) {
			throw new Exception("Invalid date: ".@$_POST['start_date']);
		}

		if (@$_POST['edit_medication_id']) {
			if (!$m = Medication::model()->findByPk(@$_POST['edit_medication_id'])) {
				throw new Exception("Medication not found: ".@$_POST['edit_medication_id']);
			}
			$patient->updateMedication($m,array(
				'drug_id' => $drug->id,
				'route_id' => $route->id,
				'option_id' => @$option ? $option->id : null,
				'frequency_id' => $frequency->id,
				'start_date' => $_POST['start_date'],
			));
		} else {
			$patient->addMedication(array(
				'drug_id' => $drug->id,
				'route_id' => $route->id,
				'option_id' => @$option ? $option->id : null,
				'frequency_id' => $frequency->id,
				'start_date' => $_POST['start_date'],
			));
		}

		$this->redirect(array('/patient/view/'.$patient->id));
	}

	public function actionAddFamilyHistory()
	{
		if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
			throw new Exception("Patient not found:".@$_POST['patient_id']);
		}

		if (!$relative = FamilyHistoryRelative::model()->findByPk(@$_POST['relative_id'])) {
			throw new Exception("Unknown relative: ".@$_POST['relative_id']);
		}

		if (!$side = FamilyHistorySide::model()->findByPk(@$_POST['side_id'])) {
			throw new Exception("Unknown side: ".@$_POST['side_id']);
		}

		if (!$condition = FamilyHistoryCondition::model()->findByPk(@$_POST['condition_id'])) {
			throw new Exception("Unknown condition: ".@$_POST['condition_id']);
		}

		if (@$_POST['edit_family_history_id']) {
			if (!$fh = FamilyHistory::model()->findByPk(@$_POST['edit_family_history_id'])) {
				throw new Exception("Family history not found: ".@$_POST['edit_family_history_id']);
			}
			$fh->relative_id = $relative->id;
			$fh->side_id = $side->id;
			$fh->condition_id = $condition->id;
			$fh->comments = @$_POST['comments'];

			if (!$fh->save()) {
				throw new Exception("Unable to save family history: ".print_r($fh->getErrors(),true));
			}
		} else {
			$patient->addFamilyHistory($relative->id,$side->id,$condition->id,@$_POST['comments']);
		}

		$this->redirect(array('patient/view/'.$patient->id));
	}

	public function actionRemovePreviousOperation()
	{
		if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
			throw new Exception("Patient not found: ".@$_GET['patient_id']);
		}

		if (!$po = PreviousOperation::model()->find('patient_id=? and id=?',array($patient->id,@$_GET['operation_id']))) {
			throw new Exception("Previous operation not found: ".@$_GET['operation_id']);
		}

		if (!$po->delete()) {
			throw new Exception("Failed to remove previous operation: ".print_r($po->getErrors(),true));
		}

		echo 'success';
	}

	public function actionGetPreviousOperation()
	{
		if (!$po = PreviousOperation::model()->findByPk(@$_GET['operation_id'])) {
			throw new Exception("Previous operation not found: ".@$_GET['operation_id']);
		}

		$date = explode('-',$po->date);

		echo json_encode(array(
			'operation' => $po->operation,
			'side_id' => $po->side_id,
			'fuzzy_year' => $date[0],
			'fuzzy_month' => preg_replace('/^0/','',$date[1]),
			'fuzzy_day' => preg_replace('/^0/','',$date[2]),
		));
	}

	public function actionRemoveMedication()
	{
		if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
			throw new Exception("Patient not found: ".@$_GET['patient_id']);
		}

		if (!$m = Medication::model()->find('patient_id=? and id=?',array($patient->id,@$_GET['medication_id']))) {
			throw new Exception("Medication not found: ".@$_GET['medication_id']);
		}

		$m->end_date = date('Y-m-d');

		if (!$m->save()) {
			throw new Exception("Failed to remove medication: ".print_r($m->getErrors(),true));
		}

		echo 'success';
	}

	public function actionRemoveFamilyHistory()
	{
		if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
			throw new Exception("Patient not found: ".@$_GET['patient_id']);
		}

		if (!$m = FamilyHistory::model()->find('patient_id=? and id=?',array($patient->id,@$_GET['family_history_id']))) {
			throw new Exception("Family history not found: ".@$_GET['family_history_id']);
		}

		if (!$m->delete()) {
			throw new Exception("Failed to remove family history: ".print_r($m->getErrors(),true));
		}

		echo 'success';
	}

	public function processJsVars()
	{
		if ($this->patient) {
			$this->jsVars['OE_patient_id'] = $this->patient->id;
		}
		$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
		$subspecialty_id = $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;

		$this->jsVars['OE_subspecialty_id'] = $subspecialty_id;

		parent::processJsVars();
	}

	public function actionInstitutionSites()
	{
		if (!$institution = Institution::model()->findByPk(@$_GET['institution_id'])) {
			throw new Exception("Institution not found: ".@$_GET['institution_id']);
		}

		echo json_encode(CHtml::listData($institution->sites,'id','name'));
	}

	public function actionValidateSaveContact()
	{
		if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
			throw new Exception("Patient not found: ".@$_POST['patient_id']);
		}

		$errors = array();

		if (!$institution = Institution::model()->findByPk(@$_POST['institution_id'])) {
			$errors['institution_id'] = 'Please select an institution';
		}

		if (@$_POST['site_id']) {
			if (!$site = Site::model()->findByPk($_POST['site_id'])) {
				$errors['site_id'] = 'Invalid site';
			}
		}

		if (@$_POST['contact_label_id'] == 'nonophthalmic' && !@$_POST['label_id']) {
			$errors['label_id'] = 'Please select a label';
		}

		$contact = new Contact;

		foreach (array('title','first_name','last_name') as $field) {
			if (!@$_POST[$field]) {
				$errors[$field] = $contact->getAttributeLabel($field).' is required';
			}
		}

		echo json_encode($errors);
	}

	public function actionGetDrugRouteOptions()
	{
		if (!$route = DrugRoute::model()->findByPk(@$_GET['route_id'])) {
			throw new Exception("Drug route not found: ".@$_GET['route_id']);
		}

		$this->renderPartial('_drug_route_options',array('route'=>$route));
	}

	public function actionValidateAddMedication()
	{
		$errors = array();

		if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
			throw new Exception("Patient not found: ".@$_POST['patient_id']);
		}

		if (!Drug::model()->findByPk(@$_POST['selectedMedicationID'])) {
			$errors['selectedMedicationID'] = "Please select a drug";
		}
		if (!$route = DrugRoute::model()->findByPk(@$_POST['route_id'])) {
			$errors['route_id'] = "Please select a route";
		}
		if (!empty($route->options) && !DrugRouteOption::model()->findByPk(@$_POST['option_id'])) {
			$errors['option_id'] = "Please select a route option";
		}
		if (empty($_POST['frequency_id'])) {
			$errors['frequency_id'] = 'Please select a frequency';
		}
		if (empty($_POST['start_date'])) {
			$errors['start_date'] = 'Please select a date';
		} elseif (!strtotime($_POST['start_date'])) {
			$errors['start_date'] = 'Invalid date entered';
		}

		echo json_encode($errors);
	}

	public function actionAddContact()
	{
		if (BaseController::checkUserLevel(4)) {
			if (@$_POST['site_id']) {
				if (!$site = Site::model()->findByPk($_POST['site_id'])) {
					throw new Exception("Site not found: ".$_POST['site_id']);
				}
			} else {
				if (!$institution = Institution::model()->findByPk(@$_POST['institution_id'])) {
					throw new Exception("Institution not found: ".@$_POST['institution_id']);
				}
			}

			// Attempt to de-dupe by looking for an existing record that matches the user's input
			$criteria = new CDbCriteria;
			$criteria->compare('lower(title)',strtolower($_POST['title']));
			$criteria->compare('lower(first_name)',strtolower($_POST['first_name']));
			$criteria->compare('lower(last_name)',strtolower($_POST['last_name']));

			if (isset($site)) {
				$criteria->compare('site_id',$site->id);
			} else {
				$criteria->compare('institution_id',$institution->id);
			}

			if ($contact = Contact::model()->with('locations')->find($criteria)) {
				foreach ($contact->locations as $location) {
					$pca = new PatientContactAssignment;
					$pca->patient_id = $patient->id;
					$pca->location_id = $location->id;
					if (!$pca->save()) {
						throw new Exception("Unable to save patient contact assignment: ".print_r($pca->getErrors(),true));
					}

					$this->redirect(array('/patient/view/'.$patient->id));
				}
			}

			$contact = new Contact;
			$contact->attributes = $_POST;

			if (@$_POST['contact_label_id'] == 'nonophthalmic') {
				if (!$label = ContactLabel::model()->findByPk(@$_POST['label_id'])) {
					throw new Exception("Contact label not found: ".@$_POST['label_id']);
				}
			} else {
				if (!$label = ContactLabel::model()->find('name=?',array(@$_POST['contact_label_id']))) {
					throw new Exception("Contact label not found: ".@$_POST['contact_label_id']);
				}
			}

			$contact->contact_label_id = $label->id;

			if (!$contact->save()) {
				throw new Exception("Unable to save contact: ".print_r($contact->getErrors(),true));
			}

			$cl = new ContactLocation;
			$cl->contact_id = $contact->id;
			if (isset($site)) {
				$cl->site_id = $site->id;
			} else {
				$cl->institution_id = $institution->id;
			}

			if (!$cl->save()) {
				throw new Exception("Unable to save contact location: ".print_r($cl->getErrors(),true));
			}

			$pca = new PatientContactAssignment;
			$pca->patient_id = $patient->id;
			$pca->location_id = $cl->id;

			if (!$pca->save()) {
				throw new Exception("Unable to save patient contact assignment: ".print_r($pca->getErrors(),true));
			}

			$this->redirect(array('/patient/view/'.$patient->id));
		}
	}

	public function actionGetContactLocation()
	{
		if (!$location = ContactLocation::model()->findByPk(@$_GET['location_id'])) {
			throw new Exception("ContactLocation not found: ".@$_GET['location_id']);
		}

		$data = array();

		if ($location->site) {
			$data['institution_id'] = $location->site->institution_id;
			$data['site_id'] = $location->site_id;
		} else {
			$data['institution_id'] = $location->institution_id;
			$data['site_id'] = null;
		}

		$data['contact_id'] = $location->contact_id;
		$data['name'] = $location->contact->fullName;

		echo json_encode($data);
	}

	public function actionValidateEditContact()
	{
		if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
			throw new Exception("Patient not found: ".@$_POST['patient_id']);
		}

		if (!$contact = Contact::model()->findByPk(@$_POST['contact_id'])) {
			throw new Exception("Contact not found: ".@$_POST['contact_id']);
		}

		$errors = array();

		if (!@$_POST['institution_id']) {
			$errors['institution_id'] = 'Please select an institution';
		} else {
			if (!$institution = Institution::model()->findByPk(@$_POST['institution_id'])) {
				throw new Exception("Institution not found: ".@$_POST['institution_id']);
			}
		}

		if (@$_POST['site_id']) {
			if (!$site = Site::model()->findByPk(@$_POST['site_id'])) {
				throw new Exception("Site not found: ".@$_POST['site_id']);
			}
		}

		echo json_encode($errors);
	}

	public function actionEditContact()
	{
		if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
			throw new Exception("Patient not found: ".@$_POST['patient_id']);
		}

		if (!$contact = Contact::model()->findByPk(@$_POST['contact_id'])) {
			throw new Exception("Contact not found: ".@$_POST['contact_id']);
		}

		if (@$_POST['site_id']) {
			if (!$site = Site::model()->findByPk(@$_POST['site_id'])) {
				throw new Exception("Site not found: ".@$_POST['site_id']);
			}
			if (!$cl = ContactLocation::model()->find('contact_id=? and site_id=?',array($contact->id,$site->id))) {
				$cl = new ContactLocation;
				$cl->contact_id = $contact->id;
				$cl->site_id = $site->id;

				if (!$cl->save()) {
					throw new Exception("Unable to save contact location: ".print_r($cl->getErrors(),true));
				}
			}
		} else {
			if (!$institution = Institution::model()->findByPk(@$_POST['institution_id'])) {
				throw new Exception("Institution not found: ".@$_POST['institution_id']);
			}

			if (!$cl = ContactLocation::model()->find('contact_id=? and institution_id=?',array($contact->id,$institution->id))) {
				$cl = new ContactLocation;
				$cl->contact_id = $contact->id;
				$cl->institution_id = $institution->id;

				if (!$cl->save()) {
					throw new Exception("Unable to save contact location: ".print_r($cl->getErrors(),true));
				}
			}
		}

		if (!$pca = PatientContactAssignment::model()->findByPk(@$_POST['pca_id'])) {
			throw new Exception("PCA not found: ".@$_POST['pca_id']);
		}

		$pca->location_id = $cl->id;

		if (!$pca->save()) {
			throw new Exception("Unable to save patient contact assignment: ".print_r($pca->getErrors(),true));
		}

		$this->redirect(array('/patient/view/'.$patient->id));
	}

	public function actionSendSiteMessage()
	{
		$message = Yii::app()->mailer->newMessage();
		$message->setFrom(array($_POST['newsite_from'] => User::model()->findByPk(Yii::app()->user->id)->fullName));
		$message->setTo(array(Yii::app()->params['helpdesk_email']));
		$message->setSubject($_POST['newsite_subject']);
		$message->setBody($_POST['newsite_message']);
		echo Yii::app()->mailer->sendMessage($message) ? '1' : '0';
	}

	public function actionGetMedication()
	{
		if (!$m = Medication::model()->findByPk(@$_GET['medication_id'])) {
			throw new Exception("Medication not found: ".@$_GET['medication_id']);
		}

		echo json_encode(array(
			'drug_id' => $m->drug_id,
			'drug_name' => $m->drug->name,
			'route_id' => $m->route_id,
			'option_id' => $m->option_id,
			'frequency_id' => $m->frequency_id,
			'start_date' => $m->start_date,
			'route_options' => $this->renderPartial('_drug_route_options',array('route'=>$m->route),true),
		));
	}

	public function actionDrugList()
	{
		if (Yii::app()->request->isAjaxRequest) {
			$criteria = new CDbCriteria();
			if (isset($_GET['term']) && $term = $_GET['term']) {
				$criteria->addCondition(array('LOWER(name) LIKE :term', 'LOWER(aliases) LIKE :term'), 'OR');
				$params[':term'] = '%' . strtolower(strtr($term, array('%' => '\%'))) . '%';
			}
			$criteria->order = 'name';
			$criteria->params = $params;
			$drugs = Drug::model()->findAll($criteria);
			$return = array();
			foreach ($drugs as $drug) {
				$return[] = array(
						'label' => $drug->tallmanlabel,
						'value' => $drug->tallman,
						'id' => $drug->id,
				);
			}
			echo CJSON::encode($return);
		}
	}

	public function actionDrugDefaults()
	{
		if (!$drug = Drug::model()->findByPk(@$_GET['drug_id'])) {
			throw new Exception("Unable to save drug: ".print_r($drug->getErrors(),true));
		}

		echo json_encode(array('route_id'=>$drug->default_route_id,'frequency_id'=>$drug->default_frequency_id));
	}

	public function actionAddNewEvent()
	{
		if (!BaseController::checkUserLevel(4)) {
			return;
		}

		if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
			throw new Exception("Patient not found: ".@$_POST['patient_id']);
		}

		if (@$_POST['subspecialty_id']) {
			if (!$subspecialty = Subspecialty::model()->findByPk(@$_POST['subspecialty_id'])) {
				throw new Exception("Subspecialty not found: ".@$_POST['subspecialty_id']);
			}
		}

		$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);

		if (isset($subspecialty)) {
			if (!$firm->serviceSubspecialtyAssignment || $firm->serviceSubspecialtyAssignment->subspecialty_id != $subspecialty->id) {
				$has = false;
				foreach (UserFirm::model()->findAll('user_id=?',array(Yii::app()->user->id)) as $uf) {
					if ($uf->firm->serviceSubspecialtyAssignment && $uf->firm->serviceSubspecialtyAssignment->subspecialty_id == $subspecialty->id) {
						$has = true;
						break;
					}
				}

				if (!$has) {
					echo "0";
					return;
				}
			}
		}

		if ((isset($subspecialty) && $firm->serviceSubspecialtyAssignment && $subspecialty->id == $firm->serviceSubspecialtyAssignment->subspecialty_id) || (!isset($subspecialty) && $firm->serviceSubspecialtyAssignment == null)) {
			return $this->renderPartial('//patient/add_new_event',array(
				'subspecialty' => @$subspecialty,
				'patient' => $patient,
				'eventTypes' => EventType::model()->getEventTypeModules(),
			),false, true);
		}

		$this->renderPartial('/site/change_site_and_firm', array(
			'returnUrl' => @$_POST['returnUrl'],
			'subspecialty' => @$subspecialty,
			'support_services' => (boolean) !@$subspecialty,
			'patient' => $patient,
		), false, true);
	}

	public function actionVerifyAddNewEpisode()
	{
		if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
			throw new Exception("Patient not found: ".@$_GET['patient_id']);
		}

		$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);

		if ($patient->hasOpenEpisodeOfSubspecialty($firm->serviceSubspecialtyAssignment->subspecialty_id)) {
			echo "0";
			return;
		}

		echo "1";
	}

	public function actionAddNewEpisode()
	{
		if (!BaseController::checkUserLevel(4)) {
			return;
		}

		if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
			throw new Exception("Patient not found: ".@$_POST['patient_id']);
		}

		if (!empty($_POST['firm_id'])) {
			$firm = Firm::model()->findByPk($_POST['firm_id']);
			$episode = $patient->addEpisode($firm);

			$this->redirect(array('/patient/episode/'.$episode->id));
		}

		return $this->renderPartial('//patient/add_new_episode',array(
			'patient' => $patient,
			'firm' => Firm::model()->findByPk(Yii::app()->session['selected_firm_id']),
		),false, true);
	}
}
