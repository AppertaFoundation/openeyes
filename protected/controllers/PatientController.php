<?php

/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
use OEModule\BreakGlass\BreakGlass;
Yii::import('application.controllers.*');

/**
 * Class PatientController
 *
 * @property Episode $episode
 * @property Patient $patient
 */
class PatientController extends BaseController
{
    public $layout = '//layouts/home';
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
    public $iframe_policy = '';

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('deactivatePlansProblems', 'updatePlansProblems'),
                'roles' => array('Edit'),
            ),
            array(
                'allow',
                'actions' => array('search', 'ajaxSearch', 'view', 'parentEvent', 'gpList', 'gpListRp', 'practiceList', 'getInternalReferralDocumentListUrl', 'getPastWorklistPatients', 'getCitoUrl', 'showCurrentPathway'),
                'users' => array('@'),
            ),
            array(
                'allow',
                'actions' => array('episode', 'episodes', 'hideepisode', 'showepisode', 'previouselements', 'oescape', 'lightningViewer', 'summary'),
                'roles' => array('OprnViewClinical'),
            ),
            array(
                'allow',
                'actions' => array('verifyAddNewEpisode', 'addNewEpisode'),
                'roles' => array('OprnCreateEpisode'),
            ),
            array(
                'allow',
                'actions' => array('updateepisode'),  // checked in action
                'users' => array('@'),
            ),
            array(
                'allow',
                'actions' => array('possiblecontacts', 'associatecontact', 'unassociatecontact', 'getContactLocation', 'institutionSites', 'validateSaveContact', 'addContact', 'validateEditContact', 'editContact', 'sendSiteMessage'),
                'roles' => array('OprnEditContact'),
            ),
            array(
                'allow',
                'actions' => array('addAllergy', 'removeAllergy', 'generateAllergySelect', 'addRisk', 'removeRisk'),
                // TODO: check how to add new roles!!!
                'roles' => array('OprnEditAllergy'),
            ),
            array(
                'allow',
                'actions' => array('adddiagnosis', 'validateAddDiagnosis', 'removediagnosis'),
                'roles' => array('OprnEditOtherOphDiagnosis'),
            ),
            array(
                'allow',
                'actions' => array('editOphInfo'),
                'roles' => array('OprnEditOphInfo'),
            ),
            array(
                'allow',
                'actions' => array('addPreviousOperation', 'getPreviousOperation', 'removePreviousOperation'),
                'roles' => array('OprnEditPreviousOperation'),
            ),
            array(
                'allow',
                'actions' => array('addFamilyHistory', 'removeFamilyHistory'),
                'roles' => array('OprnEditFamilyHistory'),
            ),
            array(
                'allow',
                'actions' => array('editSocialHistory', 'editSocialHistory'),
                'roles' => array('OprnEditSocialHistory'),
            ),
            array(
                'allow',
                'actions' => array('create', 'update', 'findDuplicates', 'findDuplicatesByIdentifier'),
                'roles' => array('TaskAddPatient'),
            ),
            array(
                'allow',
                'actions' => array('summary'),
                'roles' => array('User'),
            ),
            array(
                'allow',
                'actions' => array('getHieSource'),
                'roles' => array('View clinical'),
            ),
            array(
                'allow',
                'actions' => array('delete'),
                'roles' => array('OprnDeletePatient')
            )
        );
    }

    public function behaviors()
    {
        return array(
            'CreateEventBehavior' => ['class' => 'application.behaviors.CreateEventControllerBehavior',],
        );
    }

    public function beforeAction($action)
    {
        parent::storeData();

        $this->firm = Firm::model()->findByPk($this->selectedFirmId);

        if (!isset($this->firm)) {
            // No firm selected, reject
            throw new CHttpException(403, 'You are not authorised to view this page without selecting a firm.');
        }

        return parent::beforeAction($action);
    }

    public function afterAction($action)
    {
        $worklist_patient_id = \Yii::app()->request->getQuery('worklist_patient_id', null);
        $worklist_patient = WorklistPatient::model()->findByPk($worklist_patient_id);

        if ($worklist_patient && ($this->patient->id === $worklist_patient->patient->id)) {
            //store the worklist_patient ID to use later when we create an event
            $worklist_manager = new WorklistManager();
            $worklist_manager->setWorklistPatientId($worklist_patient->id);
        }

        parent::afterAction($action);
    }

    /**
     * Displays a particular model.
     *
     * @param int $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->redirect(array('summary', 'id' => $id));
    }

    /**
     * @param $id Patient ID
     */
    public function actionGetHieSource($id)
    {
        $errors = [];
        $url = '';

        try {
            $this->patient = Patient::model()->findByPk($id);

            if (\SettingMetadata::model()->checkSetting('hie_remote_url', '')) {
                throw new Exception("HIE remote url not exists.");
            }

            if (is_null($this->patient)) {
                throw new Exception("Patient not found: $id");
            }

            $nhs_number = $this->patient->getNhs();
            if (strlen($nhs_number) === 0) {
                throw new Exception("NHS number is missing.");
            }

            if ($component = $this->getApp()->getComponent('hieIntegration')) {
                $url = $component->generateHieUrl($this->patient, $nhs_number);
            }

            if ($url === '') {
                throw new Exception("Empty Url.");
            }

            $data = $component->getData();
            \Audit::add(
                'search',
                'search',
                json_encode(array_merge($data, ['patient_id' => $this->patient->id])),
                $log_message = 'HIE search: patinet id = ' . $this->patient->id,
            );
        } catch (Exception $exception) {
            \Yii::log($exception);
            $errors[] = $exception->getMessage();
        }

        $episodes = $this->patient->episodes;
        $support_service_episodes = $this->patient->supportserviceepisodes;
        $events = $this->patient->getEvents();
        $legacy_episodes = $this->patient->legacyepisodes;
        $no_episodes = (count($episodes) < 1 || count($events) < 1) && count($support_service_episodes) < 1 && count($legacy_episodes) < 1;

        if ($no_episodes) {
            $this->layout = '//layouts/events_and_episodes_no_header';
        } else {
            $this->layout = '//layouts/events_and_episodes';
        }

        $hie_url = \SettingMetadata::model()->getSetting('hie_remote_url');
        if (filter_var($hie_url, FILTER_VALIDATE_URL)) {
            $iframe_policy = " frame-src {$hie_url} localhost:* blob:;";
        } else {
            $iframe_policy = " frame-src 'self' localhost:* blob:;";
        }

        $this->iframe_policy = $iframe_policy;
        $this->render('hie_view', array(
            'encrypted_url' => $url,
            'patient' => $this->patient,
            'errors' => $errors
        ));
    }

    public function actionSummary($id)
    {
        $this->layout = '//layouts/events_and_episodes';
        $this->patient = $this->loadModel($id, false);

        // if the ids are different, it means the $id belongs to a merged patient
        if ($id !== $this->patient->id) {
            $link = (new CoreAPI())->generatePatientLandingPageLink($this->patient);
            // using redirect to correct the url and to avoid issues from creating events
            $this->redirect("$link");
        }

            $this->layout = '//layouts/events_and_episodes';
            $this->patient = $this->loadModel($id, false);
        // if the ids are different, it means the $id belongs to a merged patient
        if ($id !== $this->patient->id) {
            $link = (new CoreAPI())->generatePatientLandingPageLink($this->patient);
            // using redirect to correct the url and to avoid issues from creating events
            $this->redirect("$link");
        }
        if (Yii::app()->params['breakglass_enabled']) {
            $breakGlass = new BreakGlass($this->patient, Yii::app()->user);
            if ($breakGlass->breakGlassRequired()) {
                $this->redirect($breakGlass->getPath());
            }
        }
            $this->pageTitle = "Patient Overview";
            $this->patient->audit('patient', 'view-summary');

            $episodes = $this->patient->episodes;
            $legacy_episodes = $this->patient->legacyepisodes;
            $support_service_episodes = $this->patient->supportserviceepisodes;

            $criteria = new \CDbCriteria();
            $criteria->with = ['episode', 'episode.patient'];
            $criteria->addCondition('patient.id=:patient_id');
            $criteria->params['patient_id'] = $this->patient->id;
            $criteria->order = 't.last_modified_date desc';
            $criteria->limit = 3;
            $events = Event::model()->findAll($criteria);

            $criteria->compare('t.deleted', 0);
            $criteria->addCondition('episode.change_tracker IS NULL OR episode.change_tracker = 0');
            $active_events = Event::model()->findAll($criteria);

            $no_episodes = (count($episodes) < 1 || count($events) < 1) && count($support_service_episodes) < 1 && count($legacy_episodes) < 1;

        if ($no_episodes) {
            $this->layout = '//layouts/events_and_episodes_no_header';
        }

            $this->render('landing_page', array(
            'events' => $events,
            'active_events' => $active_events,
            'patient' => $this->patient,
            'no_episodes' => $no_episodes,
            ));
    }

        /**
        * Inactivate plan for given patient
        *
        * @param $plan_id
        * @param $patient_id
        * @throws Exception
        */
    public function actionDeactivatePlansProblems($plan_id, $patient_id)
    {
        $plan = PlansProblems::model()->findByPk($plan_id);
        $plan->active = false;
        $plan->save();

        echo $this->actionGetPlansProblems($patient_id, true);
    }

        /**
        * Get a list of plans and problems for given patient
        *
        * @param $patient_id
        * @return false|string
        */
    public function actionGetPlansProblems($patient_id, $inc_deactive = false)
    {
        $criteria = new CDbCriteria();
        if (!$inc_deactive) {
            $criteria->addCondition("active=1");
        }
        $criteria->addCondition("patient_id=:patient_id");
        $criteria->params[":patient_id"] = $patient_id;

        $plans_problems = PlansProblems::model()->findAll($criteria);
        $plans = [];
        foreach ($plans_problems as $plan_problem) {
            $user_created = $plan_problem->createdUser;
            $last_modifier = $plan_problem->lastModifiedUser;

            $attributes = $plan_problem->attributes;
            $attributes['title'] = ($user_created ? 'by ' . $user_created->getFullNameAndTitle() : '');
            $attributes['create_at'] = \Helper::convertDate2NHS($plan_problem->created_date);
            $attributes['last_modified'] = \Helper::convertDate2NHS($plan_problem->last_modified_date);
            $attributes['last_modified_by'] = ($last_modifier ? 'by ' . $last_modifier->getFullNameAndTitle() : '');
            $plans[] = $attributes;
        }

        return json_encode($plans);
    }

        /**
        * Save the new plans and update old ones
        *
        * @param $plan_ids
        * @param $new_plan
        * @param $patient_id
        */
    public function actionUpdatePlansProblems()
    {
        $request = Yii::app()->request;
        $plan_ids = $request->getPost('plan_ids');
        $new_plan = $request->getPost('new_plan');
        $patient_id = $request->getPost('patient_id');

        $transaction = \Yii::app()->db->beginTransaction();
        try {
            if ($new_plan) {
                $display_order = (is_array($plan_ids) ? count($plan_ids) + 1 : 1);
                $plan_name = strip_tags($new_plan);
                $plan = new PlansProblems();
                $plan->name = $plan_name;
                $plan->display_order = $display_order;
                $plan->patient_id = $patient_id;
                if (!$plan->validate()) {
                    $this->validationFailed($plan);
                }
                $plan->save();
            }

            if ($plan_ids) {
                foreach ($plan_ids as $display_order => $plan_id) {
                    if ($plan_id) {
                        $plan = PlansProblems::model()->findByPk($plan_id);
                        $plan->display_order = $display_order + 1;
                        if (!$plan->validate()) {
                            $this->validationFailed($plan);
                        }
                        $plan->save();
                    }
                }
            }

            $transaction->commit();
        } catch (Exception $exception) {
            \Yii::log($exception);
            $transaction->rollback();
        }


        echo $this->actionGetPlansProblems($patient_id);
    }

    protected function validationFailed($plan)
    {
        header("HTTP/1.0 400 Bad Request");
        \Yii::log($plan->getErrors());
        die(json_encode($plan->getErrors()));
    }

    public function actionSearch()
    {
        $term = \Yii::app()->request->getParam('term', '');
        $patient_identifier_type_id = \Yii::app()->request->getParam('patient_identifier_type_id');

        $patient_search = new PatientSearch(true);

        if ($patient_identifier_type_id) {
            // if set we import/save Patient from this PAS - no update -
            // this happens after user picks a patient from patient result page
            $patient_search->saveFromPASbyTypeId($patient_identifier_type_id);
        }

        $data_provider = $patient_search->search($term);

        // we could use the $dataProvider->totalItemCount but in the Patient model we set data from the event so needs to be recalculated
        $item_count = $data_provider->getItemCount(); // DO NOT USE ->totalItemCount or ->getTotalItemCount
        $search_terms = $patient_search->getSearchTerms();

        if ($item_count == 1) {
            $patient = $data_provider->getData()[0];
            $api = new CoreAPI();

            // in case the PASAPI returns 1 new patient we perform a new search
            if ($patient->isNewRecord) {
                if ($patient->localIdentifiers) {
                    // in theory, PASAPI should populate 'localIdentifiers' relation
                    $this->redirect(['/patient/search', 'term' => $patient->localIdentifiers[0]->value]);
                }
            }

            $this->redirect(array($api->generatePatientLandingPageLink($patient)));
        } else {
            if ($item_count == 0) {
                $message = 'Sorry, no results ';
                if ($search_terms['patient_identifier_value']) {
                    $message .= 'for <strong>' . $term . '</strong>';

                    $audit_search = $search_terms;
                    $audit_search['patient_identifier_value'] = implode(', ', $audit_search['patient_identifier_value']);

                    Audit::add('search', 'search-results', implode(',', $audit_search) . ' : No results');

                    // check if the record was merged into another record
                    $criteria = new CDbCriteria();
                    $criteria->compare('secondary_local_identifier_value', $search_terms['patient_identifier_value']);
                    $criteria->compare('status', PatientMergeRequest::STATUS_MERGED);

                    $patientMergeRequest = PatientMergeRequest::model()->find($criteria);

                    if ($patientMergeRequest) {
                        $message = 'Identifier <strong>' . implode(', ', $search_terms['patient_identifier_value']) . '</strong> was merged into <strong>' . $patientMergeRequest->primary_local_identifier_value . '</strong>';
                    }
                } elseif ($search_terms['first_name'] && $search_terms['last_name']) {
                    $message .= 'for Patient Name/DOB <strong>"' . $search_terms['first_name'] . ' ' . $search_terms['last_name'] . (isset($search_terms['dob']) ? ' ' . $search_terms['dob'] : '') . '"</strong>';
                } else {
                    $message .= 'found for your search.';
                }
                Yii::app()->user->setFlash('warning.no-results', $message);

                Yii::app()->session['search_term'] = $term;
                Yii::app()->session->close();

                $this->redirect(Yii::app()->homeUrl);
            } elseif ($item_count > 1) {
                $this->renderPatientPanel = false;
                $this->pageTitle = $term . ' - Search';
                $this->fixedHotlist = false;
                $this->render('results', array(
                    'data_provider' => $data_provider,
                    'page_num' => \Yii::app()->request->getParam('Patient_page', 0),
                    'total_items' => $item_count,
                    'term' => $term,
                    'search_terms' => $patient_search->getSearchTerms(),
                    'sort_by' => (int)\Yii::app()->request->getParam('sort_by', null),
                    'sort_dir' => (int)\Yii::app()->request->getParam('sort_dir', null),
                ));
            }
        }
    }

        /**
        * Ajax search.
        */
    public function actionAjaxSearch()
    {
        $institution_id = \Institution::model()->getCurrent()->id;
        $site_id = Yii::app()->session['selected_site_id'];
        $term = trim(\Yii::app()->request->getParam('term', ''));
        $result = array();
        $patient_search = new PatientSearch();
        if ($patient_search->getValidSearchTerm($term)) {
            $data_provider = $patient_search->search($term);
            foreach ($data_provider->getData() as $patient) {
                $pi = [];
                foreach ($patient->identifiers as $identifier) {
                    $pi[] = [
                        'title' => $identifier->patientIdentifierType->long_title ?? $identifier->patientIdentifierType->short_title,
                        'value' => $identifier->value
                    ];
                }

                $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(
                    Yii::app()->params['display_primary_number_usage_code'],
                    $patient->id,
                    $institution_id,
                    $site_id
                );

                $secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient(
                    Yii::app()->params['display_secondary_number_usage_code'],
                    $patient->id,
                    $institution_id,
                    $site_id
                );

                $result[] = array(
                    'id' => $patient->id,
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'age' => ($patient->isDeceased() ? 'Deceased' : $patient->getAge()),
                    'gender' => $patient->getGenderString(),
                    'genderletter' => $patient->gender,
                    'dob' => ($patient->dob) ? $patient->NHSDate('dob') : 'Unknown',
                    'secondary_identifier_value' => PatientIdentifierHelper::getIdentifierValue($secondary_identifier),
                    'is_deceased' => $patient->is_deceased,
                    'patient_identifiers' => $pi,
                    'primary_patient_identifiers' => [
                        'title' => PatientIdentifierHelper::getIdentifierPrompt($primary_identifier),
                        'value' => PatientIdentifierHelper::getIdentifierValue($primary_identifier)
                    ]
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

        $this->redirect(Yii::app()->createUrl('/' . $event->parent->eventType->class_name . '/default/view/' . $event->parent_id));
    }

    public function actionEpisodes()
    {
        $this->layout = '//layouts/events_and_episodes';
        $this->patient = $this->loadModel($_GET['id'], false);
        $this->pageTitle = 'Episodes';

        //if $this->patient was merged we redirect the user to the primary patient's page
        $this->redirectIfMerged();

        $episodes = $this->patient->episodes;
        $legacyepisodes = $this->patient->legacyepisodes;
        $supportserviceepisodes = $this->patient->supportserviceepisodes;
        $site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);

        if (!$current_episode = $this->patient->getEpisodeForCurrentSubspecialty()) {
            $current_episode = empty($episodes) ? false : $episodes[0];
            if (!empty($legacyepisodes)) {
                $criteria = new CDbCriteria();
                $criteria->compare('episode_id', $legacyepisodes[0]->id);
                $criteria->order = 'event_date desc, created_date desc';

                foreach (Event::model()->findAll($criteria) as $event) {
                    if (in_array($event->eventType->class_name, Yii::app()->modules) && (!$event->eventType->disabled)) {
                        $this->redirect(array($event->eventType->class_name . '/default/view/' . $event->id));
                        Yii::app()->end();
                    }
                }
            }
        } elseif ($current_episode->end_date == null) {
            $criteria = new CDbCriteria();
            $criteria->compare('episode_id', $current_episode->id);
            $criteria->order = 'event_date desc, created_date desc';

            if ($event = Event::model()->find($criteria)) {
                $this->redirect(array($event->eventType->class_name . '/default/view/' . $event->id));
                Yii::app()->end();
            }
        } else {
            $current_episode = null;
        }

        $no_episodes = count($episodes) < 1 && count($supportserviceepisodes) < 1 && count($legacyepisodes) < 1;

        if ($no_episodes) {
            $this->layout = '//layouts/events_and_episodes_no_header';
        }

        $this->current_episode = $current_episode;
        $this->title = 'Episode summary';

        $this->render('episodes', array(
        'title' => empty($episodes) ? '' : 'Episode summary',
        'episodes' => $episodes,
        'site' => $site,
        'css_class' => 'episodes-list',
        'noEpisodes' => $no_episodes,
        ));
    }

        /**
        * Returns the data model based on the primary key given in the GET variable.
        * If the data model is not found, an HTTP exception will be raised.
        *
        * @param int $id the ID of the model to be loaded
        * @param bool $allow_deleted
        * @return Patient|null
        * @throws CHttpException
        */
    public function loadModel($id, $allow_deleted = true)
    {
        $model = Patient::model()->findByPk((int)$id);
        // cannot find any patient by id, throw exception
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        // if the deleted patient is not allowed and the found patient is deleted
        if (!$allow_deleted && $model->deleted) {
            // try to find if the deleted patient is merged to some other patient
            if ($merged = $model->isMergedInto()) {
                // assign the primary patient for return
                $model = $merged->primaryPatient;
                // set a flash to inform user patient x was merged into this patient
                Yii::app()->user->setFlash('warning.patient-merged', $merged->getMergedMessage());
            } elseif (!\Yii::app()->user->checkAccess('OprnDeletePatient')) {
                // Users with patient deletion access can see patient summary
                // throw exception
                throw new CHttpException(404, 'The requested page does not exist.');
            }
        }

        return $model;
    }

        /**
        * Redirect the request if the the patient was merged into a primary patient
        */
    public function redirectIfMerged($redirect_link = null)
    {
        if ($this->patient && ($merged = $this->patient->isMergedInto())) {
            $primary_patient = $this->loadModel($merged->primary_id);

            //display the flash message
            Yii::app()->user->setFlash('warning.no-results', $merged->getMergedMessage());

            $this->redirect(($redirect_link ? $redirect_link : (new CoreAPI())->generatePatientLandingPageLink($this->patient)));
        }
    }

    public function actionEpisode($id)
    {
        if (!$this->episode = Episode::model()->findByPk($id)) {
            throw new SystemException('Episode not found: ' . $id);
        }

        $this->layout = '//layouts/events_and_episodes';
        $this->patient = $this->episode->patient;
        $this->pageTitle = $this->episode->getSubspecialtyText();

        //if $this->patient was merged we redirect the user to the primary patient's page
        $this->redirectIfMerged();

        $episodes = $this->patient->episodes;

        $site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);

        $this->title = 'Episode summary';
        $this->event_tabs = array(
        array(
            'label' => 'View',
            'active' => true,
        ),
        );

        if ($this->checkAccess('OprnEditEpisode', $this->episode) && $this->episode->firm) {
            $this->event_tabs[] = array(
                'label' => 'Edit',
                'href' => Yii::app()->createUrl('/patient/updateepisode/' . $this->episode->id),
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
        'noEpisodes' => false,
        ));
    }

    public function actionUpdateepisode($id)
    {
        if (!$this->episode = Episode::model()->findByPk($id)) {
            throw new SystemException('Episode not found: ' . $id);
        }

        if (!$this->checkAccess('OprnEditEpisode', $this->episode) || isset($_POST['episode_cancel'])) {
            $this->redirect(array('patient/episode/' . $this->episode->id));

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
                        $diagnosisDate = isset($_POST['DiagnosisSelection']['date']) ? $_POST['DiagnosisSelection']['date'] : false;
                        $diagnosisTime = isset($_POST['DiagnosisSelection']['time']) ? $_POST['DiagnosisSelection']['time'] : false;
                        $this->episode->setPrincipalDiagnosis($_POST['DiagnosisSelection']['disorder_id'], $_POST['eye_id'], $diagnosisDate, $diagnosisTime);
                    }
                }

                if ($_POST['episode_status_id'] != $this->episode->episode_status_id) {
                    $this->episode->episode_status_id = $_POST['episode_status_id'];

                    if (!$this->episode->save()) {
                        throw new Exception('Unable to update status for episode ' . $this->episode->id . ' ' . print_r($this->episode->getErrors(), true));
                    }
                }

                $this->redirect(array('patient/episode/' . $this->episode->id));
            }
        }

        $this->patient = $this->episode->patient;
        $this->layout = '//layouts/events_and_episodes';
        $this->pageTitle = $this->episode->getSubspecialtyText();

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
            'href' => Yii::app()->createUrl('/patient/summary/' . $this->episode->id),
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
        'noEpisodes' => false,
        ));
    }

    public function actionOEscape($subspecialty_id, $patient_id)
    {

        $subspecialty = Subspecialty::model()->findByPk($subspecialty_id);
        $patient = Patient::model()->findByPk($patient_id);

        $this->patient = $patient;
        $this->fixedHotlist = false;
        $this->layout = '//layouts/events_and_episodes';
        $this->pageTitle = 'OEScape: ' . $subspecialty->name;

        //if $this->patient was merged we redirect the user to the primary patient's page
        $this->redirectIfMerged();

        $site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);

        $this->event_tabs = [
        [
            'label' => 'View',
            'active' => true,
        ],
        ];

        $header_data = [];
        if ($subspecialty->ref_spec == 'GL') {
            $exam_api = \Yii::app()->moduleAPI->get('OphCiExamination');
            $cct_element = $exam_api->getLatestElement(
                'OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment_CCT',
                $this->patient,
                false //use context
            );

            $criteria = new \CDbCriteria();
            $criteria->with = ['event.episode'];
            $criteria->addCondition('episode.patient_id = :patient_id');
            $criteria->params[':patient_id'] = $this->patient->id;
            $criteria->order = "event.event_date ASC";

            if ($cct_element) {
                if ($cct_element->hasLeft()) {
                    $header_data['CCT']['left'] = $cct_element->left_value;
                }
                if ($cct_element->hasRight()) {
                    $header_data['CCT']['right'] = $cct_element->right_value;
                }
                $header_data['CCT']['date'] = \Helper::convertMySQL2NHS($cct_element->event->event_date);
            }
            $iop = $exam_api->getBaseIOPValues($patient);

            if ($iop) {
                $header_data['IOP']['right'] = $iop['right'];
                $header_data['IOP']['left'] = $iop['left'];
                $header_data['IOP']['date'] = $iop['date'];
            }

            $max_iop = $exam_api->getMaxIOPValues($patient);
            if ($max_iop) {
                $header_data['IOP_MAX']['right'] = $max_iop['right'];
                $header_data['IOP_MAX']['left'] = $max_iop['left'];
            }
        }

        $this->render('/oescape/oescapes', array(
        'title' => '',
        'subspecialty' => $subspecialty,
        'site' => $site,
        'noEpisodes' => false,
        'header_data' => $header_data
        ));
    }

    public function actionLightningViewer($id, $preview_type = null)
    {
        $this->patient = Patient::model()->findByPk($id);
        if (!$this->patient) {
            throw new SystemException('Patient not found: ' . $id);
        }

        $this->pageTitle = 'Lightning Viewer';
        $this->layout = '//layouts/events_and_episodes';
        $this->title = 'Lightning Viewer';

        /* @var array(string => Event[]) $eventTypeMap */
        $eventTypeMap = array();

        // Letters is the fallback if no events exist, so its key is initialised to an empty array
        $previewGroups = ['Letters' => []];

        // Find all events for this patient
        /* @var EventType $eventType */
        foreach (EventType::model()->findAll() as $eventType) {
            $eventTypeMap[$eventType->name] = array();
            $api = $eventType->getApi();
            if ($api) {
                $eventTypeMap[$eventType->name] += $eventType->getApi()->getVisibleEvents($this->patient);
            }
        }

        // For every document sub type...
        /* @var OphCoDocument_Sub_Types $documentTyoe */
        foreach (OphCoDocument_Sub_Types::model()->findAll() as $documentType) {
            // Find the document events for that subtype ...
            $documentEvents = array_filter($eventTypeMap['Document'], function ($documentEvent) use ($documentType) {
                $documentElement = $documentEvent->getElementByClass(Element_OphCoDocument_Document::class);
                return $documentElement->sub_type->id === $documentType->id;
            });

            // And add them to the preview groups
            // Referral letters should be put in the Letter bucket, along with correspondence events
            if ($documentType->name === 'Referral Letter') {
                $previewGroups['Letters'] += $documentEvents;
            } else {
                $previewGroups[$documentType->name] = $documentEvents;
            }
        }

        foreach ($eventTypeMap as $eventType => $events) {
            switch ($eventType) {
                // Document events should be ignored, as they have already been broken down by document sub type
                case 'Document':
                    continue 2;
                // Biometry events and report documents should be in the same bucket
                case 'Biometry':
                    $groupType = 'BiometryReport';
                    break;
                // Correspondence events should go in th 'Letters' bucket
                case 'Correspondence':
                    $groupType = 'Letters';
                    break;
                default:
                    $groupType = $eventType;
                    break;
            }

            if (!array_key_exists($groupType, $previewGroups)) {
                $previewGroups[$groupType] = [];
            }
            $previewGroups[$groupType] = array_merge($previewGroups[$groupType], $events);
        }

        // Default to letters if no other preview type exists
        if (!$preview_type || !isset($previewGroups[$preview_type])) {
            $preview_type = 'Letters';
            // but if there aren't any letters, then default to the first non-empty group
            if (count($previewGroups['Letters']) === 0) {
                foreach ($previewGroups as $key => $group) {
                    if (count($group) > 0) {
                        $preview_type = $key;
                        break;
                    }
                }
            }
        }
        $selectedPreviews = $previewGroups[$preview_type];

        $previewsByYear = array();

        if (count($selectedPreviews) > 0) {
            // Sort the documents and split them into different years
            usort($selectedPreviews, function ($a, $b) {
                return $a->event_date > $b->event_date ? -1 : 1;
            });

            foreach ($selectedPreviews as $event) {
                $year = (new DateTime($event->event_date))->format('Y');
                if (!isset($previewsByYear[$year])) {
                    $previewsByYear[$year] = array();
                }
                $previewsByYear[$year][] = $event;
            }
        }

        $this->render('lightning_viewer', array(
        'selectedPreviewType' => $preview_type,
        'previewGroups' => $previewGroups,
        'previewsByYear' => $previewsByYear,
        ));
    }

    public function setPageTitle($pageTitle)
    {
        if ($this->patient && (string)SettingMetadata::model()->getSetting('use_short_page_titles') != "on") {
            parent::setPageTitle($pageTitle . ' - ' . $this->patient->last_name . ', ' . $this->patient->first_name);
        } else {
            parent::setPageTitle($pageTitle);
        }
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
        $template = 'eventTypeTemplates' . DIRECTORY_SEPARATOR . $action . DIRECTORY_SEPARATOR . $eventTypeId;

        if (!file_exists(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'clinical' . DIRECTORY_SEPARATOR . $template . '.php')) {
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
        $term = strtolower(trim($_GET['term'])) . '%';

        switch (strtolower(@$_GET['filter'])) {
            case 'staff':
                $contacts = User::model()->findAsContacts($term);
                break;
            case 'nonspecialty':
                if (!$specialty = Specialty::model()->find('code=?', array(Yii::app()->params['institution_specialty']))) {
                    throw new Exception('Unable to find specialty: ' . Yii::app()->params['institution_specialty']);
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
            throw new Exception('Patient not found: ' . @$_GET['patient_id']);
        }

        if (@$_GET['contact_location_id']) {
            if (!$location = ContactLocation::model()->findByPk(@$_GET['contact_location_id'])) {
                throw new Exception("Can't find contact location: " . @$_GET['contact_location_id']);
            }
            $contact = $location->contact;
        } else {
            if (!$contact = Contact::model()->findByPk(@$_GET['contact_id'])) {
                throw new Exception("Can't find contact: " . @$_GET['contact_id']);
            }
        }

        // Don't assign the patient's own GP
        if ($contact->label == \SettingMetadata::model()->getSetting('general_practitioner_label')) {
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
                    throw new Exception('Unable to save patient contact assignment: ' . print_r($pca->getErrors(), true));
                }
            }
        } else {
            if (!$pca = PatientContactAssignment::model()->find('patient_id=? and contact_id=?', array($patient->id, $contact->id))) {
                $pca = new PatientContactAssignment();
                $pca->patient_id = $patient->id;
                $pca->contact_id = $contact->id;

                if (!$pca->save()) {
                    throw new Exception('Unable to save patient contact assignment: ' . print_r($pca->getErrors(), true));
                }
            }
        }

        $this->renderPartial('_patient_contact_row', array('pca' => $pca));
    }

    public function actionUnassociatecontact()
    {
        if (!$pca = PatientContactAssignment::model()->findByPk(@$_GET['pca_id'])) {
            throw new Exception('Patient contact assignment not found: ' . @$_GET['pca_id']);
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

        $this->redirect(array('patient/view/' . $patient->id));
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
        * Generate the select to the frontend for the allergy selection.
        */
    public function actionGenerateAllergySelect()
    {
        $this->patient = $this->loadModel(Yii::app()->getRequest()->getQuery('patient_id'));
        echo CHtml::dropDownList(
            'allergy_id',
            null,
            CHtml::listData($this->allergyList(), 'id', 'name'),
            array('empty' => '-- Select --')
        );
    }

        /**
        * List of allergies - changed to a wrap function to be able to use a common function from the model.
        */
    public function allergyList()
    {
        return PatientAllergyAssignment::model()->allergyList($this->patient->id);
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

        $this->redirect(array('patient/view/' . $patient->id));
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

    public function actionAdddiagnosis()
    {
        if (isset($_POST['DiagnosisSelection']['ophthalmic_disorder_id'])) {
            $disorder = Disorder::model()->findByPk(@$_POST['DiagnosisSelection']['ophthalmic_disorder_id']);
        } else {
            $disorder = Disorder::model()->findByPk(@$_POST['DiagnosisSelection']['systemic_disorder_id']);
        }

        if (!$disorder) {
            throw new Exception('Unable to find disorder: ' . @$_POST['DiagnosisSelection']['ophthalmic_disorder_id'] . ' / ' . @$_POST['DiagnosisSelection']['systemic_disorder_id']);
        }

        if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
            throw new Exception('Unable to find patient: ' . @$_POST['patient_id']);
        }

        $date = $this->processFuzzyDate();

        if (!$_POST['diagnosis_eye']) {
            if (!SecondaryDiagnosis::model()->find('patient_id=? and disorder_id=? and date=?', array($patient->id, $disorder->id, $date))) {
                $patient->addDiagnosis($disorder->id, null, $date);
            }
        } elseif (!SecondaryDiagnosis::model()->find('patient_id=? and disorder_id=? and eye_id=? and date=?', array($patient->id, $disorder->id, $_POST['diagnosis_eye'], $date))) {
            $patient->addDiagnosis($disorder->id, $_POST['diagnosis_eye'], $date);
        }

        $this->redirect(array('patient/view/' . $patient->id));
    }

    private function processFuzzyDate()
    {
        return Helper::padFuzzyDate(@$_POST['fuzzy_year'], @$_POST['fuzzy_month'], @$_POST['fuzzy_day']);
    }

    public function actionValidateAddDiagnosis()
    {
        $errors = array();

        if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
            throw new Exception('Patient not found: ' . @$_POST['patient_id']);
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

        $this->renderJSON($errors);
    }

    public function actionRemovediagnosis()
    {
        if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
            throw new Exception('Unable to find patient: ' . @$_GET['patient_id']);
        }

        $patient->removeDiagnosis(@$_GET['diagnosis_id']);

        echo 'success';
    }

    public function actionEditOphInfo()
    {
        $cvi_status = PatientOphInfoCviStatus::model()->findByPk(@$_POST['PatientOphInfo']['cvi_status_id']);

        if (!$cvi_status) {
            throw new Exception('invalid cvi status selection:' . @$_POST['PatientOphInfo']['cvi_status_id']);
        }

        if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
            throw new Exception('Unable to find patient: ' . @$_POST['patient_id']);
        }

        $cvi_status_date = $this->processFuzzyDate();

        $result = $patient->editOphInfo($cvi_status, $cvi_status_date);

        $this->renderJSON($result);
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
                        'diagnosis' => $row['episode' . $m[1] . '_disorder'],
                    );
                }
                if (preg_match('/^sd([0-9]+)_eye$/', $key, $m)) {
                    $results['patients'][$date['timestamp']]['diagnoses'][] = array(
                        'eye' => $value,
                        'diagnosis' => $row['sd' . $m[1] . '_disorder'],
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
                'timestamp' => strtotime(substr($dates[0], 0, 4) . '-01-01'),
            );
        } elseif (preg_match('/-00$/', $dates[0])) {
            $date = Helper::getMonthText(substr($dates[0], 5, 2)) . ' ' . substr($dates[0], 0, 4);

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
            throw new Exception('Patient not found:' . @$_POST['patient_id']);
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
            $this->renderJSON($po->getErrors());

            return;
        }

        $this->renderJSON(array());
    }

    public function actionEditSocialHistory()
    {
        if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
            throw new Exception('Patient not found:' . @$_POST['patient_id']);
        }
        if (!$social_history = SocialHistory::model()->find('patient_id=?', array($patient->id))) {
            $social_history = new SocialHistory();
        }
        $social_history->patient_id = $patient->id;
        $social_history->attributes = $_POST['SocialHistory'];
        if (!$social_history->save()) {
            throw new Exception('Unable to save social history: ' . print_r($social_history->getErrors(), true));
        } else {
            $this->redirect(array('patient/view/' . $patient->id));
        }
    }

    public function actionAddFamilyHistory()
    {
        if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
            throw new Exception('Patient not found:' . @$_POST['patient_id']);
        }

        if (@$_POST['no_family_history']) {
            $patient->setNoFamilyHistory();
        } else {
            if (!$relative = FamilyHistoryRelative::model()->findByPk(@$_POST['relative_id'])) {
                throw new Exception('Unknown relative: ' . @$_POST['relative_id']);
            }

            if (!$side = FamilyHistorySide::model()->findByPk(@$_POST['side_id'])) {
                throw new Exception('Unknown side: ' . @$_POST['side_id']);
            }

            if (!$condition = FamilyHistoryCondition::model()->findByPk(@$_POST['condition_id'])) {
                throw new Exception('Unknown condition: ' . @$_POST['condition_id']);
            }

            if (@$_POST['edit_family_history_id']) {
                if (!$fh = FamilyHistory::model()->findByPk(@$_POST['edit_family_history_id'])) {
                    throw new Exception('Family history not found: ' . @$_POST['edit_family_history_id']);
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
                    throw new Exception('Unable to save family history: ' . print_r($fh->getErrors(), true));
                }
            } else {
                $patient->addFamilyHistory($relative->id, @$_POST['other_relative'], $side->id, $condition->id, @$_POST['other_condition'], @$_POST['comments']);
            }
        }

        $this->redirect(array('patient/view/' . $patient->id));
    }

    public function actionRemovePreviousOperation()
    {
        if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
            throw new Exception('Patient not found: ' . @$_GET['patient_id']);
        }

        if (!$po = PreviousOperation::model()->find('patient_id=? and id=?', array($patient->id, @$_GET['operation_id']))) {
            throw new Exception('Previous operation not found: ' . @$_GET['operation_id']);
        }

        if (!$po->delete()) {
            throw new Exception('Failed to remove previous operation: ' . print_r($po->getErrors(), true));
        }

        echo 'success';
    }

    public function actionGetPreviousOperation()
    {
        if (!$po = PreviousOperation::model()->findByPk(@$_GET['operation_id'])) {
            throw new Exception('Previous operation not found: ' . @$_GET['operation_id']);
        }

        $date = explode('-', $po->date);

        $this->renderJSON(array(
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
            throw new Exception('Patient not found: ' . @$_GET['patient_id']);
        }

        if (!$m = FamilyHistory::model()->find('patient_id=? and id=?', array($patient->id, @$_GET['family_history_id']))) {
            throw new Exception('Family history not found: ' . @$_GET['family_history_id']);
        }

        if (!$m->delete()) {
            throw new Exception('Failed to remove family history: ' . print_r($m->getErrors(), true));
        }

        echo 'success';
    }

    public function processJsVars()
    {
        if ($this->patient) {
            $patient_identifier = PatientIdentifier::model()->find(
                'patient_id=:patient_id AND patient_identifier_type_id=:patient_identifier_type_id',
                [':patient_id' => $this->patient->id,
                    ':patient_identifier_type_id' => Yii::app()->params['oelauncher_patient_identifier_type']]
            );
            $this->jsVars['OE_patient_id'] = $this->patient->id;
            $this->jsVars['OE_patient_hosnum'] = $patient_identifier->value ?? null;
        }
        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
        $subspecialty_id = $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;

        $this->jsVars['OE_subspecialty_id'] = $subspecialty_id;

        parent::processJsVars();
    }

    public function actionInstitutionSites()
    {
        if (!$institution = Institution::model()->findByPk(@$_GET['institution_id'])) {
            throw new Exception('Institution not found: ' . @$_GET['institution_id']);
        }

        $this->renderJSON(CHtml::listData($institution->sites, 'id', 'name'));
    }

    public function actionValidateSaveContact()
    {
        if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
            throw new Exception('Patient not found: ' . @$_POST['patient_id']);
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

        if (@$_POST['contact_label_id'] === 'nonspecialty' && !@$_POST['label_id']) {
            $errors['label_id'] = 'Please select a label';
        }

        $contact = Contact::model();

        foreach (array('title', 'first_name', 'last_name') as $field) {
            if (!@$_POST[$field]) {
                $errors[$field] = $contact->getAttributeLabel($field) . ' is required';
            }
        }

        $this->renderJSON($errors);
    }

        /**
        * @throws Exception
        */
    public function actionAddContact()
    {
        if (@$_POST['site_id']) {
            if (!$site = Site::model()->findByPk($_POST['site_id'])) {
                throw new Exception('Site not found: ' . $_POST['site_id']);
            }
        } else {
            if (!$institution = Institution::model()->findByPk(@$_POST['institution_id'])) {
                throw new Exception('Institution not found: ' . @$_POST['institution_id']);
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
                    throw new Exception('Unable to save patient contact assignment: ' . print_r($pca->getErrors(), true));
                }

                $this->redirect(array('/patient/view/' . $patient->id));
            }
        }

        $contact = new Contact();
        $contact->attributes = $_POST;
        $contact->created_institution_id = Yii::app()->session['selected_institution_id'];

        if (@$_POST['contact_label_id'] === 'nonspecialty') {
            if (!$label = ContactLabel::model()->findByPk(@$_POST['label_id'])) {
                throw new Exception('Contact label not found: ' . @$_POST['label_id']);
            }
        } elseif (!$label = ContactLabel::model()->find('name=?', array(@$_POST['contact_label_id']))) {
            throw new Exception('Contact label not found: ' . @$_POST['contact_label_id']);
        }

        $contact->contact_label_id = $label->id;

        if (!$contact->save()) {
            throw new Exception('Unable to save contact: ' . print_r($contact->getErrors(), true));
        }

        $cl = new ContactLocation();
        $cl->contact_id = $contact->id;
        if (isset($site)) {
            $cl->site_id = $site->id;
        } else {
            $cl->institution_id = $institution->id;
        }

        if (!$cl->save()) {
            throw new Exception('Unable to save contact location: ' . print_r($cl->getErrors(), true));
        }

        $pca = new PatientContactAssignment();
        $pca->patient_id = $patient->id;
        $pca->location_id = $cl->id;

        if (!$pca->save()) {
            throw new Exception('Unable to save patient contact assignment: ' . print_r($pca->getErrors(), true));
        }

        $this->redirect(array('/patient/view/' . $patient->id));
    }

    public function actionGetContactLocation()
    {
        if (!$location = ContactLocation::model()->findByPk(@$_GET['location_id'])) {
            throw new Exception('ContactLocation not found: ' . @$_GET['location_id']);
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

        $this->renderJSON($data);
    }

    public function actionValidateEditContact()
    {
        if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
            throw new Exception('Patient not found: ' . @$_POST['patient_id']);
        }

        if (!$contact = Contact::model()->findByPk(@$_POST['contact_id'])) {
            throw new Exception('Contact not found: ' . @$_POST['contact_id']);
        }

        $errors = array();

        if (!@$_POST['institution_id']) {
            $errors['institution_id'] = 'Please select an institution';
        } else {
            if (!$institution = Institution::model()->findByPk(@$_POST['institution_id'])) {
                throw new Exception('Institution not found: ' . @$_POST['institution_id']);
            }
        }

        if (@$_POST['site_id']) {
            if (!$site = Site::model()->findByPk(@$_POST['site_id'])) {
                throw new Exception('Site not found: ' . @$_POST['site_id']);
            }
        }

        $this->renderJSON($errors);
    }

    public function actionEditContact()
    {
        if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
            throw new Exception('Patient not found: ' . @$_POST['patient_id']);
        }

        if (!$contact = Contact::model()->findByPk(@$_POST['contact_id'])) {
            throw new Exception('Contact not found: ' . @$_POST['contact_id']);
        }

        if (@$_POST['site_id']) {
            if (!$site = Site::model()->findByPk(@$_POST['site_id'])) {
                throw new Exception('Site not found: ' . @$_POST['site_id']);
            }
            if (!$cl = ContactLocation::model()->find('contact_id=? and site_id=?', array($contact->id, $site->id))) {
                $cl = new ContactLocation();
                $cl->contact_id = $contact->id;
                $cl->site_id = $site->id;

                if (!$cl->save()) {
                    throw new Exception('Unable to save contact location: ' . print_r($cl->getErrors(), true));
                }
            }
        } else {
            if (!$institution = Institution::model()->findByPk(@$_POST['institution_id'])) {
                throw new Exception('Institution not found: ' . @$_POST['institution_id']);
            }

            if (!$cl = ContactLocation::model()->find('contact_id=? and institution_id=?', array($contact->id, $institution->id))) {
                $cl = new ContactLocation();
                $cl->contact_id = $contact->id;
                $cl->institution_id = $institution->id;

                if (!$cl->save()) {
                    throw new Exception('Unable to save contact location: ' . print_r($cl->getErrors(), true));
                }
            }
        }

        if (!$pca = PatientContactAssignment::model()->findByPk(@$_POST['pca_id'])) {
            throw new Exception('PCA not found: ' . @$_POST['pca_id']);
        }

        $pca->location_id = $cl->id;

        if (!$pca->save()) {
            throw new Exception('Unable to save patient contact assignment: ' . print_r($pca->getErrors(), true));
        }

        $this->redirect(array('/patient/view/' . $patient->id));
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
            throw new Exception('Patient not found: ' . @$_GET['patient_id']);
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
            throw new Exception('Patient not found: ' . @$_POST['patient_id']);
        }

        if (!empty($_POST['firm_id'])) {
            $firm = Firm::model()->findByPk($_POST['firm_id']);
            if (!$episode = $patient->getOpenEpisodeOfSubspecialty($firm->getSubspecialtyID())) {
                $episode = $patient->addEpisode($firm);
            }

            $this->redirect(array('/patient/summary/' . $episode->id));
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
        * @param Episode $episode
        * @param EventType $event_type
        *
        * @return bool
        */
    public function checkCreateAccess(Episode $episode, EventType $event_type)
    {
        $oprn = 'OprnCreate' . ($event_type->class_name == 'OphDrPrescription' ? 'Prescription' : 'Event');

        return $this->checkAccess($oprn, $this->firm, $episode, $event_type);
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

        // Executing the js function
        // to find duplicate patients on entering create patient screen each time so that the warning message
        // does not disappear after refreshing.
        Yii::app()->clientScript->registerScript('findduplicatepatients', 'findDuplicates();', CClientScript::POS_READY);

        //Don't render patient summary box on top as we have no selected patient
        $this->renderPatientPanel = false;
        $this->fixedHotlist = true;
        $this->pageTitle = 'Add New Patient';

        $patient_source = isset(Yii::app()->params['default_patient_source']) ? Yii::app()->params['default_patient_source'] : 'Referral';
        $patient = new Patient($patient_source);
        $patient->noPas();
        $contact = new Contact('manualAddPatient');
        $address = new Address($patient_source);
        $referral = null;
        $patient_user_referral = null;
        $pid_type_necessity_values = $this->getPatientIdentifierTypeNecessityValues();
        $patient_identifiers = $this->getPatientIdentifiers($patient, $pid_type_necessity_values);

        $gpcontact = new Contact();
        $practicecontact = new Contact();
        $practiceaddress = new Address();
        $practice = new Practice();

        $this->performAjaxValidation(array($patient, $contact, $address));

        if (isset($_POST['Contact'], $_POST['Address'], $_POST['Patient'])) {
            $contact->attributes = $_POST['Contact'];
            $contact->created_institution_id = Yii::app()->session['selected_institution_id'];
            $patient->attributes = $_POST['Patient'];
            $address->attributes = $_POST['Address'];

            $referral = new PatientReferral();
            if (isset($_POST['PatientReferral'])) {
                $referral->attributes = $_POST['PatientReferral'];
            }

            if (Yii::app()->params['use_contact_practice_associate_model'] === true) {
                if (isset($_POST['ExtraContact'])) {
                    $gp_ids = $_POST['ExtraContact']['gp_id'];
                    if (isset($_POST['ExtraContact']['practice_id'])) {
                        $practice_ids = $_POST['ExtraContact']['practice_id'];
                        $pca_models = array();
                        for ($i = 0; $i < sizeof($gp_ids); $i++) {
                            $pca_model = new PatientContactAssociate();
                            $pca_model->gp_id = $gp_ids[$i];
                            $pca_model->practice_id = $practice_ids[$i];
                            $pca_models[] = $pca_model;
                        }
                    } else {
                        $pca_models = array();
                        foreach ($gp_ids as $gp_id) {
                            $pca_model = new PatientContactAssociate();
                            $pca_model->gp_id = $gp_id;
                            $pca_models[] = $pca_model;
                        }
                    }
                    if (!empty($pca_models)) {
                        $patient->patientContactAssociates = $pca_models;
                    }
                }
            }


            if (isset($_POST['PatientUserReferral'])) {
                $patient_user_referral = new PatientUserReferral();
                if ($_POST['PatientUserReferral']['user_id'] != -1) {
                    $patient_user_referral->attributes = $_POST['PatientUserReferral'];
                }
            }
            switch ($patient->patient_source) {
                case Patient::PATIENT_SOURCE_OTHER:
                    $contact->setScenario('other_register');
                    $patient->setScenario('other_register');
                    $address->setScenario('other_register');
                    $referral->setScenario('other_register');
                    break;
                case Patient::PATIENT_SOURCE_REFERRAL:
                    $contact->setScenario('referral');
                    $patient->setScenario('referral');
                    $address->setScenario('referral');
                    $referral->setScenario('referral');
                    break;
                case Patient::PATIENT_SOURCE_SELF_REGISTER:
                    $contact->setScenario('self_register');
                    $patient->setScenario('self_register');
                    $address->setScenario('self_register');
                    $referral->setScenario('self_register');
                    break;
                default:
                    $contact->setScenario('manual');
                    break;
            }
            // not to be sync with PAS
            $patient->is_local = 1;


            // Don't save if the user just changed the "Patient Source"
            if ($_POST["changePatientSource"] == 0) {
                list($contact, $patient, $address, $referral, $patient_user_referral, $patient_identifiers) =
                    $this->performPatientSave($contact, $patient, $address, $referral, $patient_user_referral, $patient_identifiers, $pid_type_necessity_values, '');
            } else {
                // Return the same page to the user without saving
                // However the date of birth is usually reformatted before being displayed to the user, so we need to emulate that here.
                $patient->beforeValidate();
            }
        }
        // Only auto increment hos no. when the set_auto_increment_hospital_no is on
        /* TODO OE-10452
        if ($patient->getIsNewRecord() && Yii::app()->params['set_auto_increment_hospital_no'] == 'on') {
        $patient->hos_num = $patient->autoCompleteHosNum();
        }
        */

        $this->render('crud/create', array(
        'patient' => $patient,
        'contact' => $contact,
        'address' => $address,
        'referral' => isset($referral) ? $referral : new PatientReferral($patient_source),
        'patientuserreferral' => isset($patient_user_referral) ? $patient_user_referral : new PatientUserReferral(),
        'patient_identifiers' => $patient_identifiers,
        'pid_type_necessity_values' => $pid_type_necessity_values,
        'gpcontact' => $gpcontact,
        'practicecontact' => $practicecontact,
        'practiceaddress' => $practiceaddress,
        'practice' => $practice
        ));
    }

        /**
        * Gets the PatientIdentifierTypes and their necessity values based on current institution/site
        */

    private function getPatientIdentifierTypeNecessityValues()
    {
        // order of precedence: if there are site display order rules we take those and nothing else,
        // if there are no site level rules we take the institution level ones

        $institution_id = \Institution::model()->getCurrent()->id;
        $site_id = \Yii::app()->session['selected_site_id'];

        $criteria = new CDbCriteria();
        $criteria->condition = 't.institution_id = :institution_id AND t.site_id = :site_id';
        $criteria->order = 'display_order';
        $criteria->params[':institution_id'] = $institution_id;
        $criteria->params[':site_id'] = $site_id;

        $patient_identifier_type_display_orders = PatientIdentifierTypeDisplayOrder::model()->findAll($criteria);
        if (empty($patient_identifier_type_display_orders)) {
            unset($criteria->params[':site_id']);
            $criteria->condition = 't.institution_id = :institution_id AND t.site_id IS NULL';
            $patient_identifier_type_display_orders = PatientIdentifierTypeDisplayOrder::model()->findAll($criteria);
        }

        $pid_type_necessity_values = [];
        foreach ($patient_identifier_type_display_orders as $patient_identifier_type_display_order) {
            $pid_type_necessity_values[$patient_identifier_type_display_order->patient_identifier_type_id] = [
                'necessity' => $patient_identifier_type_display_order->necessity,
                'status_necessity' => $patient_identifier_type_display_order->status_necessity,
            ];
        }
        return $pid_type_necessity_values;
    }

        /**
        * Gets existing PatientIdentifier records and modified records from $_POST
        * (except hidden entries, based on PatientIdentifierTypeDisplayOrder->necessity)
        *
        * @param Patient $patient The patient for the identifiers
        * @param array $pid_type_necessity_values
        * @return PatientIdentifier[]
        */
    private function getPatientIdentifiers($patient, $pid_type_necessity_values)
    {
        $patient_identifiers = [];

        // fetch existing patient identifiers
        $existing_patient_identifiers = PatientIdentifier::model()->findAllByAttributes(['patient_id' => $patient->id, 'deleted' => 0]);
        foreach ($existing_patient_identifiers as $existing_patient_identifier) {
            $patient_identifiers[$existing_patient_identifier->patient_identifier_type_id] = $existing_patient_identifier;
        }

        // remove hidden patient identifiers
        $patient_identifiers = array_filter($patient_identifiers, function ($type_id) use ($pid_type_necessity_values) {
            return array_key_exists($type_id, $pid_type_necessity_values) && $pid_type_necessity_values[$type_id]['necessity'] !== 'hidden';
        }, ARRAY_FILTER_USE_KEY);

        // add not hidden patient identifiers
        foreach ($pid_type_necessity_values as $type_id => $value) {
            if ($value['necessity'] !== 'hidden' && !array_key_exists($type_id, $patient_identifiers)) {
                $patient_identifier = new PatientIdentifier();
                $patient_identifier->patient_identifier_type_id = $type_id;
                $patient_identifiers[$type_id] = $patient_identifier;
            }
        }

        // overwrite values if they are set in $_POST
        if (isset($_POST['PatientIdentifier'])) {
            foreach ($_POST['PatientIdentifier'] as $post_info) {
                if (array_key_exists('patient_identifier_type_id', $post_info) && array_key_exists($post_info['patient_identifier_type_id'], $patient_identifiers)) {
                    $patient_identifiers[$post_info['patient_identifier_type_id']]->value = @$post_info['value'];
                    if (array_key_exists('patient_identifier_status_id', $post_info)) {
                        $patient_identifiers[$post_info['patient_identifier_type_id']]->patient_identifier_status_id = $post_info['patient_identifier_status_id'];
                    }
                }
            }
        }
        return array_values($patient_identifiers);
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

        /**
        * Saving the Contact, Patient and Address object
        *
        * @param Contact $contact
        * @param Patient $patient
        * @param Address $address
        * @param PatientIdentifier[] $patient_identifiers
        * @param array $pid_type_necessity_values
        * @param PatientReferral $referral
        * @param PatientUserReferral $patient_user_referral
        * @param  $prevUrl
        * @return array on validation error returns the 3 objects otherwise redirects to the patient view page
        *
        * @throws
        */
    private function performPatientSave(
        Contact $contact,
        Patient $patient,
        Address $address,
        PatientReferral $referral,
        PatientUserReferral $patient_user_referral,
        $patient_identifiers,
        $pid_type_necessity_values,
        $prevUrl
    ) {

        $patientScenario = $patient->getScenario();
        $isNewPatient = $patient->isNewRecord ? true : false;
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $success =
                $this->patientSaveInner(
                    $contact,
                    $patient,
                    $address,
                    $referral,
                    $patient_user_referral,
                    $patient_identifiers,
                    $pid_type_necessity_values,
                );
            if ($success) {
                if (
                    isset(Yii::app()->modules["Genetics"])
                    && Yii::app()->user->checkAccess('Genetics Clinical')
                    && $isNewPatient
                ) {
                    $redirect = array('Genetics/subject/edit?patient=' . $patient->id);
                } elseif ($prevUrl !== '') {
                    $redirect = array($prevUrl);
                } else {
                    $redirect = array('/patient/summary/' . $patient->id);
                }
                $transaction->commit();
                $this->redirect($redirect);
            } else {
                //Get all the validation errors
                foreach (
                    [
                             'patient',
                             'contact',
                             'address',
                             'patient_user_referral',
                             'patient_user_referral',
                         ] as $model
                ) {
                    if (
                        isset(${$model
                        })
                    ) {
                        if (
                            is_array(${$model
                            })
                        ) {
                            foreach (${$model} as $item) {
                                $item->validate();
                            }
                        } else {
                            ${$model}->validate();
                        }
                    }
                }
                foreach ($patient_identifiers as $patient_identifier) {
                    if (empty($patient_identifier->value) && $pid_type_necessity_values[$patient_identifier->patient_identifier_type_id]['necessity'] === 'optional') {
                        continue;
                    }
                    $patient_identifier->validate();
                }
                $transaction->rollback();
            }

            // remove contact_id validation error
            $patient->clearErrors('contact_id');
            $address->clearErrors('contact_id');
        } catch (Exception $ex) {
            \Yii::log("Rolling back patient creation. " . $ex->getMessage());
            OELog::logException($ex);
            $transaction->rollback();
        }

        $patient->setScenario($patientScenario);

        return array($contact, $patient, $address, $referral, $patient_user_referral, $patient_identifiers);
    }

    private function patientSaveInner(
        Contact &$contact,
        Patient &$patient,
        Address &$address,
        PatientReferral &$referral,
        PatientUserReferral &$patient_user_referral,
        &$patient_identifiers,
        $pid_type_necessity_values
    ) {

        if (!$this->checkForReferralFiles($referral, $patient)) {
            return false;
        }
        if (!$contact->save()) {
            return false;
        }

        $patient->contact_id = $contact->id;
        $address->contact_id = $contact->id;

        if (
            !$patient->save()
            || !$address->save()
            || !$this->performIdentifierSave($patient, $patient_identifiers, $pid_type_necessity_values)
        ) {
            return false;
        }

        //Save referral documents
        if (!$this->actionPerformReferralDoc($patient, $referral)) {
            return false;
        }


        //Save referral to doctor
        if (isset($patient_user_referral) && $patient_user_referral->user_id != '') {
            if (!isset($patient_user_referral->patient_id)) {
                $patient_user_referral->patient_id = $patient->id;
            }

            if (!$patient_user_referral->save()) {
                return false;
            }
            Audit::add('Referred to', 'saved', $patient_user_referral->id);
        }

        $this->performPatientContactAssociatesSave($patient);

        $action = $patient->isNewRecord ? 'add' : 'edit';
        Audit::add(
            'Patient',
            $action . '-patient',
            "Patient manually [id: $patient->id] {$action}ed."
        );
        return true;
    }

    private function performPatientContactAssociatesSave($patient)
    {
        // Check if any contact selected for this patient.
        if (isset($_POST['ExtraContact'])) {
            // If a single contact exists for a patient,  delete all the records from the patient_contact_associate table before populating.
            $existing_pca_models = PatientContactAssociate::model()->findAllByAttributes(array('patient_id' => $patient->id));
            if (isset($existing_pca_models)) {
                foreach ($existing_pca_models as $existing_pca_model) {
                    $existing_pca_model->delete();
                }
            }

            $gp_ids = $_POST['ExtraContact']['gp_id'];
            $practice_ids = $_POST['ExtraContact']['practice_id'];
            for ($i = 0; $i < sizeof($gp_ids); $i++) {
                $existing_pca_model = PatientContactAssociate::model()->findAllByAttributes(array('patient_id' => $patient->id, 'gp_id' => $gp_ids[$i], 'practice_id' => $practice_ids[$i]));
                if (empty($existing_pca_model)) {
                    $pca_model = new PatientContactAssociate();
                    $pca_model->patient_id = $patient->id;
                    $pca_model->gp_id = $gp_ids[$i];
                    $pca_model->practice_id = $practice_ids[$i];
                    $pca_model->save();
                }
            }
        } else {
            // If not delete all the data related to this patient from the patient_contact_associate table.
            $existing_pca_models = PatientContactAssociate::model()->findAllByAttributes(array('patient_id' => $patient->id));
            if (isset($existing_pca_models)) {
                foreach ($existing_pca_models as $existing_pca_model) {
                    $existing_pca_model->delete();
                }
            }
        }
    }


    /**
     * Saves the input $patient_identifiers
     *
     * @param Patient $patient
     * @param PatientIdentifier[] $patient_identifiers
     * @param array $pid_type_necessity_values
     * @return bool
     * @throws Exception
     */
    private function performIdentifierSave($patient, $patient_identifiers, $pid_type_necessity_values)
    {
        $success = true;
        foreach ($patient_identifiers as $patient_identifier) {
            if (empty($patient_identifier->value) && $pid_type_necessity_values[$patient_identifier->patient_identifier_type_id]['necessity'] === 'optional') {
                $patient_identifier_to_delete = PatientIdentifier::model()->findByAttributes([
                    'patient_id' => $patient->id,
                    'patient_identifier_type_id' => $patient_identifier->patient_identifier_type_id,
                ]);
                if ($patient_identifier_to_delete && !$patient_identifier_to_delete->delete()) {
                    $success = false;
                }
            } else {
                $patient_identifier->patient_id = $patient->id;
                if ($pid_type_necessity_values[$patient_identifier->patient_identifier_type_id]['status_necessity'] === 'mandatory') {
                    $patient_identifier->status_is_mandatory = true;
                }
                if (!$patient_identifier->save()) {
                    $success = false;
                }
            }
        }
        return $success;
    }

    /**
     * Takes an uploaded file from $_FILES and saves it to a document event under the current context/firm
     *
     * @param Patient $patient To save the referral document to
     * @param PatientReferral $referral
     *
     * @return bool false for failure to save a file
     * @throws Exception
     */
    public function actionPerformReferralDoc($patient, $referral)
    {
        // To get allowed file types from the model
        $allowed_file_types = Yii::app()->params['OphCoDocument']['allowed_file_types'];

        $firm_id = Yii::app()->session['selected_firm_id'];
        //Get or Create an episode
        list($episode, $episode_is_new) = $this->getOrCreateEpisode($patient, $firm_id);


        $event = new Event();
        $event->episode_id = $episode->id;
        $event->firm_id = $firm_id;
        $event->event_type_id = EventType::model()->findByAttributes(array('name' => 'Document'))->id;
        $event->event_date = date('Y-m-d');
        $referral_letter_type_id = OphCoDocument_Sub_Types::model()->findByAttributes(array('name' => 'Referral Letter'))->id;

        if (!$event->save()) {
            throw new Exception('Could not save event');
        }

        $document_saved = false;
        foreach ($_FILES as $file) {
            $tmp_name = $file["tmp_name"]["uploadedFile"];


            //If no document is selected this can throw errors
            if ($tmp_name == '') {
                continue;
            }
            $p_file = ProtectedFile::createFromFile($tmp_name);
            $p_file->name = $file["name"]["uploadedFile"];

            if (!in_array($p_file->mimetype, $allowed_file_types)) {
                $message = 'Only the following file types can be uploaded: ' . (implode(', ', $allowed_file_types)) . '.';
                $referral->addError('uploadedFile', $message);
            }

            if ($p_file->save()) {
                unlink($tmp_name);
                $document = new Element_OphCoDocument_Document();
                $document->patientId = $patient->id;
                $document->event_id = $event->id;
                $document->event = $event;
                $document->single_document_id = $p_file->id;
                $document->event_sub_type = $referral_letter_type_id;
                $document->single_document = $p_file;
                if (!$document->save()) {
                    throw new Exception('Could not save Document');
                } else {
                    $document_saved = true;
                }
            } else {
                unlink($tmp_name);
            }
        }

        if (!$document_saved) {
            $patient_source = $_POST['Patient']['patient_source'];
            if ($patient_source == Patient::PATIENT_SOURCE_REFERRAL) {
                //If there is no existing referral letter document, add an error
                if ($this->checkExistingReferralLetter($patient)) {
                    $referral->addError('uploadedFile', 'Referral requires a letter file');
                }
            }

            //Removed any extraneous models if we couldn't save a document
            $event->delete();
            if ($episode_is_new) {
                $episode->delete();
            }
        }
        return !$referral->hasErrors();
    }

    /**
     * @param Patient $patient
     * @param integer $firm_id Firm under which the episode should be
     * @return array(Episode, bool) The created or found episode and whether or not is was created
     * @throws Exception If a episode could not be found or created
     */
    private function getOrCreateEpisode($patient, $firm_id)
    {
        $episode = Episode::model()->findByAttributes(array('firm_id' => $firm_id, 'patient_id' => $patient->id));
        $episode_is_new = false;
        if (!$episode) {
            $episode_is_new = true;
            $episode = new Episode();
            $episode->patient_id = $patient->id;
            $episode->firm_id = $firm_id;
            $episode->support_services = false;
            $episode->start_date = date('Y-m-d H:i:s');
            if (!$episode->save()) {
                throw new Exception('Could not get episode');
            }
        }
        return [$episode, $episode_is_new];
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id, $prevUrl)
    {
        Yii::app()->assetManager->registerScriptFile('js/patient.js');

        // Executing the js function to find duplicate patients on entering update patient screen each time to
        // retain the warning message on screen after refreshing.
        Yii::app()->clientScript->registerScript('findduplicatepatients', 'findDuplicates();', CClientScript::POS_READY);

        //Don't render patient summary box on top as we have no selected patient
        $this->renderPatientPanel = false;
        $this->fixedHotlist = true;

        $patient = $this->loadModel($id);
        $referral = isset($patient->referral) ? $patient->referral : new PatientReferral();
        $this->pageTitle = 'Update Patient' . ((string)SettingMetadata::model()->getSetting('use_short_page_titles') != "on" ?
                ' - ' . $patient->last_name . ', ' . $patient->first_name : '');
        $gpcontact = isset($patient->gp) ? $patient->gp->contact : new Contact();
        $practice = isset($patient->practice) ? $patient->practice : new Practice();
        $practicecontact = isset($patient->practice) ? $patient->practice->contact : new Contact();
        $practiceaddress = isset($practicecontact) && isset($practicecontact->address) ? $practicecontact->address : new Address();

        //only local patient can be edited
        if ($patient->is_local == 0) {
            Yii::app()->user->setFlash('warning.update-patient', 'Only local patients can be edited.');
            $this->redirect(array('view', 'id' => $patient->id));
        }

        $contact = $patient->contact ? $patient->contact : new Contact('');
        $address = $patient->contact->address ?: new Address();

        $patient_user_referral = isset($patient->patientuserreferral[0]) ? $patient->patientuserreferral[0] : new PatientUserReferral();
        $pid_type_necessity_values = $this->getPatientIdentifierTypeNecessityValues();
        $patient_identifiers = $this->getPatientIdentifiers($patient, $pid_type_necessity_values);

        //only local patient can be edited
        if ($patient->is_local == 0) {
            Yii::app()->user->setFlash('warning.update-patient', 'Only local patients can be edited.');
            $this->redirect(array('view', 'id' => $patient->id));
        }
        if (isset($_POST['Contact'], $_POST['Address'], $_POST['Patient'])) {
            $contact->attributes = $_POST['Contact'];
            $patient->attributes = $_POST['Patient'];
            $address->attributes = $_POST['Address'];

            if (isset($_POST['PatientReferral'])) {
                $referral->attributes = $_POST['PatientReferral'];
            }

            // not to be sync with PAS
            $patient->is_local = 1;

            if (isset($_POST['PatientUserReferral'])) {
                if ($_POST['PatientUserReferral']['user_id'] == -1) {
                    if (isset($patient_user_referral->user_id)) {
                        $patient_user_referral->delete();
                    }
                } elseif ($_POST['PatientUserReferral']['user_id'] != $patient_user_referral->user_id) {
                    if (isset($patient_user_referral->user_id)) {
                        $patient_user_referral->delete();
                    }
                    $patient_user_referral = new PatientUserReferral();
                    $patient_user_referral->attributes = $_POST['PatientUserReferral'];
                }
            }
        }

        switch ($patient->patient_source) {
            case Patient::PATIENT_SOURCE_OTHER:
                $contact->setScenario('other_register');
                $patient->setScenario('other_register');
                $address->setScenario('other_register');
                $referral->setScenario('other_register');
                break;
            case Patient::PATIENT_SOURCE_REFERRAL:
                $contact->setScenario('referral');
                $patient->setScenario('referral');
                $address->setScenario('referral');
                $referral->setScenario('referral');
                break;
            case Patient::PATIENT_SOURCE_SELF_REGISTER:
                $contact->setScenario('self_register');
                $patient->setScenario('self_register');
                $address->setScenario('self_register');
                $referral->setScenario('self_register');
                break;
            default:
                $contact->setScenario('manual');
                $patient->setScenario('manual');
                $address->setScenario('manual');
                $referral->setScenario('manual');
                break;
        }

        $this->performAjaxValidation(array($patient, $contact, $address));

        if (isset($_POST['Contact'], $_POST['Address'], $_POST['Patient'])) {
            if ($_POST['changePatientSource'] == 0) {
                list($contact, $patient, $address, $referral, $patient_user_referral, $patient_identifiers) =
                    $this->performPatientSave($contact, $patient, $address, $referral, $patient_user_referral, $patient_identifiers, $pid_type_necessity_values, $prevUrl);
            }
        }


        $this->render('crud/update', array(
            'patient' => $patient,
            'contact' => $contact,
            'address' => $address,
            'patient_identifiers' => $patient_identifiers,
            'pid_type_necessity_values' => $pid_type_necessity_values,
            'practicecontact' => $practicecontact,
            'practiceaddress' => $practiceaddress,
            'practice' => $practice,
            'referral' => $referral,
            'patientuserreferral' => $patient_user_referral,
            'prevUrl' => $prevUrl,
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
        if ($patient->save()) {
            $message = 'Patient "<strong>' . $patient->getFullName() . '</strong>" was deleted';
            Audit::add('patient', 'delete', $message, null);
            $message .= ' successfully';
            Yii::app()->user->setFlash('success', $message);
        }

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax'])) {
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : Yii::app()-> createURL('site/index'));
        }
    }

    public function actionGpList($term)
    {
        $criteria = new CDbCriteria();
        $criteria->addSearchCondition('first_name', '', true, 'OR');
        $criteria->addSearchCondition('LOWER(first_name)', '', true, 'OR');
        $criteria->addSearchCondition('last_name', '', true, 'OR');
        $criteria->addSearchCondition('LOWER(last_name)', '', true, 'OR');

        $criteria->addSearchCondition('concat(first_name, " ", last_name)', $term, true, 'OR');
        $criteria->addSearchCondition('LOWER(concat(first_name, " ", last_name))', strtolower($term), true, 'OR');

        $gps = Gp::model()->with('contact')->findAll($criteria);

        $output = array();

        if (Yii::app()->params['use_contact_practice_associate_model'] === true) {
            foreach ($gps as $gp) {
                $practice_contact_associates = ContactPracticeAssociate::model()->findAllByAttributes(array('gp_id' => $gp->id));
                $role = $gp->getGPROle() ? ' - ' . $gp->getGPROle() : '';
                // CERA-513 the autocomplete search result should not show the inactivated gp
                if ($gp->is_active) {
                    if (count($practice_contact_associates) == 0) {
                        $output[] = array(
                            'gpTitle' => $gp->contact->title,
                            'gpFirstName' => $gp->contact->first_name,
                            'gpLastName' => $gp->contact->last_name,
                            'gpPhoneno' => $gp->contact->primary_phone,
                            'gpRole' => CJSON::encode(array('label' => $gp->contact->label->name, 'value' => $gp->contact->label->name, 'id' => $gp->contact->label->id)),
                            'label' => $gp->correspondenceName . $role,
                            'value' => $gp->id,
                            'practiceId' => '',
                        );
                    } else {
                        foreach ($practice_contact_associates as $practice_contact_associate) {
                            if (isset($practice_contact_associate->practice)) {
                                $practice = $practice_contact_associate->practice;
                                $practiceId = $practice->id;
                                $practiceNameAddress = $practice->getPracticeNames() ? ' - ' . $practice->getPracticeNames() : '';
                                $providerNo = isset($practice_contact_associate->provider_no) ? ' (' . $practice_contact_associate->provider_no . ') ' : '';
                                $output[] = array(
                                    'gpTitle' => $gp->contact->title,
                                    'gpFirstName' => $gp->contact->first_name,
                                    'gpLastName' => $gp->contact->last_name,
                                    'gpPhoneno' => $gp->contact->primary_phone,
                                    'gpRole' => CJSON::encode(array('label' => $gp->contact->label->name, 'value' => $gp->contact->label->name, 'id' => $gp->contact->label->id)),
                                    'label' => $gp->correspondenceName . $providerNo . $role . $practiceNameAddress,
                                    'value' => $gp->id,
                                    'practiceId' => $practiceId,
                                );
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($gps as $gp) {
                $output[] = array(
                    'label' => $gp->correspondenceName,
                    'value' => $gp->id
                );
            }
        }

        $this->renderJSON($output);
        Yii::app()->end();
    }

    /**
     * This function is only called from the Gp or Referring Practitioner field on Add Patient Screen.
     * @param $term - Search term
     */
    public function actionGpListRp($term)
    {
        $criteria = new CDbCriteria();
        $criteria->addSearchCondition('first_name', '', true, 'OR');
        $criteria->addSearchCondition('LOWER(first_name)', '', true, 'OR');
        $criteria->addSearchCondition('last_name', '', true, 'OR');
        $criteria->addSearchCondition('LOWER(last_name)', '', true, 'OR');

        $criteria->addSearchCondition('concat(first_name, " ", last_name)', $term, true, 'OR');
        $criteria->addSearchCondition('LOWER(concat(first_name, " ", last_name))', strtolower($term), true, 'OR');

        $gps = Gp::model()->with('contact')->findAll($criteria);

        $output = array();

        if (Yii::app()->params['use_contact_practice_associate_model'] === true) {
            foreach ($gps as $gp) {
                $practice_contact_associates = ContactPracticeAssociate::model()->findAllByAttributes(array('gp_id' => $gp->id));
                $role = $gp->getGPROle() ? ' - ' . $gp->getGPROle() : '';
                // CERA-513 the autocomplete search result should not show the inactivated gp
                if ($gp->is_active && count($practice_contact_associates) > 0) {
                    foreach ($practice_contact_associates as $practice_contact_associate) {
                        if (isset($practice_contact_associate->practice)) {
                            $practice = $practice_contact_associate->practice;
                            $practiceId = $practice->id;
                            $practiceNameAddress = $practice->getPracticeNames() ? ' - ' . $practice->getPracticeNames() : '';
                            $providerNo = isset($practice_contact_associate->provider_no) ? ' (' . $practice_contact_associate->provider_no . ') ' : '';
                            $output[] = array(
                                'gpTitle' => $gp->contact->title,
                                'gpFirstName' => $gp->contact->first_name,
                                'gpLastName' => $gp->contact->last_name,
                                'gpPhoneno' => $gp->contact->primary_phone,
                                'gpRole' => CJSON::encode(array('label' => $gp->contact->label->name, 'value' => $gp->contact->label->name, 'id' => $gp->contact->label->id)),
                                'label' => $gp->correspondenceName . $providerNo . $role . $practiceNameAddress,
                                'value' => $gp->id,
                                'practiceId' => $practiceId,
                            );
                        }
                    }
                }
            }
        } else {
            foreach ($gps as $gp) {
                $output[] = array(
                    'label' => $gp->correspondenceName,
                    'value' => $gp->id
                );
            }
        }

        $this->renderJSON($output);
        Yii::app()->end();
    }

    public function actionPracticeList($term)
    {
        $term = strtolower($term);

        $criteria = new CDbCriteria();
        $criteria->join = 'JOIN contact on t.contact_id = contact.id';
        $criteria->join .= '  JOIN address on contact.id = address.contact_id';
        $criteria->addCondition('( (date_end is NULL OR date_end > NOW()) AND (date_start is NULL OR date_start < NOW()))');

        $criteria->addSearchCondition('LOWER(CONCAT_WS(", ", first_name ,address1, address2, city, county, postcode))', $term);

        $practices = Practice::model()->findAll($criteria);

        $output = array();
        foreach ($practices as $practice) {
            $output[] = array(
                'label' => $practice->getPracticeNames(),
                'value' => $practice->id
            );
        }

        $this->renderJSON($output);

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

    public function actionFindDuplicates($firstName, $last_name, $dob, $id = null)
    {
        $patients = Patient::findDuplicates($firstName, $last_name, $dob, $id);

        if (isset($patients['error'])) {
            $this->renderPartial('crud/_conflicts_error', array(
                'errors' => $patients['error'],
            ));
        } else {
            if (count($patients) !== 0) {
                $this->renderPartial('crud/_conflicts', array(
                    'patients' => $patients,
                    'name' => $firstName . ' ' . $last_name
                ));
            } else {
                $this->renderPartial('crud/_conflicts', array(
                    'name' => $firstName . ' ' . $last_name
                ));
            }
        }
    }

    public function actionFindDuplicatesByIdentifier($identifier_type_id, $identifier_value, $id = null)
    {

        $patients = Patient::findDuplicatesByIdentifier($identifier_type_id, $identifier_value, $id);

        if (isset($patients['error'])) {
            $this->renderPartial('crud/_conflicts_error', array(
                'errors' => $patients['error'],
            ));
        } else {
            if (count($patients) !== 0) {
                $this->renderPartial('crud/_conflicts_identifier', array(
                    'patients' => $patients,
                    'identifier_type_id' => $identifier_type_id,
                ));
            }
        }
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
                array(
                    'subspecialty' => $element->event->episode->getSubspecialtyText(),
                    'event_date' => $element->event->NHSDate('event_date')
                ),
                $element->getDisplayAttributes()
            );
        }

        $this->renderJSON($result);
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

    public function checkForReferralFiles($referral, $patient)
    {

        // To get allowed file types from the model
        $allowed_file_types = Yii::app()->params['OphCoDocument']['allowed_file_types'];

        // To get maximum file size that can be uploaded from the model
        $max_document_size = Helper::return_bytes(ini_get('upload_max_filesize'));

        foreach ($_FILES as $file) {
            $name = $file["name"]["uploadedFile"];
            $size = $file["size"]["uploadedFile"];
            $type = $file["type"]["uploadedFile"];


            //Check only if document has been added
            if ($name != '') {
                // PHP automatically discards the files that exceed the maximum file upload limit.
                // So when the size parameter is 0 and the name is not null, it means the file size is large
                if ($size == 0) {
                    $message = "The file you tried to upload exceeds the maximum allowed file size, which is " . $max_document_size / 1048576 . " MB ";
                    $referral->addError('uploadedFile', $message);
                    return false;
                } // Check for compatible file types
                elseif (!in_array($type, $allowed_file_types)) {
                    $message = 'Only the following file types can be uploaded: ' . (implode(', ', $allowed_file_types)) . '.';
                    $referral->addError('uploadedFile', $message);
                    return false;
                }
            } // The file field is empty. It should throw error for referral scenario
            elseif ($patient->getScenario() == 'referral' && $this->checkExistingReferralLetter($patient)) {
                $referral->addError('uploadedFile', 'Referral requires a letter file');
                return false;
            }
        }
        return true;
    }


    /**
     * Check for new imported biometry event.
     */
    private function checkImportedBiometryEvent()
    {
        // we need to be sure that Biometry module is installed
        if (isset(Yii::app()->modules['OphInBiometry'])) {
            $criteria = new CDbCriteria();
            $criteria->addCondition("is_linked=0 AND patient_id='" . $this->patient->id . "'");
            $resultSet = OphInBiometry_Imported_Events::model()->findAll($criteria);
            if ($resultSet) {
                Yii::app()->user->setFlash(
                    'alert.unlinked_biometry_event',
                    'A new biometry report is available for this patient - please create a biometry event to view it '
                );
            }
        }
    }

    /**
     * @param $patient
     * @return bool any existing referral letter for this patient will return false
     */
    protected function checkExistingReferralLetter($patient)
    {
        if (!isset($patient->id)) {
            return true;
        }
        $command = Yii::app()->db->createCommand()->setText("
                    select count(*) 'referral letters'
                    from patient p
                    join episode e on p.id = e.patient_id
                    join event e2 on e.id = e2.episode_id
                    join et_ophcodocument_document d on d.event_id = e2.id
                      and d.event_sub_type in (select id from ophcodocument_sub_types where name = 'Referral Letter')
                    where e2.deleted = 0 and p.id = $patient->id;");
        return ($command->queryScalar() === 0);
    }


    public function actionGetPastWorklistPatients($patient_id)
    {
        $criteria = new \CDbCriteria();
        $criteria->join = " JOIN worklist w ON w.id = t.worklist_id";

        $start_of_today = date("Y-m-d");

        $criteria->addCondition('t.when < "' . $start_of_today . '"');
        $criteria->order = 't.when desc';

        $past_worklist_patients = WorklistPatient::model()->findAllByAttributes(
            ['patient_id' => $patient_id],
            $criteria
        );

        $this->renderJSON(array(
            'past_worklist_tbody' => $this->renderPartial('/default/appointment_entry_tbody', array('worklist_patients' => $past_worklist_patients), true),
        ));
    }

    public function actionShowCurrentPathway()
    {
        $pathway = Pathway::model()->findByPk($_POST['pathway_id']);
        $this->renderJSON($this->renderPartial('//patient/_patient_clinic_pathway', [
            'pathway' => $pathway,
            'display_wait_duration' => true,
            'editable' => true,
        ], true));
    }

    /**
     * Get CITO url
     * @return string
     * @throws Exception
     */
    public function actionGetCitoUrl($hos_num)
    {
        $citoIntegration = \Yii::app()->citoIntegration;

        try {
            $username = \Yii::app()->user->name;
            $cito_url = $citoIntegration->generateCitoUrl($hos_num, $username);
            $this->renderJSON(array('success' => true, 'url' => $cito_url));
        } catch (Exception $e) {
            $message = $e->getMessage();
            \OELog::log($message);
            $this->renderJSON(array('success' => false, 'message' => 'Something went wrong trying to contact CITO. If this issue persists, please contact support.'));
        }
    }
}
