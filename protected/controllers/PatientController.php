<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
Yii::import('application.controllers.*');

class PatientController extends BaseController
{
    public $layout = '//layouts/main';
    public $renderPatientPanel = true;
    public $patient;
    public $firm;
    public $editable;
    public $editing;
    public $event;
    public $event_type;
    public $title;
    public $event_type_id;
    public $episode;
    public $current_episode;
    public $event_tabs = array();
    public $event_actions = array();
    public $episodes = array();

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('search', 'ajaxSearch', 'view', 'parentEvent', 'gpList', 'practiceList', 'getInternalReferralDocumentListUrl' ),
                'users' => array('@'),
            ),
            array('allow',
                'actions' => array('episode', 'episodes', 'hideepisode', 'showepisode', 'previouselements'),
                'roles' => array('OprnViewClinical'),
            ),
            array('allow',
                'actions' => array('verifyAddNewEpisode', 'addNewEpisode'),
                'roles' => array('OprnCreateEpisode'),
            ),
            array('allow',
                'actions' => array('updateepisode'),  // checked in action
                'users' => array('@'),
            ),
            array('allow',
                'actions' => array('possiblecontacts', 'associatecontact', 'unassociatecontact', 'getContactLocation', 'institutionSites', 'validateSaveContact', 'addContact', 'validateEditContact', 'editContact', 'sendSiteMessage'),
                'roles' => array('OprnEditContact'),
            ),
            array('allow',
                'actions' => array('addAllergy', 'removeAllergy', 'generateAllergySelect', 'addRisk', 'removeRisk'),
                // TODO: check how to add new roles!!!
                'roles' => array('OprnEditAllergy'),
            ),
            array('allow',
                'actions' => array('adddiagnosis', 'validateAddDiagnosis', 'removediagnosis'),
                'roles' => array('OprnEditOtherOphDiagnosis'),
            ),
            array('allow',
                'actions' => array('editOphInfo'),
                'roles' => array('OprnEditOphInfo'),
            ),
            array('allow',
                'actions' => array('addPreviousOperation', 'getPreviousOperation', 'removePreviousOperation'),
                'roles' => array('OprnEditPreviousOperation'),
            ),
            array('allow',
                'actions' => array('addFamilyHistory', 'removeFamilyHistory'),
                'roles' => array('OprnEditFamilyHistory'),
            ),
            array('allow',
                'actions' => array('editSocialHistory', 'editSocialHistory'),
                'roles' => array('OprnEditSocialHistory'),
            ),
            array('allow',
                'actions' => array('create', 'update'),
                'roles' => array('TaskAddPatient'),
            )
        );
    }

    public function behaviors()
    {
        return array(
            'CreateEventBehavior' => array(
                'class' => 'application.behaviors.CreateEventControllerBehavior',
            ),
        );
    }

    protected function beforeAction($action)
    {
        parent::storeData();

        $this->firm = Firm::model()->findByPk($this->selectedFirmId);

        if (!isset($this->firm)) {
            // No firm selected, reject
            throw new CHttpException(403, 'You are not authorised to view this page without selecting a firm.');
        }

        return parent::beforeAction($action);
    }

    /**
     * Displays a particular model.
     *
     * @param int $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        Yii::app()->assetManager->registerScriptFile('js/patientSummary.js');

        $this->patient = $this->loadModel($id);
        
        $tabId = !empty($_GET['tabId']) ? $_GET['tabId'] : 0;
        $eventId = !empty($_GET['eventId']) ? $_GET['eventId'] : 0;

        $episodes = $this->patient->episodes;
        // TODO: verify if ordered_episodes complete supercedes need for unordered $episodes
        $ordered_episodes = $this->patient->getOrderedEpisodes();

        $legacyepisodes = $this->patient->legacyepisodes;
        // NOTE that this is not being used in the render
        $supportserviceepisodes = $this->patient->supportserviceepisodes;

        $properties['patient_id'] = $this->patient->id;
        Audit::add('patient summary', 'view', $id, '', $properties);

        $this->logActivity('viewed patient');

        $episodes_open = 0;
        $episodes_closed = 0;

        foreach ($episodes as $episode) {
            if ($episode->end_date === null) {
                ++$episodes_open;
            } else {
                ++$episodes_closed;
            }
        }

        $this->jsVars['currentContacts'] = $this->patient->currentContactIDS();

        $this->breadcrumbs = array(
            $this->patient->first_name.' '.$this->patient->last_name.'('.$this->patient->hos_num.')',
        );

        $this->checkImportedBiometryEvent();

        $this->render('view', array(
            'tab' => $tabId,
            'event' => $eventId,
            'episodes' => $episodes,
            'ordered_episodes' => $ordered_episodes,
            'legacyepisodes' => $legacyepisodes,
            'episodes_open' => $episodes_open,
            'episodes_closed' => $episodes_closed,
            'firm' => $this->firm,
            'supportserviceepisodes' => $supportserviceepisodes,
        ));
    }

    public function actionSearch()
    {
        $term = \Yii::app()->request->getParam('term', '');

        $patientSearch = new PatientSearch();
	    $dataProvider = $patientSearch->search($term);
	    $itemCount = $dataProvider->getItemCount(); // we could use the $dataProvider->totalItemCount but in the Patient model we set data from the event so needs to be recalculated
	    $search_terms = $patientSearch->getSearchTerms();

        if ($itemCount == 0) {
            Audit::add('search', 'search-results', implode(',', $search_terms).' : No results');

            $message = 'Sorry, no results ';
            if ($search_terms['hos_num']) {
                $message .= 'for Hospital Number <strong>"'.$search_terms['hos_num'].'"</strong>';

                // check if the record was merged into another record
                $criteria = new CDbCriteria();
                $criteria->compare('secondary_hos_num', $search_terms['hos_num']);
                $criteria->compare('status', PatientMergeRequest::STATUS_MERGED);

                $patientMergeRequest = PatientMergeRequest::model()->find($criteria);

                if ($patientMergeRequest) {
                    $message = 'Hospital Number <strong>'.$search_terms['hos_num'].'</strong> was merged into <strong>'.$patientMergeRequest->primary_hos_num.'</strong>';
                }
            } elseif ($search_terms['nhs_num']) {
                $message .= 'for NHS Number <strong>"'.$search_terms['nhs_num'].'"</strong>';
            } elseif ($search_terms['first_name'] && $search_terms['last_name']) {
                $message .= 'for Patient Name <strong>"'.$search_terms['first_name'].' '.$search_terms['last_name'].'"</strong>';
            } else {
                $message .= 'found for your search.';
            }
            Yii::app()->user->setFlash('warning.no-results', $message);

            $this->redirect(Yii::app()->homeUrl);
        } elseif ($itemCount == 1) {
            $item = $dataProvider->getData()[0];
            $api = new CoreAPI();
            $this->redirect(array($api->generateEpisodeLink($item)));
        } else {
            $this->renderPatientPanel = false;

            $this->render('results', array(
                'data_provider' => $dataProvider,
                'page_num' => \Yii::app()->request->getParam('Patient_page', 0),
                'total_items' => $itemCount,
                'term' => $term,
                'search_terms' => $patientSearch->getSearchTerms(),
                'sort_by' => (integer) \Yii::app()->request->getParam('sort_by', null),
                'sort_dir' => (integer) \Yii::app()->request->getParam('sort_dir', null),
            ));
        }
    }

   /**
    * Ajax search.
    */
   public function actionAjaxSearch()
   {
       $term = trim(\Yii::app()->request->getParam('term', ''));
       $result = array();
       $patientSearch = new PatientSearch();
       if ($patientSearch->isValidSearchTerm($term)) {
           $dataProvider = $patientSearch->search($term);
           foreach ($dataProvider->getData() as $patient) {
               $result[] = array(
                   'id' => $patient->id,
                   'first_name' => $patient->first_name,
                   'last_name' => $patient->last_name,
                   'age' => ($patient->isDeceased() ? 'Deceased' : $patient->getAge()),
                   'gender' => $patient->getGenderString(),
                   'genderletter' => $patient->gender,
                   'dob' => ($patient->dob) ? $patient->NHSDate('dob') : 'Unknown',
                   'hos_num' => $patient->hos_num,
                   'nhsnum' => $patient->nhsnum,
                   // in script.js we override the behaviour for showing search results and its require the label key to be present
                   'label' => $patient->first_name.' '.$patient->last_name.' ('.$patient->hos_num.')',
                   'is_deceased' => $patient->is_deceased,
               );
           }
       }
       echo CJavaScript::jsonEncode($result);
       Yii::app()->end();
   }

    public function actionParentEvent($id)
    {
        if (!$event = Event::model()->findByPk($id)) {
            throw new Exception("Event not found: $id");
        }

        if (!$event->parent) {
            throw new Exception("Event has no parent: $id");
        }

        $this->redirect(Yii::app()->createUrl('/'.$event->parent->eventType->class_name.'/default/view/'.$event->parent_id));
    }

    public function actionEpisodes()
    {
        $this->layout = '//layouts/events_and_episodes';
        $this->patient = $this->loadModel($_GET['id']);

        $episodes = $this->patient->episodes;
        $legacyepisodes = $this->patient->legacyepisodes;
        $site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);

        if (!$current_episode = $this->patient->getEpisodeForCurrentSubspecialty()) {
            $current_episode = empty($episodes) ? false : $episodes[0];
            if (!empty($legacyepisodes)) {
                $criteria = new CDbCriteria();
                $criteria->compare('episode_id', $legacyepisodes[0]->id);
                $criteria->order = 'event_date desc, created_date desc';

                foreach (Event::model()->findAll($criteria) as $event) {
                    if (in_array($event->eventType->class_name, Yii::app()->modules) && (!$event->eventType->disabled)) {
                        $this->redirect(array($event->eventType->class_name.'/default/view/'.$event->id));
                        Yii::app()->end();
                    }
                }
            }
        } elseif ($current_episode->end_date == null) {
            $criteria = new CDbCriteria();
            $criteria->compare('episode_id', $current_episode->id);
            $criteria->order = 'event_date desc, created_date desc';

            if ($event = Event::model()->find($criteria)) {
                $this->redirect(array($event->eventType->class_name.'/default/view/'.$event->id));
                Yii::app()->end();
            }
        } else {
            $current_episode = null;
        }

        $this->current_episode = $current_episode;
        $this->title = 'Episode summary';

        $this->render('episodes', array(
            'title' => empty($episodes) ? '' : 'Episode summary',
            'episodes' => $episodes,
            'site' => $site,
            'cssClass' => 'episodes-list',
        ));
    }

    public function actionEpisode($id)
    {
        if (!$this->episode = Episode::model()->findByPk($id)) {
            throw new SystemException('Episode not found: '.$id);
        }

        $this->layout = '//layouts/events_and_episodes';
        $this->patient = $this->episode->patient;

        $episodes = $this->patient->episodes;

        $site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);

        $this->title = 'Episode summary';
        $this->event_tabs = array(
                array(
                        'label' => 'View',
                        'active' => true,
                ),
        );

        if ($this->checkAccess('OprnEditEpisode', $this->firm, $this->episode) && $this->episode->firm) {
            $this->event_tabs[] = array(
                    'label' => 'Edit',
                    'href' => Yii::app()->createUrl('/patient/updateepisode/'.$this->episode->id),
            );
        }
        $this->current_episode = $this->episode;
        $status = Yii::app()->session['episode_hide_status'];
        $status[$id] = true;
        Yii::app()->session['episode_hide_status'] = $status;

        $this->render('episodes', array(
            'title' => empty($episodes) ? '' : 'Episode summary',
            'episodes' => $episodes,
            'site' => $site,
        ));
    }

    public function actionUpdateepisode($id)
    {
        if (!$this->episode = Episode::model()->findByPk($id)) {
            throw new SystemException('Episode not found: '.$id);
        }

        if (!$this->checkAccess('OprnEditEpisode', $this->firm, $this->episode) || isset($_POST['episode_cancel'])) {
            $this->redirect(array('patient/episode/'.$this->episode->id));

            return;
        }

        if (!empty($_POST)) {
            if ((@$_POST['eye_id'] && !@$_POST['DiagnosisSelection']['disorder_id'])) {
                $error = 'Please select a disorder for the principal diagnosis';
            } elseif (!@$_POST['eye_id'] && @$_POST['DiagnosisSelection']['disorder_id']) {
                $error = 'Please select an eye for the principal diagnosis';
            } else {
                if (@$_POST['eye_id'] && @$_POST['DiagnosisSelection']['disorder_id']) {
                    if ($_POST['eye_id'] != $this->episode->eye_id || $_POST['DiagnosisSelection']['disorder_id'] != $this->episode->disorder_id) {
                        $this->episode->setPrincipalDiagnosis($_POST['DiagnosisSelection']['disorder_id'], $_POST['eye_id']);
                    }
                }

                if ($_POST['episode_status_id'] != $this->episode->episode_status_id) {
                    $this->episode->episode_status_id = $_POST['episode_status_id'];

                    if (!$this->episode->save()) {
                        throw new Exception('Unable to update status for episode '.$this->episode->id.' '.print_r($this->episode->getErrors(), true));
                    }
                }

                $this->redirect(array('patient/episode/'.$this->episode->id));
            }
        }

        $this->patient = $this->episode->patient;
        $this->layout = '//layouts/events_and_episodes';

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

        $this->render('episodes', array(
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
     *
     * @param int $id the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model = Patient::model()->findByPk((int) $id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }

    /**
     * Performs the AJAX validation.
     *
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
     * (separate function for unit testing).
     *
     * @param array $data form data of search terms
     *
     * @return CDataProvider
     */
    public function getSearch($data)
    {
        $model = new Patient();
        $model->attributes = $data;

        return $model->search();
    }

    public function getTemplateName($action, $eventTypeId)
    {
        $template = 'eventTypeTemplates'.DIRECTORY_SEPARATOR.$action.DIRECTORY_SEPARATOR.$eventTypeId;

        if (!file_exists(Yii::app()->basePath.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'clinical'.DIRECTORY_SEPARATOR.$template.'.php')) {
            $template = $action;
        }

        return $template;
    }

    /**
     * Get all the elements for a the current module's event type.
     *
     * @param $event_type_id
     *
     * @return array
     */
    public function getDefaultElements($action, $event_type_id = false, $event = false)
    {
        $etc = new BaseEventTypeController(1);
        $etc->event = $event;

        return $etc->getDefaultElements($action, $event_type_id);
    }

    /**
     * Get the optional elements for the current module's event type
     * This will be overriden by the module.
     *
     * @param $event_type_id
     *
     * @return array
     */
    public function getOptionalElements($action, $event = false)
    {
        return array();
    }

    public function actionPossiblecontacts()
    {
        $term = strtolower(trim($_GET['term'])).'%';

        switch (strtolower(@$_GET['filter'])) {
            case 'staff':
                $contacts = User::model()->findAsContacts($term);
                break;
            case 'nonspecialty':
                if (!$specialty = Specialty::model()->find('code=?', array(Yii::app()->params['institution_specialty']))) {
                    throw new Exception('Unable to find specialty: '.Yii::app()->params['institution_specialty']);
                }
                $contacts = Contact::model()->findByLabel($term, $specialty->default_title, true, 'person');
                break;
            default:
                $contacts = Contact::model()->findByLabel($term, @$_GET['filter'], false, null);
        }

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
            if ($gp = Gp::model()->find('contact_id=?', array($contact->id))) {
                if ($gp->id == $patient->gp_id) {
                    return;
                }
            }
        }

        if (isset($location)) {
            if (!$pca = PatientContactAssignment::model()->find('patient_id=? and location_id=?', array($patient->id, $location->id))) {
                $pca = new PatientContactAssignment();
                $pca->patient_id = $patient->id;
                $pca->location_id = $location->id;

                if (!$pca->save()) {
                    throw new Exception('Unable to save patient contact assignment: '.print_r($pca->getErrors(), true));
                }
            }
        } else {
            if (!$pca = PatientContactAssignment::model()->find('patient_id=? and contact_id=?', array($patient->id, $contact->id))) {
                $pca = new PatientContactAssignment();
                $pca->patient_id = $patient->id;
                $pca->contact_id = $contact->id;

                if (!$pca->save()) {
                    throw new Exception('Unable to save patient contact assignment: '.print_r($pca->getErrors(), true));
                }
            }
        }

        $this->renderPartial('_patient_contact_row', array('pca' => $pca));
    }

    public function actionUnassociatecontact()
    {
        if (!$pca = PatientContactAssignment::model()->findByPk(@$_GET['pca_id'])) {
            throw new Exception('Patient contact assignment not found: '.@$_GET['pca_id']);
        }

        if (!$pca->delete()) {
            echo '0';
        } else {
            $pca->patient->audit('patient', 'unassociate-contact');
            echo '1';
        }
    }

    /**
     * Add patient/allergy assignment.
     *
     * @throws Exception
     */
    public function actionAddAllergy()
    {
        if (!empty($_POST)) {
            $patient = $this->fetchModel('Patient', @$_POST['patient_id']);

            if (@$_POST['no_allergies']) {
                $patient->setNoAllergies();
            } else {
                $allergy = $this->fetchModel('Allergy', @$_POST['allergy_id']);
                $patient->addAllergy($allergy, @$_POST['other'], @$_POST['comments']);
            }
        }

        $this->redirect(array('patient/view/'.$patient->id));
    }

    /**
     * Remove patient/allergy assignment.
     *
     * @throws Exception
     */
    public function actionRemoveAllergy()
    {
        PatientAllergyAssignment::model()->deleteByPk(@$_GET['assignment_id']);
        echo 'success';
    }

    /**
     * List of allergies - changed to a wrap function to be able to use a common function from the model.
     */
    public function allergyList()
    {
        return PatientAllergyAssignment::model()->allergyList($this->patient->id);
    }

    /**
     * Generate the select to the frontend for the allergy selection.
     */
    public function actionGenerateAllergySelect()
    {
        $this->patient = $this->loadModel(Yii::app()->getRequest()->getQuery('patient_id'));
        echo CHtml::dropDownList('allergy_id', null, CHtml::listData($this->allergyList(), 'id', 'name'),
            array('empty' => '-- Select --'));
    }

    /**
     * Add patient/allergy assignment.
     *
     * @throws Exception
     */
    public function actionAddRisk()
    {
        if (!empty($_POST)) {
            $patient = $this->fetchModel('Patient', @$_POST['patient_id']);

            if (@$_POST['no_risks']) {
                $patient->setNoRisks();
            } else {
                $risk = $this->fetchModel('Risk', @$_POST['risk_id']);
                $patient->addRisk($risk, @$_POST['other'], @$_POST['comments']);
            }
        }

        $this->redirect(array('patient/view/'.$patient->id));
    }

    /**
     * Remove patient/allergy assignment.
     *
     * @throws Exception
     */
    public function actionRemoveRisk()
    {
        PatientRiskAssignment::model()->deleteByPk(@$_GET['assignment_id']);
        echo 'success';
    }

    /**
     * List of risks.
     */
    public function riskList()
    {
        $risk_ids = array();
        foreach ($this->patient->risks as $risk) {
            if ($risk->name != 'Other') {
                $risk_ids[] = $risk->id;
            }
        }
        $criteria = new CDbCriteria();
        !empty($risk_ids) && $criteria->addNotInCondition('id', $risk_ids);
        $criteria->order = 'name asc';

        return Risk::model()->active()->findAll($criteria);
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

    private function processFuzzyDate()
    {
        return Helper::padFuzzyDate(@$_POST['fuzzy_year'], @$_POST['fuzzy_month'], @$_POST['fuzzy_day']);
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

        $date = $this->processFuzzyDate();

        if (!$_POST['diagnosis_eye']) {
            if (!SecondaryDiagnosis::model()->find('patient_id=? and disorder_id=? and date=?', array($patient->id, $disorder->id, $date))) {
                $patient->addDiagnosis($disorder->id, null, $date);
            }
        } elseif (!SecondaryDiagnosis::model()->find('patient_id=? and disorder_id=? and eye_id=? and date=?', array($patient->id, $disorder->id, $_POST['diagnosis_eye'], $date))) {
            $patient->addDiagnosis($disorder->id, $_POST['diagnosis_eye'], $date);
        }

        $this->redirect(array('patient/view/'.$patient->id));
    }

    public function actionValidateAddDiagnosis()
    {
        $errors = array();

        if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
            throw new Exception('Patient not found: '.@$_POST['patient_id']);
        }

        if (isset($_POST['DiagnosisSelection']['ophthalmic_disorder_id'])) {
            $disorder_id = $_POST['DiagnosisSelection']['ophthalmic_disorder_id'];
        } elseif (isset($_POST['DiagnosisSelection']['systemic_disorder_id'])) {
            $disorder_id = $_POST['DiagnosisSelection']['systemic_disorder_id'];
        }

        $sd = new SecondaryDiagnosis();
        $sd->patient_id = $patient->id;
        $sd->date = $this->processFuzzyDate();
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
                $errors['disorder_id'] = 'The disorder is already set at the episode level for this patient';
            }
        }

        // Check that the date isn't in the future
        if (@$_POST['fuzzy_year'] == date('Y')) {
            if (@$_POST['fuzzy_month'] > date('n')) {
                $errors['date'] = 'The date cannot be in the future.';
            } elseif (@$_POST['fuzzy_month'] == date('n')) {
                if (@$_POST['fuzzy_day'] > date('j')) {
                    $errors['date'] = 'The date cannot be in the future.';
                }
            }
        }

        // Check that the date is valid
        $v = new OEFuzzyDateValidator();
        $v->validateAttribute($sd, 'date');

        echo json_encode($errors);
    }

    public function actionRemovediagnosis()
    {
        if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
            throw new Exception('Unable to find patient: '.@$_GET['patient_id']);
        }

        $patient->removeDiagnosis(@$_GET['diagnosis_id']);

        echo 'success';
    }

    public function actionEditOphInfo()
    {
        $cvi_status = PatientOphInfoCviStatus::model()->findByPk(@$_POST['PatientOphInfo']['cvi_status_id']);

        if (!$cvi_status) {
            throw new Exception('invalid cvi status selection:'.@$_POST['PatientOphInfo']['cvi_status_id']);
        }

        if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
            throw new Exception('Unable to find patient: '.@$_POST['patient_id']);
        }

        $cvi_status_date = $this->processFuzzyDate();

        $result = $patient->editOphInfo($cvi_status, $cvi_status_date);

        echo json_encode($result);
    }

    public function reportDiagnoses($params)
    {
        $patients = array();

        $where = 'p.deleted = 0 ';
        $select = 'p.id as patient_id, p.hos_num, c.first_name, c.last_name';

        if (empty($params['selected_diagnoses'])) {
            return array('patients' => array());
        }

        $command = Yii::app()->db->createCommand()
            ->from('patient p')
            ->join('contact c', 'p.contact_id = c.id');

        if (!empty($params['principal'])) {
            foreach ($params['principal'] as $i => $disorder_id) {
                $command->join("episode e$i", "e$i.patient_id = p.id");
                $command->join("eye eye_e_$i", "eye_e_$i.id = e$i.eye_id");
                $command->join("disorder disorder_e_$i", "disorder_e_$i.id = e$i.disorder_id");
                $where .= "e$i.disorder_id = $disorder_id and e$i.deleted = 0 and disorder_e_$i.deleted = 0 ";
                $select .= ", e$i.last_modified_date as episode{$i}_date, eye_e_$i.name as episode{$i}_eye, disorder_e_$i.term as episode{$i}_disorder";
            }
        }

        foreach ($params['selected_diagnoses'] as $i => $disorder_id) {
            if (empty($params['principal']) || !in_array($disorder_id, $params['principal'])) {
                $command->join("secondary_diagnosis sd$i", "sd$i.patient_id = p.id");
                $command->join("eye eye_sd_$i", "eye_sd_$i.id = sd$i.eye_id");
                $command->join("disorder disorder_sd_$i", "disorder_sd_$i.id = sd$i.disorder_id");
                $where .= "sd$i.disorder_id = $disorder_id and sd$i.deleted = 0 and disorder_sd_$i.deleted = 0 ";
                $select .= ", sd$i.date as sd{$i}_date, sd$i.eye_id as sd{$i}_eye_id, eye_sd_$i.name as sd{$i}_eye, disorder_sd_$i.term as sd{$i}_disorder";
            }
        }

        $results = array();

        foreach ($command->select($select)->where($where)->queryAll() as $row) {
            $date = $this->reportEarliestDate($row);

            while (isset($results[$date['timestamp']])) {
                ++$date['timestamp'];
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
                if (preg_match('/^episode([0-9]+)_eye$/', $key, $m)) {
                    $results['patients'][$date['timestamp']]['diagnoses'][] = array(
                        'eye' => $value,
                        'diagnosis' => $row['episode'.$m[1].'_disorder'],
                    );
                }
                if (preg_match('/^sd([0-9]+)_eye$/', $key, $m)) {
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
            $value = substr($value, 0, 10);

            if (preg_match('/_date$/', $key) && !in_array($value, $dates)) {
                $dates[] = $value;
            }
        }

        sort($dates, SORT_STRING);

        if (preg_match('/-00-00$/', $dates[0])) {
            return array(
                'date' => substr($dates[0], 0, 4),
                'timestamp' => strtotime(substr($dates[0], 0, 4).'-01-01'),
            );
        } elseif (preg_match('/-00$/', $dates[0])) {
            $date = Helper::getMonthText(substr($dates[0], 5, 2)).' '.substr($dates[0], 0, 4);

            return array(
                'date' => $date,
                'timestamp' => strtotime($date),
            );
        }

        return array(
            'date' => date('j M Y', strtotime($dates[0])),
            'timestamp' => strtotime($dates[0]),
        );
    }

    public function actionAddPreviousOperation()
    {
        if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
            throw new Exception('Patient not found:'.@$_POST['patient_id']);
        }

        if (!isset($_POST['previous_operation'])) {
            throw new Exception('Missing previous operation text');
        }

        if (@$_POST['edit_operation_id']) {
            if (!$po = PreviousOperation::model()->findByPk(@$_POST['edit_operation_id'])) {
                $po = new PreviousOperation();
            }
        } else {
            $po = new PreviousOperation();
        }

        $po->patient_id = $patient->id;
        $po->side_id = @$_POST['previous_operation_side'] ? @$_POST['previous_operation_side'] : null;
        $po->operation = @$_POST['previous_operation'];
        $po->date = $this->processFuzzyDate();

        if ($po->date == '0000-00-00') {
            $po->date = null;
        }

        if (!$po->save()) {
            echo json_encode($po->getErrors());

            return;
        }

        echo json_encode(array());
    }

    public function actionEditSocialHistory()
    {
        if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
            throw new Exception('Patient not found:'.@$_POST['patient_id']);
        }
        if (!$social_history = SocialHistory::model()->find('patient_id=?', array($patient->id))) {
            $social_history = new SocialHistory();
        }
        $social_history->patient_id = $patient->id;
        $social_history->attributes = $_POST['SocialHistory'];
        if (!$social_history->save()) {
            throw new Exception('Unable to save social history: '.print_r($social_history->getErrors(), true));
        } else {
            $this->redirect(array('patient/view/'.$patient->id));
        }
    }

    public function actionAddFamilyHistory()
    {
        if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
            throw new Exception('Patient not found:'.@$_POST['patient_id']);
        }

        if (@$_POST['no_family_history']) {
            $patient->setNoFamilyHistory();
        } else {
            if (!$relative = FamilyHistoryRelative::model()->findByPk(@$_POST['relative_id'])) {
                throw new Exception('Unknown relative: '.@$_POST['relative_id']);
            }

            if (!$side = FamilyHistorySide::model()->findByPk(@$_POST['side_id'])) {
                throw new Exception('Unknown side: '.@$_POST['side_id']);
            }

            if (!$condition = FamilyHistoryCondition::model()->findByPk(@$_POST['condition_id'])) {
                throw new Exception('Unknown condition: '.@$_POST['condition_id']);
            }

            if (@$_POST['edit_family_history_id']) {
                if (!$fh = FamilyHistory::model()->findByPk(@$_POST['edit_family_history_id'])) {
                    throw new Exception('Family history not found: '.@$_POST['edit_family_history_id']);
                }
                $fh->relative_id = $relative->id;
                if ($relative->is_other) {
                    $fh->other_relative = @$_POST['other_relative'];
                }
                $fh->side_id = $side->id;
                $fh->condition_id = $condition->id;
                if ($condition->is_other) {
                    $fh->other_condition = @$_POST['other_condition'];
                }
                $fh->comments = @$_POST['comments'];

                if (!$fh->save()) {
                    throw new Exception('Unable to save family history: '.print_r($fh->getErrors(), true));
                }
            } else {
                $patient->addFamilyHistory($relative->id, @$_POST['other_relative'], $side->id, $condition->id, @$_POST['other_condition'], @$_POST['comments']);
            }
        }

        $this->redirect(array('patient/view/'.$patient->id));
    }

    public function actionRemovePreviousOperation()
    {
        if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
            throw new Exception('Patient not found: '.@$_GET['patient_id']);
        }

        if (!$po = PreviousOperation::model()->find('patient_id=? and id=?', array($patient->id, @$_GET['operation_id']))) {
            throw new Exception('Previous operation not found: '.@$_GET['operation_id']);
        }

        if (!$po->delete()) {
            throw new Exception('Failed to remove previous operation: '.print_r($po->getErrors(), true));
        }

        echo 'success';
    }

    public function actionGetPreviousOperation()
    {
        if (!$po = PreviousOperation::model()->findByPk(@$_GET['operation_id'])) {
            throw new Exception('Previous operation not found: '.@$_GET['operation_id']);
        }

        $date = explode('-', $po->date);

        echo json_encode(array(
            'operation' => $po->operation,
            'side_id' => $po->side_id,
            'fuzzy_year' => $date[0],
            'fuzzy_month' => preg_replace('/^0/', '', $date[1]),
            'fuzzy_day' => preg_replace('/^0/', '', $date[2]),
        ));
    }

    public function actionRemoveFamilyHistory()
    {
        if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
            throw new Exception('Patient not found: '.@$_GET['patient_id']);
        }

        if (!$m = FamilyHistory::model()->find('patient_id=? and id=?', array($patient->id, @$_GET['family_history_id']))) {
            throw new Exception('Family history not found: '.@$_GET['family_history_id']);
        }

        if (!$m->delete()) {
            throw new Exception('Failed to remove family history: '.print_r($m->getErrors(), true));
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
            throw new Exception('Institution not found: '.@$_GET['institution_id']);
        }

        echo json_encode(CHtml::listData($institution->sites, 'id', 'name'));
    }

    public function actionValidateSaveContact()
    {
        if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
            throw new Exception('Patient not found: '.@$_POST['patient_id']);
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

        if (@$_POST['contact_label_id'] == 'nonspecialty' && !@$_POST['label_id']) {
            $errors['label_id'] = 'Please select a label';
        }

        $contact = new Contact();

        foreach (array('title', 'first_name', 'last_name') as $field) {
            if (!@$_POST[$field]) {
                $errors[$field] = $contact->getAttributeLabel($field).' is required';
            }
        }

        echo json_encode($errors);
    }

    public function actionAddContact()
    {
        if (@$_POST['site_id']) {
            if (!$site = Site::model()->findByPk($_POST['site_id'])) {
                throw new Exception('Site not found: '.$_POST['site_id']);
            }
        } else {
            if (!$institution = Institution::model()->findByPk(@$_POST['institution_id'])) {
                throw new Exception('Institution not found: '.@$_POST['institution_id']);
            }
        }
        if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
            throw new Exception('patient required for contact assignment');
        }

        // Attempt to de-dupe by looking for an existing record that matches the user's input
        $criteria = new CDbCriteria();
        $criteria->compare('lower(title)', strtolower($_POST['title']));
        $criteria->compare('lower(first_name)', strtolower($_POST['first_name']));
        $criteria->compare('lower(last_name)', strtolower($_POST['last_name']));

        if (isset($site)) {
            $criteria->compare('site_id', $site->id);
        } else {
            $criteria->compare('institution_id', $institution->id);
        }

        if ($contact = Contact::model()->with('locations')->find($criteria)) {
            foreach ($contact->locations as $location) {
                $pca = new PatientContactAssignment();
                $pca->patient_id = $patient->id;
                $pca->location_id = $location->id;
                if (!$pca->save()) {
                    throw new Exception('Unable to save patient contact assignment: '.print_r($pca->getErrors(), true));
                }

                $this->redirect(array('/patient/view/'.$patient->id));
            }
        }

        $contact = new Contact();
        $contact->attributes = $_POST;

        if (@$_POST['contact_label_id'] == 'nonspecialty') {
            if (!$label = ContactLabel::model()->findByPk(@$_POST['label_id'])) {
                throw new Exception('Contact label not found: '.@$_POST['label_id']);
            }
        } else {
            if (!$label = ContactLabel::model()->find('name=?', array(@$_POST['contact_label_id']))) {
                throw new Exception('Contact label not found: '.@$_POST['contact_label_id']);
            }
        }

        $contact->contact_label_id = $label->id;

        if (!$contact->save()) {
            throw new Exception('Unable to save contact: '.print_r($contact->getErrors(), true));
        }

        $cl = new ContactLocation();
        $cl->contact_id = $contact->id;
        if (isset($site)) {
            $cl->site_id = $site->id;
        } else {
            $cl->institution_id = $institution->id;
        }

        if (!$cl->save()) {
            throw new Exception('Unable to save contact location: '.print_r($cl->getErrors(), true));
        }

        $pca = new PatientContactAssignment();
        $pca->patient_id = $patient->id;
        $pca->location_id = $cl->id;

        if (!$pca->save()) {
            throw new Exception('Unable to save patient contact assignment: '.print_r($pca->getErrors(), true));
        }

        $this->redirect(array('/patient/view/'.$patient->id));
    }

    public function actionGetContactLocation()
    {
        if (!$location = ContactLocation::model()->findByPk(@$_GET['location_id'])) {
            throw new Exception('ContactLocation not found: '.@$_GET['location_id']);
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
            throw new Exception('Patient not found: '.@$_POST['patient_id']);
        }

        if (!$contact = Contact::model()->findByPk(@$_POST['contact_id'])) {
            throw new Exception('Contact not found: '.@$_POST['contact_id']);
        }

        $errors = array();

        if (!@$_POST['institution_id']) {
            $errors['institution_id'] = 'Please select an institution';
        } else {
            if (!$institution = Institution::model()->findByPk(@$_POST['institution_id'])) {
                throw new Exception('Institution not found: '.@$_POST['institution_id']);
            }
        }

        if (@$_POST['site_id']) {
            if (!$site = Site::model()->findByPk(@$_POST['site_id'])) {
                throw new Exception('Site not found: '.@$_POST['site_id']);
            }
        }

        echo json_encode($errors);
    }

    public function actionEditContact()
    {
        if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
            throw new Exception('Patient not found: '.@$_POST['patient_id']);
        }

        if (!$contact = Contact::model()->findByPk(@$_POST['contact_id'])) {
            throw new Exception('Contact not found: '.@$_POST['contact_id']);
        }

        if (@$_POST['site_id']) {
            if (!$site = Site::model()->findByPk(@$_POST['site_id'])) {
                throw new Exception('Site not found: '.@$_POST['site_id']);
            }
            if (!$cl = ContactLocation::model()->find('contact_id=? and site_id=?', array($contact->id, $site->id))) {
                $cl = new ContactLocation();
                $cl->contact_id = $contact->id;
                $cl->site_id = $site->id;

                if (!$cl->save()) {
                    throw new Exception('Unable to save contact location: '.print_r($cl->getErrors(), true));
                }
            }
        } else {
            if (!$institution = Institution::model()->findByPk(@$_POST['institution_id'])) {
                throw new Exception('Institution not found: '.@$_POST['institution_id']);
            }

            if (!$cl = ContactLocation::model()->find('contact_id=? and institution_id=?', array($contact->id, $institution->id))) {
                $cl = new ContactLocation();
                $cl->contact_id = $contact->id;
                $cl->institution_id = $institution->id;

                if (!$cl->save()) {
                    throw new Exception('Unable to save contact location: '.print_r($cl->getErrors(), true));
                }
            }
        }

        if (!$pca = PatientContactAssignment::model()->findByPk(@$_POST['pca_id'])) {
            throw new Exception('PCA not found: '.@$_POST['pca_id']);
        }

        $pca->location_id = $cl->id;

        if (!$pca->save()) {
            throw new Exception('Unable to save patient contact assignment: '.print_r($pca->getErrors(), true));
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

    public function actionVerifyAddNewEpisode()
    {
        if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
            throw new Exception('Patient not found: '.@$_GET['patient_id']);
        }

        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);

        if ($patient->hasOpenEpisodeOfSubspecialty($firm->getSubspecialtyID())) {
            echo '0';

            return;
        }

        echo '1';
    }

    /**
     * @return mixed|string
     * @throws Exception
     * @deprecated - since version 2.0
     */
    public function actionAddNewEpisode()
    {
        if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
            throw new Exception('Patient not found: '.@$_POST['patient_id']);
        }

        if (!empty($_POST['firm_id'])) {
            $firm = Firm::model()->findByPk($_POST['firm_id']);
            if (!$episode = $patient->getOpenEpisodeOfSubspecialty($firm->getSubspecialtyID())) {
                $episode = $patient->addEpisode($firm);
            }

            $this->redirect(array('/patient/episode/'.$episode->id));
        }

        return $this->renderPartial('//patient/add_new_episode', array(
            'patient' => $patient,
            'firm' => Firm::model()->findByPk(Yii::app()->session['selected_firm_id']),
        ), false, true);
    }

    public function getEpisodes()
    {
        if ($this->patient && empty($this->episodes)) {
            $this->episodes = array(
                'ordered_episodes' => $this->patient->getOrderedEpisodes(),
                'legacyepisodes' => $this->patient->legacyepisodes,
                'supportserviceepisodes' => $this->patient->supportserviceepisodes,
            );
        }

        return $this->episodes;
    }

    /**
     * Check create access for the specified event type.
     *
     * @param Episode   $episode
     * @param EventType $event_type
     *
     * @return bool
     */
    public function checkCreateAccess(Episode $episode, EventType $event_type)
    {
        $oprn = 'OprnCreate'.($event_type->class_name == 'OphDrPrescription' ? 'Prescription' : 'Event');

        return $this->checkAccess($oprn, $this->firm, $episode, $event_type);
    }

    /**
     * Check for new imported biometry event.
     */
    private function checkImportedBiometryEvent()
    {
        // we need to be sure that Biometry module is installed
        if (isset(Yii::app()->modules['OphInBiometry'])) {
            $criteria = new CDbCriteria();
            $criteria->addCondition("is_linked=0 AND patient_id='".$this->patient->id."'");
            $resultSet = OphInBiometry_Imported_Events::model()->findAll($criteria);
            if ($resultSet) {
                Yii::app()->user->setFlash('alert.unlinked_biometry_event',
                    'A new biometry report is available for this patient - please create a biometry event to view it ');
            }
        }
    }

    /**
     * @param $area
     */
    public function renderModulePartials($area)
    {
        if (isset(Yii::app()->params['module_partials'][$area])) {
            foreach (Yii::app()->params['module_partials'][$area] as $module => $partials) {
                if ($api = Yii::app()->moduleAPI->get($module)) {
                    foreach ($partials as $partial) {
                        if ($viewFile = $api->findViewFile('patientSummary', $partial)) {
                            $this->renderFile($viewFile, array(
                                'patient' => $this->patient,
                                'api' => $api,
                            ));
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        Yii::app()->assetManager->registerScriptFile('js/patient.js');
        //Don't render patient summary box on top as we have no selected patient
        $this->renderPatientPanel = false;
       
        $patient = new Patient('manual');
        $patient->noPas();
        $contact = new Contact('manualAddPatient');
        $address = new Address();

        $this->performAjaxValidation(array($patient, $contact, $address));
        
        if( isset($_POST['Contact'], $_POST['Address'], $_POST['Patient']) )
        {   
            $contact->attributes = $_POST['Contact'];
            $patient->attributes = $_POST['Patient'];
            $address->attributes = $_POST['Address'];

            // not to be sync with PAS
            $patient->is_local = 1;
            
            list($contact, $patient, $address) = $this->performPatientSave($contact, $patient, $address);
        }
        
        $this->render('crud/create',array(
                        'patient' => $patient,
                        'contact' => $contact,
                        'address' => $address,
        ));
   }
   
   /**
    * Saving the Contact, Patient and Address object
    * 
    * @param Contact $contact
    * @param Patient $patient
    * @param Address $address
    * @return array on validation error returns the 3 objects otherwise redirects to the patient view page
    */
   private function performPatientSave(Contact $contact, Patient $patient, Address $address)
   {
        $transaction = Yii::app()->db->beginTransaction();
        try{
            if( $contact->save() ){
                $patient->contact_id = $contact->id;
                $address->contact_id = $contact->id;
                $action = $patient->isNewRecord ? 'add' : 'edit';
                $isNewPatient = $patient->isNewRecord ? true : false;

                $issetGeneticsModule = isset(Yii::app()->modules["Genetics"]);
                $issetGeneticsClinical = Yii::app()->user->checkAccess('Genetics Clinical');

                if($patient->save() && $address->save()){
                    $transaction->commit();

                    if(($issetGeneticsModule !== FALSE ) && ($issetGeneticsClinical !== FALSE) && ($isNewPatient)){
                        $this->redirect(array('Genetics/subject/edit?patient='.$patient->id));
                    } else {
                        Audit::add('Patient', $action . '-patient', "Patient manually [id: $patient->id] {$action}ed.");
                        $this->redirect(array('view', 'id' => $patient->id));
                    }

                } else {
                    // patient or address failed to save
                    $transaction->rollback();

                    // to show validation error messages to the user
                    $patient->validate();
                    $address->validate();
                }
            } else {
                // to show validation error messages to the user
                $patient->validate();
                $address->validate();

                // remove contact_id validation error
                $patient->clearErrors('contact_id');
                $address->clearErrors('contact_id');

                // contact failed to save
                $transaction->rollback();
            }

        } catch (Exception $ex) {
            OELog::logException($ex);
            $transaction->rollback();
        }
        
        return array($contact, $patient, $address);
   }
   
    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        Yii::app()->assetManager->registerScriptFile('js/patient.js');
        
        //Don't render patient summary box on top as we have no selected patient
        $this->renderPatientPanel = false;

        $patient = $this->loadModel($id);
        $patient->scenario = 'manual';
        
        //only local patient can be edited
        if($patient->is_local == 0){
            Yii::app()->user->setFlash('warning.update-patient', 'Only local patients can be edited.');
            $this->redirect(array('view', 'id' => $patient->id));
        }
        
        $contact = $patient->contact ? $patient->contact : new Contact();
        $address = $patient->contact->address ? $patient->contact->address : new Address();
        
        $this->performAjaxValidation(array($patient, $contact, $address));

        if( isset($_POST['Contact'], $_POST['Address'], $_POST['Patient']) )
        {
            $contact->attributes = $_POST['Contact'];
            $patient->attributes = $_POST['Patient'];
            $address->attributes = $_POST['Address'];

            // not to be sync with PAS
            $patient->is_local = 1;

            list($contact, $patient, $address) = $this->performPatientSave($contact, $patient, $address);
        }

        $this->render('crud/update',array(
                        'patient' => $patient,
                        'contact' => $contact,
                        'address' => $address,
        ));
    }
    
    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        $patient = $this->loadModel($id);
        $patient->deleted = 1;
        $patient->save();
        
        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax'])){
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('site'));
        }
    }
    
    public function actionGpList($term)
    {
        $criteria = new CDbCriteria;
        $criteria->addSearchCondition('first_name', '', true, 'OR');
        $criteria->addSearchCondition('LOWER(first_name)', '', true, 'OR');
        $criteria->addSearchCondition('last_name', '', true, 'OR');
        $criteria->addSearchCondition('LOWER(last_name)', '', true, 'OR');
        
        $criteria->addSearchCondition('concat(first_name, " ", last_name)', $term, true, 'OR');
        $criteria->addSearchCondition('LOWER(concat(first_name, " ", last_name))', $term, true, 'OR');
        
        $gps = Gp::model()->with('contact')->findAll($criteria);
        
        $output = array();
        foreach($gps as $gp){
            $output[] = array(
                'label' => $gp->correspondenceName,
                'value' => $gp->id
            );
        }
        
        echo CJSON::encode($output);
        
        Yii::app()->end();
    }
    
    public function actionPracticeList($term)
    {
        $term = strtolower($term);
        
        $criteria = new CDbCriteria;
        $criteria->join = 'JOIN contact on t.contact_id = contact.id';
        $criteria->join .= '  JOIN address on contact.id = address.contact_id';
        $criteria->addCondition('( (date_end is NULL OR date_end > NOW()) AND (date_start is NULL OR date_start < NOW()))');
        
        $criteria->addSearchCondition('LOWER(CONCAT_WS(", ", address1, address2, city, county, postcode))', $term);
        
        $practices = Practice::model()->findAll($criteria);
        
        $output = array();
        foreach($practices as $practice){
            $output[] = array(
                'label' => $practice->getAddressLines(),
                'value' => $practice->id
            );
        }
        
        echo CJSON::encode($output);
        
        Yii::app()->end();
    }

    public function actionGetInternalReferralDocumentListUrl($id)
    {
        $patient = $this->loadModel($id);

        if ($component = $this->getApp()->getComponent('internalReferralIntegration')) {
            $link = $component->generateUrlForDocumentList($patient);
        }

        echo \CJSON::encode(array('link' => $link));
        $this->getApp()->end();
    }

    /**
     * Ajax method for viewing previous elements.
     *
     * @param int $element_type_id
     * @param int $patient_id
     * @param int $limit
     *
     * @throws CHttpException
     */
    public function actionPreviousElements($element_type_id, $patient_id, $limit = null)
    {
        $element_type = ElementType::model()->findByPk($element_type_id);
        if (!$element_type) {
            throw new CHttpException(404, 'Unknown ElementType');
        }
        $this->patient = Patient::model()->findByPk($patient_id);
        if (!$this->patient) {
            throw new CHttpException(404, 'Unknown Patient');
        }

        $api = $element_type->eventType->getApi();
        $result = array();
        $criteria = new CDbCriteria();
        if ($limit) {
            $criteria->limit = $limit;
        }
        foreach ($api->getElements($element_type->class_name, $this->patient, false, null, $criteria) as $element) {
            // Note when there are more complex elements required for this,
            // would recommend pushing this into a base method that can then
            // be overridden as appropriate
            $result[] = array_merge(
                array('subspecialty' => $element->event->episode->getSubspecialtyText(),
                    'event_date' => $element->event->NHSDate('event_date')),
                $element->getDisplayAttributes()
            );
        }

        echo CJSON::encode($result);
    }
}
