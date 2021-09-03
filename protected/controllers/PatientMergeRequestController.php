<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class PatientMergeRequestController extends BaseController
{
    public $firm;

    /**
     * @var string the default layout for the views
     */
    public $layout = '//layouts/main';

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index', 'create', 'view', 'log', 'search', 'merge', 'update', 'delete'),
                'roles' => array('Patient Merge'),
            ),

            array('allow',
                'actions' => array('index', 'create', 'view', 'log', 'search', 'update', 'delete'),
                'roles' => array('Patient Merge Request'),
            ),

        );
    }

    public function init()
    {
        Yii::app()->assetManager->registerScriptFile('js/patient_merge.js');
    }

    public function beforeAction($action)
    {
        parent::storeData();
        $this->firm = Firm::model()->findByPk($this->selectedFirmId);

        return parent::beforeAction($action);
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $filters = Yii::app()->request->getParam('PatientMergeRequestFilter');
        $filters['secondary_hos_num_filter'] = isset($filters['secondary_hos_num_filter']) ? $filters['secondary_hos_num_filter'] : null;
        $filters['primary_hos_num_filter'] = isset($filters['primary_hos_num_filter']) ? $filters['primary_hos_num_filter'] : null;

        $cookie_key = 'show_merged_'.Yii::app()->user->id;

        if ((isset($filters['show_merged']) && $filters['show_merged'] == 1)) {
            // turn ON the show_merge filter
            $cookie_value = 1;
        } elseif (isset($filters['show_merged']) && $filters['show_merged'] == 0) {
            // turn OFF the show_merge filter
            $cookie_value = 0;
        } elseif (Yii::app()->request->cookies->contains($cookie_key)) {
            // get back the cookie value if it is set
            $cookie_value = Yii::app()->request->cookies[$cookie_key]->value;
        } else {
            // neither 'show_merged' in the get/post nor in the cookies
            $cookie_value = 0;
        }

        Yii::app()->request->cookies[$cookie_key] = new CHttpCookie($cookie_key, $cookie_value);
        $filters['show_merged'] = $cookie_value;

        $criteria = new CDbCriteria();
        $criteria->compare('deleted', 0);
        $criteria->addCondition('status != :status');
        $criteria->addSearchCondition('secondary_hos_num', $filters['secondary_hos_num_filter']);
        $criteria->addSearchCondition('primary_hos_num', $filters['primary_hos_num_filter']);

        if (!$cookie_value) {
            $criteria->params[':status'] = PatientMergeRequest::STATUS_MERGED;
        } else {
            $criteria->params[':status'] = PatientMergeRequest::STATUS_NOT_PROCESSED;
        }

        $items_count = PatientMergeRequest::model()->count($criteria);
        $pagination = new CPagination($items_count);
        $pagination->pageSize = 15;
        $pagination->applyLimit($criteria);

        $data_provider = new CActiveDataProvider('PatientMergeRequest', array(
            'criteria' => $criteria,
            'pagination' => $pagination,
            'sort' => array(
                'defaultOrder' => ($filters['show_merged'] ? 'last_modified_date' : 'created_date').' DESC',
            ),
        ));

        $this->pageTitle = 'Merge Requests';
        $this->render('//patientmergerequest/index', array(
            'data_provider' => $data_provider,
            'filters' => $filters,
        ));
    }

    public function actionCreate()
    {
        $model = new PatientMergeRequest();
        $merge_handler = new PatientMerge();
        $patient_merge_request = Yii::app()->request->getParam('PatientMergeRequest', null);

        $model->attributes = $patient_merge_request;

        $personal_details_conflict_confirm = null;

        if ($patient_merge_request && isset($patient_merge_request['primary_id']) && isset($patient_merge_request['secondary_id'])) {
            $primary_patient = Patient::model()->findByPk($patient_merge_request['primary_id']);
            $secondary_patient = Patient::model()->findByPk($patient_merge_request['secondary_id']);

            $merge_errors = $merge_handler->isMergable($primary_patient, $secondary_patient);

            if ($merge_errors) {
                foreach ($merge_errors as $merge_error) {
                    $model->addError($merge_error['attribute'], $merge_error['message']);
                }
            }

            //non local patient cannot be merged into local patient
            if ( $secondary_patient->is_local == 0 && $primary_patient->is_local == 1 ) {
                $model->addError('secondary_id', 'Non local patient cannot be merged into local patient');
            }

            //check if the patients' ids are already submited
            // we do not allow the same patient id in the list multiple times
            $criteria = new CDbCriteria();

            // as secondary records will be deleted the numbers cannot be in the secondary columns
            $criteria->condition = '(secondary_id=:secondary_id OR secondary_id=:primary_id) ';

            //we allow primary patients only if it has no active/unmerged requests
            $criteria->condition .= 'AND ( (primary_id=@primary AND STATUS != 20) OR (primary_id=@secondary AND STATUS != 20) )';
            $criteria->condition .= ' AND t.deleted = 0';
            $criteria->params = array(':primary_id' => $patient_merge_request['primary_id'], ':secondary_id' => $patient_merge_request['secondary_id']);

            $numbers_not_unique = PatientMergeRequest::model()->find($criteria);

            $personal_details_conflict_confirm = $merge_handler->comparePatientDetails($primary_patient, $secondary_patient);

            if (empty($numbers_not_unique) && (!$personal_details_conflict_confirm['is_conflict'] || ($personal_details_conflict_confirm['is_conflict'] && isset($patient_merge_request['personal_details_conflict_confirm'])))) {
                // the Primary and Secondary user cannot be the same user , same database record I mean
                if ((!empty($patient_merge_request['secondary_id']) && !empty($patient_merge_request['primary_id'])) && $patient_merge_request['secondary_id'] == $patient_merge_request['primary_id']) {
                    Yii::app()->user->setFlash('warning.merge_error', 'The Primary and Secondary patient cannot be the same. Record cannot be merged into itself.');
                } else {
                    if (empty($patient_merge_request['secondary_id']) || empty($patient_merge_request['primary_id'])) {
                        Yii::app()->user->setFlash('warning.merge_error', 'Both Primary and Secondary patients have to be selected.');
                    } else {
                        if ($model->save()) {
                            $this->redirect(array('index'));
                        }
                    }
                }
            } elseif ($personal_details_conflict_confirm['is_conflict'] && !isset($patient_merge_request['personal_details_conflict_confirm'])) {
                Yii::app()->user->setFlash('warning.user_error', 'Please tick the checkboxes.');
            } elseif ($numbers_not_unique) {
                Yii::app()->user->setFlash('warning.merge_error_duplicate', 'One of the Hospital Numbers are already in the Patient Merge Request list, please merge them first.');
                $this->redirect(array('index'));
            }
        }

        if ($personal_details_conflict_confirm && $personal_details_conflict_confirm['is_conflict'] == true) {
            foreach ($personal_details_conflict_confirm['details'] as $conflict) {
                Yii::app()->user->setFlash('warning.merge_error_'.$conflict['column'], 'Patients have different personal details : '.$conflict['column']);
            }
        }

        $this->pageTitle = 'Create Patient Merge Request';
        $this->render('//patientmergerequest/create', array(
            'model' => $model,
        ));
    }

    /**
     * Displays a particular model.
     *
     * @param int $id the ID of the model to be displayed
     */
    public function actionLog($id)
    {
        $model = $this->loadModel($id);

        $log = array();
        foreach (json_decode($model->merge_json, true)['log'] as $key => $log_row) {
            $log[] = array(
                'id' => $key,
                'log' => $log_row,
            );
        }

        $this->pageTitle = 'Patient Merge Request Log';
        $this->render('//patientmergerequest/log', array(
            'model' => $model,
            'data_provider' => new CArrayDataProvider($log),
        ));
    }

    /**
     * Displays a particular model.
     *
     * @param int $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $model = $this->loadModel($id);

        $this->pageTitle = 'View Patient Merge Request';
        $this->render('//patientmergerequest/view', array(
            'model' => $model,
        ));
    }

   /**
    * Updates a particular model.
    * If update is successful, the browser will be redirected to the 'view' page.
    *
    * @param int $id the ID of the model to be updated
    */
    public function actionUpdate($id)
    {
        $merge_request = $this->loadModel($id);
        $merge_handler = new PatientMerge();

        // if the personal details are conflictng (DOB and Gender at the moment) we need extra confirmation
        $personal_details_conflict_confirm = $merge_handler->comparePatientDetails($merge_request->primaryPatient, $merge_request->secondaryPatient);
        if ($personal_details_conflict_confirm && $personal_details_conflict_confirm['is_conflict'] == true) {
            foreach ($personal_details_conflict_confirm['details'] as $conflict) {
                Yii::app()->user->setFlash('warning.merge_error_'.$conflict['column'], 'Patients have different personal details : '.$conflict['column']);
            }
        }

        if (isset($_POST['PatientMergeRequest'])) {
            if (!$personal_details_conflict_confirm['is_conflict'] || ($personal_details_conflict_confirm['is_conflict'] && isset($_POST['PatientMergeRequest']['personal_details_conflict_confirm']))) {
                $merge_request->attributes = $_POST['PatientMergeRequest'];
                if ($merge_request->status == PatientMergeRequest::STATUS_MERGED) {
                    $this->redirect(array('view', 'id' => $merge_request->id));
                } elseif ($merge_request->save()) {
                    $this->redirect(array('index'));
                }
            } elseif ($personal_details_conflict_confirm['is_conflict'] && !isset($_POST['PatientMergeRequest']['personal_details_conflict_confirm'])) {
                Yii::app()->user->setFlash('warning.user_error', 'Please tick the checkboxes.');
            }
        }

        $primary = Patient::model()->findByPk($merge_request->primary_id);
        $secondary = Patient::model()->findByPk($merge_request->secondary_id);

        $this->pageTitle = 'Update Patient Merge Request';
        $this->render('//patientmergerequest/update', array(
            'model' => $merge_request,
            'personal_details_conflict_confirm' => $personal_details_conflict_confirm['is_conflict'],
            'primary_patient_JSON' => CJavaScript::jsonEncode(array(
                            'id' => $primary->id,
                            'first_name' => $primary->first_name,
                            'last_name' => $primary->last_name,
                            'age' => ($primary->isDeceased() ? 'Deceased' : $primary->getAge()),
                            'gender' => $primary->getGenderString(),
                            'genderletter' => $primary->gender,
                            'dob' => ($primary->dob) ? $primary->NHSDate('dob') : 'Unknown',
                            'hos_num' => $primary->hos_num,
                            'is_local' => $primary->is_local,
                            'nhsnum' => $primary->nhsnum,
                            'all-episodes' => htmlentities(str_replace(array("\n", "\r", "\t"), '', $this->getEpisodesHTML($primary))),
                        )),

            'secondary_patient_JSON' => CJavaScript::jsonEncode(array(
                            'id' => $secondary->id,
                            'first_name' => $secondary->first_name,
                            'last_name' => $secondary->last_name,
                            'age' => ($secondary->isDeceased() ? 'Deceased' : $secondary->getAge()),
                            'gender' => $secondary->getGenderString(),
                            'genderletter' => $secondary->gender,
                            'dob' => ($secondary->dob) ? $secondary->NHSDate('dob') : 'Unknown',
                            'hos_num' => $secondary->hos_num,
                            'is_local' => $secondary->is_local,
                            'nhsnum' => $secondary->nhsnum,
                            'all-episodes' => htmlentities(str_replace(array("\n", "\r", "\t"), '', $this->getEpisodesHTML($secondary))),
                        )),
            ));
    }

    /**
     * Merging patients.
     *
     * @param int $id the ID of the model to be displayed
     */
    public function actionMerge($id)
    {
        $merge_request = $this->loadModel($id);

        //if the model already merged we just redirect to the index page
        if ($merge_request->status == PatientMergeRequest::STATUS_MERGED) {
            $this->redirect(array('index'));
        }

        $merge_handler = new PatientMerge();

        // if the personal details are conflictng (DOB and Gender at the moment) we need extra confirmation
        $personal_details_conflict_confirm = $merge_handler->comparePatientDetails($merge_request->primaryPatient, $merge_request->secondaryPatient);

        if ($personal_details_conflict_confirm && $personal_details_conflict_confirm['is_conflict'] == true) {
            foreach ($personal_details_conflict_confirm['details'] as $conflict) {
                Yii::app()->user->setFlash('warning.merge_error_'.$conflict['column'], 'Patients have different personal details : '.$conflict['column']);
            }
        }

        if (isset($_POST['PatientMergeRequest']) && isset($_POST['PatientMergeRequest']['confirm']) && Yii::app()->user->checkAccess('Patient Merge')) {
            // if personal details are not conflicting than its fine,
            // but if there is a conflict we need the extra confirmation
            if (!$personal_details_conflict_confirm['is_conflict'] || ($personal_details_conflict_confirm['is_conflict'] && isset($_POST['PatientMergeRequest']['personal_details_conflict_confirm']))) {
                // Load data from PatientMergeRequest AR record
                $merge_handler->load($merge_request);

                try {
                    if ($merge_handler->merge()) {
                        $msg = 'Merge Request '.$merge_request->secondaryPatient->hos_num.' INTO '.$merge_request->primaryPatient->hos_num.'(hos_num) successfully done.';
                        $merge_handler->addLog($msg);
                        $merge_request->status = $merge_request::STATUS_MERGED;
                        $merge_request->merge_json = json_encode(array('log' => $merge_handler->getLog()));
                        $merge_request->save();
                        Audit::add('Patient Merge', 'Patient Merge Request successfully done.', $msg);
                        $this->redirect(array('log', 'id' => $merge_request->id));
                    } else {
                        $msg = 'Merge Request '.$merge_request->secondaryPatient->hos_num.' INTO '.$merge_request->primaryPatient->hos_num.' FAILED.';
                        $merge_handler->addLog($msg);
                        $merge_request->status = $merge_request::STATUS_CONFLICT;
                        $merge_request->merge_json = json_encode(array('log' => $merge_handler->getLog()));
                        $merge_request->save();
                        Yii::app()->user->setFlash('warning.search_error', 'Merge failed.');
                        $this->redirect(array('index'));
                    }
                } catch (Exception $exception) {
                    Yii::app()->user->getFlashes(true);
                    Yii::app()->user->setFlash('warning.merge_error', 'Merge failed.');
                    Yii::app()->user->setFlash('warning.merge_exp', $exception->getMessage());

                    $this->redirect(array('view', 'id' => $merge_request->id));
                }
            }
        }

        $this->pageTitle = 'Patient Merge';
        $this->render('//patientmergerequest/merge', array(
            'model' => $merge_request,
            'personal_details_conflict_confirm' => $personal_details_conflict_confirm['is_conflict'],

        ));
    }

    public function actionDelete()
    {
        if (isset($_POST['patient_merge_request_ids'])) {
            $criteria = new CDbCriteria();
            $criteria->condition = 't.status = '.PatientMergeRequest::STATUS_NOT_PROCESSED;

            $requests = PatientMergeRequest::model()->findAllByPk($_POST['patient_merge_request_ids'], $criteria);

            foreach ($requests as $request) {
                $request->deleted = 1;

                if ($request->save()) {
                    Audit::add('Patient Merge', 'Patient Merge Request flagged as deleted.', "Patient merge request id:{$request->id} deleted.");
                } else {
                    throw new Exception('Unable to save Patient Merge Request: '.print_r($request->getErrors(), true));
                }
            }
        }

        echo CJavaScript::jsonEncode(array('success' => 1));
        Yii::app()->end();
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     *
     * @param int $id the ID of the model to be loaded
     *
     * @return PatientMergeRequest the loaded model
     *
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = PatientMergeRequest::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }

    /**
     * Check if the paient id is already in the request list.
     *
     * @param int $patientId
     *
     * @return null|string 'primary' or 'secondary', this means, e.g.:  patient id was submited for merge as secondary patient
     */
    public function isPatientInRequestList($patientId)
    {
        $criteria = new CDbCriteria();

        $criteria->condition = '(secondary_id=:patient_id OR ( primary_id=:patient_id AND status = '.PatientMergeRequest::STATUS_NOT_PROCESSED.')) AND deleted = 0';

        $criteria->params = array(':patient_id' => $patientId);

        $merge_request = PatientMergeRequest::model()->find($criteria);

        return $merge_request ? ($merge_request->primary_id == $patientId ? 'primary' : 'secondary') : null;
    }

    public function actionSearch()
    {
        $term = trim(\Yii::app()->request->getParam('term', ''));
        $result = array();

        $patient_search = new PatientSearch();

        if ($patient_search->isValidSearchTerm($term)) {
            $data_provider = $patient_search->search($term);
            foreach ($data_provider->getData() as $patient) {
                // check if the patient is already in the Request List
                $warning = array();
                $notice = array();
                $is_in_list = $this->isPatientInRequestList($patient->id);
                if ($is_in_list) {
                    $warning[] = "This patient is already requested for merge as $is_in_list patient.";
                }
                if ($patient->is_local) {
                    $notice[] = "Local patient";
                }

                $subject = null;
                $genetics_panel = null;
                if ($api = $this->getApp()->moduleAPI->get('Genetics')) {
                    $subject = $api->getSubject($patient);
                    if ($subject) {
                        $genetics_panel = $this->getGeneticsHTML($patient);
                    }
                }

                $result[] = array(
                    'id' => $patient->id,
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'age' => ($patient->isDeceased() ? 'Deceased' : $patient->getAge()),
                    'gender' => $patient->getGenderString(),
                    'genderletter' => $patient->gender,
                    'dob' => ($patient->dob) ? $patient->NHSDate('dob') : 'Unknown',
                    'hos_num' => $patient->hos_num,
                    'nhsnum' => $patient->nhs_num,
                    'is_local' => $patient->is_local ? 1 : 0,
                    'all-episodes' => $this->getEpisodesHTML($patient),
                    'warning' => $warning,
                    'notice' => $notice,
                    'genetics-panel' => $genetics_panel,
                    'subject_id' => $subject ? $subject->id : null,
                );
            }
        }

        echo CJavaScript::jsonEncode($result);
        Yii::app()->end();
    }

    public function getEpisodesHTML($patient)
    {
        $episodes = $patient->episodes;

        $episodes_open = 0;
        $episodes_closed = 0;

        foreach ($episodes as $episode) {
            if ($episode->end_date === null) {
                ++$episodes_open;
            } else {
                ++$episodes_closed;
            }
        }

        $html = $this->renderPartial('//patient/_patient_all_episodes', array(
            'episodes' => $episodes,
            'ordered_episodes' => $patient->getOrderedEpisodes(),
            'legacyepisodes' => $patient->legacyepisodes,
            'episodes_open' => $episodes_open,
            'episodes_closed' => $episodes_closed,
            'firm' => $this->firm,
        ), true);

       // you don't know how much I hate this str_replace here, but now it seems a painless method to remove a class
        return str_replace('box patient-info episodes', 'box patient-info', $html);
    }

    /**
     * @todo: look at encapsulating in the genetics module completely
     *
     * @param GeneticsPatient $subject
     * @return null|string
     */
    public function getGeneticsHTML(Patient $patient)
    {
        $html = null;
        if ($this->getApp()->moduleAPI->get('Genetics')) {
            $html = $this->renderPartial('application.modules.Genetics.views.patientSummary._patient_genetics', array(
                'patient' => $patient
            ), true);
        }

        return $html;
    }
}
