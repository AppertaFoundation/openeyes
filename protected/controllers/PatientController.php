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

	public function printActions() {
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
		$this->patient = $this->loadModel($id);

		$tabId = !empty($_GET['tabId']) ? $_GET['tabId'] : 0;
		$eventId = !empty($_GET['eventId']) ? $_GET['eventId'] : 0;

		$episodes = $this->patient->episodes;
		// TODO: verify if ordered_episodes complete supercedes need for unordered $episodes
		$ordered_episodes = $this->patient->getOrderedEpisodes();
		
		$legacyepisodes = $this->patient->legacyepisodes;

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

		$this->render('view', array(
			'tab' => $tabId, 'event' => $eventId, 'episodes' => $episodes, 'ordered_episodes' => $ordered_episodes, 'legacyepisodes' => $legacyepisodes, 'episodes_open' => $episodes_open, 'episodes_closed' => $episodes_closed
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
			$this->redirect(array('/patient/view/'.$patient->id));
		} else {
			throw new CHttpException(404, 'Hospital number not found');
		}
	}

	public function actionSearch() {
		
		// Check that we have a valid set of search criteria
		$search_terms = array(
				'hos_num' => null,
				'nhs_num' => null,
				'first_name' => null,
				'last_name' => null,
		);
		foreach($search_terms as $search_term => $search_value) {
			if(isset($_GET[$search_term]) && $search_value = trim($_GET[$search_term])) {
				
				// Pad hos_num
				if($search_term == 'hos_num') {
					$search_value = sprintf('%07s',$search_value);
				}
				
				$search_terms[$search_term] = $search_value;
			}
		}
		// if we are on a dev environment, this allows more flexible search terms (i.e. just a first name or surname - useful for testing
		// the multiple search results view. If we are live, enforces controls over search terms.
		if(!YII_DEBUG && !$search_terms['hos_num'] && !$search_terms['nhs_num'] && !($search_terms['first_name'] && $search_terms['last_name'])) {
			Yii::app()->user->setFlash('warning.invalid-search', 'Please enter a valid search.');
			$this->redirect('/');
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
		$page_num = (integer)@$_GET['page_num'];
		$page_size = 20;
		
		$model = new Patient();
		$model->hos_num = $search_terms['hos_num'];
		$model->nhs_num = $search_terms['nhs_num'];
		$dataProvider = $model->search(array(
			'currentPage' => $page_num,
			'pageSize' => $page_size,
			'sortBy' => $sort_by,
			'sortDir'=> $sort_dir,
			'first_name' => $search_terms['first_name'],
			'last_name' => $search_terms['last_name'],
		));
		$nr = $model->search_nr(array(
			'first_name' => $search_terms['first_name'],
			'last_name' => $search_terms['last_name'],
		));

		if ($nr == 0) {
			Audit::add('search','search-results',implode(',',$search_terms) ." : No results");

			$message = 'Sorry, no results ';
			if($search_terms['hos_num']) {
				$message .= 'for Hospital Number <strong>"'.$search_terms['hos_num'].'"</strong>';
			} else if($search_terms['nhs_num']) {
				$message .= 'for NHS Number <strong>"'.$search_terms['nhs_num'].'"</strong>';
			} else if($search_terms['first_name'] && $search_terms['last_name']) {
				$message .= 'for Patient Name <strong>"'.$search_terms['first_name'] . ' ' . $search_terms['last_name'].'"</strong>';
			} else {
				$message .= 'found for your search.';
			}
			Yii::app()->user->setFlash('warning.no-results', $message);
			$this->redirect('/');
		} else if($nr == 1) {
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
				'sort_by' => (integer)@$_GET['sort_by'],
				'sort_dir' => (integer)@$_GET['sort_dir']
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
		$this->patient = $this->loadModel($_GET['id']);

		$episodes = $this->patient->episodes;
		// TODO: verify if ordered_episodes complete supercedes need for unordered $episodes
		$ordered_episodes = $this->patient->getOrderedEpisodes();
		$legacyepisodes = $this->patient->legacyepisodes;
		$site = Site::model()->findByPk(Yii::app()->request->cookies['site_id']->value);

		if (!$current_episode = $this->patient->getEpisodeForCurrentSubspecialty()) {
			$current_episode = empty($episodes) ? false : $episodes[0];
			if (!empty($legacyepisodes)) {
				$criteria = new CDbCriteria;
				$criteria->compare('episode_id',$legacyepisodes[0]->id);
				$criteria->order = 'datetime desc';

				if ($event = Event::model()->find($criteria)) {
					if (!$event->eventType->disabled) {
						$this->redirect(array($event->eventType->class_name.'/default/view/'.$event->id));
						Yii::app()->end();
					}
				}
			}
		} else if ($current_episode->end_date == null) {
			$criteria = new CDbCriteria;
			$criteria->compare('episode_id',$current_episode->id);
			$criteria->order = 'datetime desc';

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

		$site = Site::model()->findByPk(Yii::app()->request->cookies['site_id']->value);

		$this->title = 'Episode summary';
		$this->event_tabs = array(
				array(
						'label' => 'View',
						'active' => true,
				)
		);
		if ($this->episode->editable
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
			return $this->redirect(array('patient/episode/'.$this->episode->id));
		}

		if (isset($_POST['episode_save'])) {
			if ((@$_POST['eye_id'] && !@$_POST['DiagnosisSelection']['disorder_id'])) {
				$error = "Please select a disorder for the principal diagnosis";
			} else if (!@$_POST['eye_id'] && @$_POST['DiagnosisSelection']['disorder_id']) {
				$error = "Please select an eye for the principal diagnosis";
			} else {
				if (@$_POST['eye_id'] && @$_POST['DiagnosisSelection']['disorder_id']) {
					if ($_POST['eye_id'] != $this->episode->eye_id || $_POST['DiagnosisSelection']['disorder_id'] != $this->episode->disorder_id) {
						$this->episode->setPrincipalDiagnosis($_POST['DiagnosisSelection']['disorder_id'],$_POST['eye_id']);
					}
				}

				if ($_POST['episode_status_id'] != $this->episode->episode_status_id) {
					$this->episode->episode_status_id = $_POST['episode_status_id'];
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

		$site = Site::model()->findByPk(Yii::app()->request->cookies['site_id']->value);

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
			'eventTypes' => EventType::model()->getEventTypeModules(),
			'site' => $site,
			'current_episode' => $this->episode,
			'error' => @$error,
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
	public function getDefaultElements($action, $event_type_id=false, $event=false) {
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
	public function getOptionalElements($action, $event=false) {
		return array();
	}

	public function actionPossiblecontacts() {
		$contacts = array();

		$term = strtolower(trim($_GET['term'])).'%';

		if (@$_GET['filter'] == 'gp') {
			$where = "parent_class = 'Gp'";
		} else if (@$_GET['filter'] == 'consultant') {
			$where = "parent_class = 'Consultant'";
		} else if (@$_GET['filter'] == 'specialist') {
			$where = "parent_class = 'Specialist'";
		} else if (@$_GET['filter'] == 'moorfields') {
			$where = "user_id is not null";
		} else {
			$where = "parent_class in ('Consultant','Specialist')";
		}

		foreach (Yii::app()->db->createCommand()
			->select('contact.*, user_contact_assignment.user_id as user_id, user.active')
			->from('contact')
			->leftJoin('user_contact_assignment','user_contact_assignment.contact_id = contact.id')
			->leftJoin('user','user_contact_assignment.user_id = user.id')
			->where("LOWER(contact.last_name) LIKE :term AND $where and (user_contact_assignment.id is null or active != 0)", array(':term' => $term))
			->order('title asc, contact.first_name asc, contact.last_name asc')
			->queryAll() as $contact) {

			$line = trim($contact['title'].' '.$contact['first_name'].' '.$contact['last_name'].' ('.$contact['parent_class']);

			if ($contact['parent_class'] == 'Consultant') {
				$institutions = array();

				if ($contact['title']) {
					$line = trim($contact['title'].' '.$contact['first_name'].' '.$contact['last_name'].' ('.$contact['parent_class']." Ophthalmologist");
				}

				$found_locations = false;

				foreach (SiteConsultantAssignment::model()->findAll('consultant_id = :consultantId',array(':consultantId'=>$contact['parent_id'])) as $sca) {
					if (!in_array($sca->site->institution_id,$institutions)) {
						$institutions[] = $sca->site->institution_id;
					}

					$contacts[] = array(
						'line' => $line.', '.$sca->site->name.')',
						'contact_id' => $contact['id'],
						'site_id' => $sca->site_id,
					);

					$found_locations = true;
				}

				foreach (InstitutionConsultantAssignment::model()->findAll('consultant_id = :consultantId',array(':consultantId'=>$contact['parent_id'])) as $ica) {
					if (!in_array($ica->institution_id,$institutions)) {

						$contacts[] = array(
							'line' => $line.', '.$ica->institution->name.')',
							'contact_id' => $contact['id'],
							'institution_id' => $ica->institution_id,
						);

						$found_locations = true;
					}
				}

				if ($contact['user_id'] && !$found_locations) {
					$institution = Institution::model()->findByPk(1);

					$contacts[] = array(
						'line' => $line.', '.$institution->name.')',
						'contact_id' => $contact['id'],
						'institution_id' => $institution->id,
					);
				}

			} else if ($contact['parent_class'] == 'Specialist') {
				$sites = array();

				foreach (SiteSpecialistAssignment::model()->findAll('specialist_id = :specialistId',array(':specialistId'=>$contact['parent_id'])) as $ica) {
					if (!in_array($ica->site_id,$sites)) {
						if ($contact['title']) {
							$contact_line = $contact['title'].' '.$contact['first_name'].' '.$contact['last_name'];
						} else {
							$contact_line = $contact['first_name'].' '.$contact['last_name'];
						}

						$specialist = Specialist::model()->findByPk($contact['parent_id']);

						$contact_line .= " (".$specialist->specialist_type->name.", ".$ica->site->name.")";

						$contacts[] = array(
							'line' => $contact_line,
							'contact_id' => $contact['id'],
							'site_id' => $ica->site_id,
						);
					}
				}

				$institutions = array();

				foreach (InstitutionSpecialistAssignment::model()->findAll('specialist_id = :specialistId',array(':specialistId'=>$contact['parent_id'])) as $ica) {
					if (!in_array($ica->institution_id,$institutions)) {
						if ($contact['title']) {
							$contact_line = $contact['title'].' '.$contact['first_name'].' '.$contact['last_name'];
						} else {
							$contact_line = $contact['first_name'].' '.$contact['last_name'];
						}

						$specialist = Specialist::model()->findByPk($contact['parent_id']);

						$contact_line .= " (".$specialist->specialist_type->name.", ".$ica->institution->name.")";

						$contacts[] = array(
							'line' => $contact_line,
							'contact_id' => $contact['id'],
							'institution_id' => $ica->institution_id,
						);
					}
				}
			} else if ($contact['user_id']) {
				$user = User::model()->findByPk($contact['user_id']);

				if (!($role = $user->role)) {
					$role = 'Staff';
				}

				$institution = Institution::model()->find('code=?',array('RP6'));

				$contacts[] = array(
					'line' => trim($contact['title'].' '.$contact['first_name'].' '.$contact['last_name']).' ('.$role.', '.$institution->name.')',
					'contact_id' => $contact['id'],
					'institution_id' => $institution->id,
				);

			} else {
				$contact = Contact::model()->findByPk($contact['id']);

				$contacts[] = array(
					'line' => $line.($contact->address ? ', '.$contact->address->address1 : '').')',
					'contact_id' => $contact['id'],
				);
			}
		}

		sort($contacts);

		echo CJavaScript::jsonEncode($contacts);
	}

	public function actionAssociatecontact() {
		if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
			throw new Exception('Patient not found: '.@$_GET['patient_id']);
		}

		$params = $_GET;

		if (!$contact = Contact::model()->findByPk($params['contact_id'])) {
			throw new Exception("Can't find contact: ".$params['contact_id']);
		}

		if ($contact->parent_class == 'Specialist') {
			$specialist = Specialist::model()->findByPk($contact->parent_id);
			$type = $specialist->specialist_type->name;
		} else if ($contact->parent_class == 'Consultant') {
			$type = 'Consultant Ophthalmologist';
		} else if ($uca = UserContactAssignment::model()->find('contact_id=?',array($contact->id))) {
			if (!($type = $uca->user->role)) {
				$type = 'Staff';
			}
		} else {
			$type = $contact->parent_class;
		}

		$data = array(
			'id' => $contact->id,
			'name' => trim($contact->title.' '.$contact->first_name.' '.$contact->last_name),
			'qualifications' => $contact->qualifications,
			'type' => $type,
			'site_id' => '',
			'institution_id' => '',
		);

		if (isset($params['site_id'])) {
			$data['location'] = Site::model()->findByPk($params['site_id'])->name;
			$data['site_id'] = $params['site_id'];
		} else if (isset($params['institution_id'])) {
			$data['location'] = Institution::model()->findByPk($params['institution_id'])->name;
			$data['institution_id'] = $params['institution_id'];
		} else if ($contact->address) {
			$data['location'] = $contact->address->address1;
		}

		foreach ($data as $key => $value) {
			if ($value == null) {
				$data[$key] = '';
			}
		}

		if ($contact->parent_class == 'Gp') {
			$gp = Gp::model()->findByPk($contact->parent_id);
			if ($patient->gp->id == $gp->id) {
				echo json_encode(array());
				return;
			}
		}

		$whereClause = 'patient_id=? and contact_id=?';
		$whereParams = array($patient->id,$contact->id);

		if (isset($params['site_id'])) {
			$whereClause .= ' and site_id=?';
			$whereParams[] = $params['site_id'];
		}
		if (isset($params['institution_id'])) {
			$whereClause .= ' and institution_id=?';
			$whereParams[] = $params['institution_id'];
		}

		if (!$pca = PatientContactAssignment::model()->find($whereClause,$whereParams)) {
			$pca = new PatientContactAssignment;
			$pca->patient_id = $patient->id;
			$pca->contact_id = $contact->id;
			if (isset($params['site_id'])) {
				$pca->site_id = $params['site_id'];
			}
			if (isset($params['institution_id'])) {
				$pca->institution_id = $params['institution_id'];
			}
			$pca->save();

			$patient->audit('patient','associate-contact',$pca->getAuditAttributes());
		}

		echo json_encode($data);
	}

	public function actionUnassociatecontact() {
		if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
			throw new Exception('Patient not found: '.@$_GET['patient_id']);
		}
		if (!$contact = Contact::model()->findByPk(@$_GET['contact_id'])) {
			throw new Exception('Contact not found: '.@$_GET['contact_id']);
		}

		if (@$_GET['site_id']) {
			if (!$site = Site::model()->findByPk($_GET['site_id'])) {
				throw new Exception('Site not found: '.$_GET['site_id']);
			}
		}

		if (@$_GET['institution_id']) {
			if (!$institution = Institution::model()->findByPk($_GET['institution_id'])) {
				throw new Exception('Institution not found: '.$_GET['institution_id']);
			}
		}

		$whereClause = 'patient_id=? and contact_id=?';
		$whereParams = array($patient->id,$contact->id);

		if (isset($site)) {
			$whereClause .= ' and site_id=?';
			$whereParams[] = $site->id;
		}
		if (isset($institution)) {
			$whereClause .= ' and institution_id=?';
			$whereParams[] = $institution->id;
		}

		if ($pca = PatientContactAssignment::model()->find($whereClause,$whereParams)) {
			if (!$pca->delete()) {
				echo "0";
				return;
			}

			$patient->audit('patient','unassociate-contact',$pca->getAuditAttributes());
		}

		echo "1";
	}

	/**
	 * Add patient/allergy assignment
	 * @param integer $patient_id
	 * @param integer $allergy_id
	 * @throws Exception
	 */
	public function actionAddAllergy() {
		if (!empty($_POST)) {
			if(!isset($_POST['patient_id']) || !$patient_id = $_POST['patient_id']) {
				throw new Exception('Patient ID required');
			}
			if(!$patient = Patient::model()->findByPk($patient_id)) {
				throw new Exception('Patient not found: '.$patient_id);
			}
			if(!isset($_POST['allergy_id']) || !$allergy_id = $_POST['allergy_id']) {
				throw new Exception('Allergy ID required');
			}
			if(!$allergy = Allergy::model()->findByPk($allergy_id)) {
				throw new Exception('Allergy not found: '.$allergy_id);
			}
			$patient->addAllergy($allergy_id);
		}
	}

	/**
	 * Remove patient/allergy assignment
	 * @param integer $patient_id
	 * @param integer $allergy_id
	 * @throws Exception
	 */
	public function actionRemoveAllergy() {
		if(!isset($_POST['patient_id']) || !$patient_id = $_POST['patient_id']) {
			throw new Exception('Patient ID required');
		}
		if(!$patient = Patient::model()->findByPk($patient_id)) {
			throw new Exception('Patient not found: '.$patient_id);
		}
		if(!isset($_POST['allergy_id']) || !$allergy_id = $_POST['allergy_id']) {
			throw new Exception('Allergy ID required');
		}
		if(!$allergy = Allergy::model()->findByPk($allergy_id)) {
			throw new Exception('Allergy not found: '.$allergy_id);
		}
		$patient->removeAllergy($allergy_id);
	}

	/**
	 * List of allergies
	 */
	public function allergyList() {
		return Allergy::model()->findAll(array('order' => 'name'));
	}

	public function actionHideepisode() {
		$status = Yii::app()->session['episode_hide_status'];

		if (isset($_GET['episode_id'])) {
			$status[$_GET['episode_id']] = false;
		}

		Yii::app()->session['episode_hide_status'] = $status;
	}

	public function actionShowepisode() {
		$status = Yii::app()->session['episode_hide_status'];
	 
		if (isset($_GET['episode_id'])) {
			$status[$_GET['episode_id']] = true;
		}
	 
		Yii::app()->session['episode_hide_status'] = $status;
	}
	
	private function processDiagnosisDate() {
		$date = $_POST['diagnosis_year'];
		
		if ($_POST['diagnosis_month']) {
			$date .= '-'.str_pad($_POST['diagnosis_month'],2,'0',STR_PAD_LEFT);
		} else {
			$date .= '-00';
		}
		
		if ($_POST['diagnosis_day']) {
			$date .= '-'.str_pad($_POST['diagnosis_day'],2,'0',STR_PAD_LEFT);
		} else {
			$date .= '-00';
		}
		
		return $date;
	}

	public function actionAdddiagnosis() {
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
			if (!SecondaryDiagnosis::model()->find('patient_id=? and disorder_id=?',array($patient->id,$disorder->id))) {
				$patient->addDiagnosis($disorder->id,null,$date);
			}
		} else if (!SecondaryDiagnosis::model()->find('patient_id=? and disorder_id=? and eye_id=?',array($patient->id,$disorder->id,$_POST['diagnosis_eye']))) {
			$patient->addDiagnosis($disorder->id, $_POST['diagnosis_eye'], $date);
		}

		$this->redirect(array('patient/view/'.$patient->id));
	}

	public function actionRemovediagnosis() {
		if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
			throw new Exception('Unable to find patient: '.@$_GET['patient_id']);
		}

		$patient->removeDiagnosis(@$_GET['diagnosis_id']);

		echo "success";
	}
	
	public function actionEditOphInfo() {
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
		}
		else {
			$patient->editOphInfo($cvi_status, $cvi_status_date);
			
			$this->redirect(array('patient/view/'.$patient->id));
		}
	}

	public function reportDiagnoses($params) {
		$patients = array();

		$where = '';
		$select = "p.id as patient_id, p.hos_num, c.first_name, c.last_name";

		if (empty($params['selected_diagnoses'])) {
			return array('patients'=>array());
		}

		$command = Yii::app()->db->createCommand()
			->from("patient p")
			->join("contact c","c.parent_class = 'Patient' and c.parent_id = p.id");

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

			while(isset($results[$date['timestamp']])) {
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

	public function reportEarliestDate($row) {
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
		} else if (preg_match('/-00$/',$dates[0])) {
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
}
