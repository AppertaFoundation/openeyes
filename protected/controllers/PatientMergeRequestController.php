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
        
        $cookie_key = 'show_merged_' . Yii::app()->user->id;
                
        if( (isset($filters['show_merged']) && $filters['show_merged'] == 1) ){
            // turn ON the show_merge filter
            $cookie_value = 1;
        } else if (isset($filters['show_merged']) && $filters['show_merged'] == 0) {
            // turn OFF the show_merge filter
            $cookie_value = 0;
        } else if ( Yii::app()->request->cookies->contains($cookie_key) ){
            // get back the cookie value if it is set
            $cookie_value = Yii::app()->request->cookies[$cookie_key]->value;
        } else {
            // neither 'show_merged' in the get/post nor in the cookies
            $cookie_value = 0;
        }
        
        Yii::app()->request->cookies[$cookie_key] = new CHttpCookie($cookie_key, $cookie_value);
        $filters['show_merged'] = $cookie_value;
        
        $criteria = new CDbCriteria;
        $criteria->compare('deleted', 0);
        $criteria->addCondition('status != :status');
        $criteria->addSearchCondition('secondary_hos_num', $filters['secondary_hos_num_filter']);
        $criteria->addSearchCondition('primary_hos_num', $filters['primary_hos_num_filter']);
         
        if(!$cookie_value){
            $criteria->params[':status'] = PatientMergeRequest::STATUS_MERGED;
        } else {
            $criteria->params[':status'] = PatientMergeRequest::STATUS_NOT_PROCESSED;
        }
        
        $itemsCount = PatientMergeRequest::model()->count($criteria);
        $pagination = new CPagination($itemsCount);
        $pagination->pageSize = 15;
        $pagination->applyLimit($criteria);
        
        $dataProvider = new CActiveDataProvider('PatientMergeRequest', array(
            'criteria' => $criteria,
            'pagination' => $pagination,
            'sort'=>array(
                'defaultOrder' => ( $filters['show_merged'] ? 'last_modified_date' : 'created_date' ) . ' DESC',
            )
        ));
        
        $this->render('//patientmergerequest/index', array(
            'dataProvider' => $dataProvider,
            'filters' => $filters,
        ));
    }
    
    public function actionCreate()
    {
        
        $model = new PatientMergeRequest;
        $mergeHandler = new PatientMerge;
        $patientMergeRequest = Yii::app()->request->getParam('PatientMergeRequest', null);
        $personalDetailsConflictConfirm = null;
        
        if($patientMergeRequest && isset($patientMergeRequest['primary_id']) && isset($patientMergeRequest['secondary_id']) ) {
            
            $primaryPatient = Patient::model()->findByPk($patientMergeRequest['primary_id']);
            $secondaryPatient = Patient::model()->findByPk($patientMergeRequest['secondary_id']);
            
            //check if the patients' ids are already submited
            // we do not allow the same patient id in the list multiple times
            $criteria = new CDbCriteria();
            
            //$criteria->condition = '((primary_id=:primary_id OR primary_id=:secondary_id OR secondary_id=:secondary_id OR secondary_id=:primary_id) AND ( deleted = 0))';
            
            // as secondary records will be deleted the numbers cannot be in the secondry columns
            $criteria->condition = '(secondary_id=:secondary_id OR secondary_id=:primary_id) ';
            
            //we allow primary patients only if it has no active/unmerged requests
            $criteria->condition .= 'AND ( (primary_id=@primary AND STATUS != 20) OR (primary_id=@secondary AND STATUS != 20) )';
            
            $criteria->params = array(':primary_id' => $patientMergeRequest['primary_id'], ':secondary_id' => $patientMergeRequest['secondary_id']);
            
            $numbersNotUnique = PatientMergeRequest::model()->find($criteria);
            
            $personalDetailsConflictConfirm = $mergeHandler->comparePatientDetails($primaryPatient, $secondaryPatient);
            
            if(empty($numbersNotUnique) && ( !$personalDetailsConflictConfirm['isConflict'] || ($personalDetailsConflictConfirm['isConflict'] && isset($patientMergeRequest['personalDetailsConflictConfirm']))) ){

                // the Primary and Secondary user cannot be the same user , same database record I mean
                if( ( !empty($patientMergeRequest['secondary_id']) && !empty($patientMergeRequest['primary_id']) )&& $patientMergeRequest['secondary_id'] == $patientMergeRequest['primary_id']){
                    Yii::app()->user->setFlash("warning.merge_error", "The Primary and Secondary patient cannot be the same. Record cannot be merged into itself.");
                } else {

                    if ( empty($patientMergeRequest['secondary_id']) || empty($patientMergeRequest['primary_id'])){
                        Yii::app()->user->setFlash("warning.merge_error", "Both Primary and Secondary patients have to be selected.");
                    } else {
                        $model->attributes = $patientMergeRequest;
                        if($model->save()){
                            $this->redirect(array('index'));
                        }
                    }
                }
            } else if( $personalDetailsConflictConfirm['isConflict'] && !isset($patientMergeRequest['personalDetailsConflictConfirm']) ){
                Yii::app()->user->setFlash('warning.user_error', "Please tick the checkboxes.");
            } else if ($numbersNotUnique){
                Yii::app()->user->setFlash('warning.merge_error_duplicate', "One of the Hospital Numbers are already in the Patient Merge Request list, please merge them first.");
                $this->redirect(array('index'));
            }
        }
        
        if($personalDetailsConflictConfirm && $personalDetailsConflictConfirm['isConflict'] == true){
            foreach($personalDetailsConflictConfirm['details'] as $conflict){
                Yii::app()->user->setFlash("warning.merge_error_" . $conflict['column'], "Patients have different personal details : " . $conflict['column']);
            }
        }
        
        $this->render('//patientmergerequest/create',array(
            'model' => $model,
        ));
    }
    
    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionLog($id)
    {
        $model = $this->loadModel($id);
        
        $log = array();
        foreach(json_decode($model->merge_json, true)['log'] as $key => $log_row){
            $log[] = array(
                'id' => $key,
                'log' => $log_row
            );
        }
        
        $this->render('//patientmergerequest/log', array(
            'model' => $model,
            'dataProvider' => new CArrayDataProvider( $log ),
        ));
    }
    
    
    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $model = $this->loadModel($id);
        
        $this->render('//patientmergerequest/view', array(
            'model' => $model,
        ));
    }
    
    
    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
    */
   public function actionUpdate($id)
   {
        $mergeRequest = $this->loadModel($id);
        $mergeHandler = new PatientMerge;
        
        // if the personal details are conflictng (DOB and Gender at the moment) we need extra confirmation
        $personalDetailsConflictConfirm = $mergeHandler->comparePatientDetails($mergeRequest->primaryPatient, $mergeRequest->secondaryPatient);
        if($personalDetailsConflictConfirm && $personalDetailsConflictConfirm['isConflict'] == true){
            foreach($personalDetailsConflictConfirm['details'] as $conflict){
                Yii::app()->user->setFlash("warning.merge_error_" . $conflict['column'], "Patients have different personal details : " . $conflict['column']);
            }
        }

        if(isset($_POST['PatientMergeRequest']))
        {
            if( !$personalDetailsConflictConfirm['isConflict'] || ($personalDetailsConflictConfirm['isConflict'] && isset($_POST['PatientMergeRequest']['personalDetailsConflictConfirm'])) ){
                
                $mergeRequest->attributes=$_POST['PatientMergeRequest'];
                if( $mergeRequest->status == PatientMergeRequest::STATUS_MERGED ){
                    $this->redirect(array('view','id' => $mergeRequest->id));
                } else if($mergeRequest->save()) {
                   $this->redirect(array('index'));
                }
            } else if( $personalDetailsConflictConfirm['isConflict'] && !isset($_POST['PatientMergeRequest']['personalDetailsConflictConfirm']) ){
                Yii::app()->user->setFlash('warning.user_error', "Please tick the checkboxes.");
            }
        }

        $primary = Patient::model()->findByPk($mergeRequest->primary_id);
        $secondary = Patient::model()->findByPk($mergeRequest->secondary_id);

        $this->render('//patientmergerequest/update', array(
            'model' => $mergeRequest,
            'personalDetailsConflictConfirm' => $personalDetailsConflictConfirm['isConflict'],
            'primaryPatientJSON' => CJavaScript::jsonEncode(array(
                            'id' => $primary->id,
                            'first_name' => $primary->first_name,
                            'last_name' => $primary->last_name,
                            'age' => ($primary->isDeceased() ? 'Deceased' : $primary->getAge()),
                            'gender' => $primary->getGenderString(),
                            'genderletter' => $primary->gender,
                            'dob' => ($primary->dob) ? $primary->NHSDate('dob') : 'Unknown',
                            'hos_num' => $primary->hos_num, 
                            'nhsnum' => $primary->nhsnum,
                            'all-episodes' => htmlentities (str_replace(array("\n", "\r", "\t"), '', $this->getEpisodesHTML($primary) ) ),
                        )
                    ),

            'secondaryPatientJSON' => CJavaScript::jsonEncode(array(
                            'id' => $secondary->id,
                            'first_name' => $secondary->first_name,
                            'last_name' => $secondary->last_name,
                            'age' => ($secondary->isDeceased() ? 'Deceased' : $secondary->getAge()),
                            'gender' => $secondary->getGenderString(),
                            'genderletter' => $secondary->gender,
                            'dob' => ($secondary->dob) ? $secondary->NHSDate('dob') : 'Unknown',
                            'hos_num' => $secondary->hos_num,
                            'nhsnum' => $secondary->nhsnum,
                            'all-episodes' => htmlentities (str_replace(array("\n", "\r", "\t"), '', $this->getEpisodesHTML($secondary) ) ),
                        )
                    ),
            ));
   }
    
    /**
     * Merging patients
     * @param integer $id the ID of the model to be displayed
     */
    public function actionMerge($id)
    {
        $mergeRequest = $this->loadModel($id);
        
        //if the model already merged we just redirect to the index page
        if( $mergeRequest->status == PatientMergeRequest::STATUS_MERGED ){
            $this->redirect(array('index'));
        }

        $mergeHandler = new PatientMerge;
        
        // if the personal details are conflictng (DOB and Gender at the moment) we need extra confirmation
        $personalDetailsConflictConfirm = $mergeHandler->comparePatientDetails($mergeRequest->primaryPatient, $mergeRequest->secondaryPatient);
        if($personalDetailsConflictConfirm && $personalDetailsConflictConfirm['isConflict'] == true){
            foreach($personalDetailsConflictConfirm['details'] as $conflict){
                Yii::app()->user->setFlash("warning.merge_error_" . $conflict['column'], "Patients have different personal details : " . $conflict['column']);
            }
        }
        
        if(isset($_POST['PatientMergeRequest']) && isset($_POST['PatientMergeRequest']['confirm']) && Yii::app()->user->checkAccess('Patient Merge')){
                
            // if personal details are not conflictin than its fine, 
            // but if there is a conflict we need the extra confirmation
            if( !$personalDetailsConflictConfirm['isConflict'] || ($personalDetailsConflictConfirm['isConflict'] && isset($_POST['PatientMergeRequest']['personalDetailsConflictConfirm'])) ){

                // Load data from PatientMergeRequest AR record
                $mergeHandler->load($mergeRequest);

                if($mergeHandler->merge()){
                    $msg = "Merge Request " . $mergeRequest->secondaryPatient->hos_num . " INTO " . $mergeRequest->primaryPatient->hos_num . "(hos_num) successfully done.";
                    $mergeHandler->addLog($msg);
                    $mergeRequest->status = $mergeRequest::STATUS_MERGED;
                    $mergeRequest->merge_json = json_encode( array( 'log' => $mergeHandler->getLog() ) );
                    $mergeRequest->save();
                    Audit::add('Patient Merge', $msg);
                    $this->redirect(array('log', 'id' => $mergeRequest->id));
                } else {
                    $msg = "Merge Request " . $mergeRequest->secondaryPatient->hos_num . " INTO " . $mergeRequest->primaryPatient->hos_num . " FAILED.";
                    $mergeHandler->addLog($msg);
                    $mergeRequest->status = $mergeRequest::STATUS_CONFLICT;
                    $mergeRequest->merge_json = json_encode( array( 'log' => $mergeHandler->getLog() ) );
                    $mergeRequest->save();
                    Yii::app()->user->setFlash('warning.search_error', "Merge failed.");
                    $this->redirect(array('index'));
                }
            }
        }
        
        $primary = Patient::model()->findByPk($mergeRequest->primary_id);
        $secondary = Patient::model()->findByPk($mergeRequest->secondary_id);
        
        $this->render('//patientmergerequest/merge', array(
            'model' => $mergeRequest,
            'personalDetailsConflictConfirm' => $personalDetailsConflictConfirm['isConflict'],
            
        ));
    }
    
    public function actionDelete()
    {        
        if( isset($_POST['patientMergeRequestIds']) ){
           
            $criteria = new CDbCriteria();
            $criteria->condition = "t.status = " . PatientMergeRequest::STATUS_NOT_PROCESSED;
            
            $requests = PatientMergeRequest::model()->findAllByPk($_POST['patientMergeRequestIds'], $criteria);

            foreach ($requests as $request) {

                $request->deleted = 1;
               
                if ( $request->save() ) {
                    Audit::add('Patient Merge', 'Patient Merge Request flagged as deleted. id: ' . $request->id);
                } else {
                    throw new Exception("Unable to save Patient Merge Request: ".print_r($request->getErrors(),true));
                }
            }
        }
        
        echo CJavaScript::jsonEncode( array('success' => 1) );
        Yii::app()->end();
    }
    
    
    
 /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return PatientMergeRequest the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = PatientMergeRequest::model()->findByPk($id);
        if($model === null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }
    
    /**
     * Check if the paient id is already in the request list
     * 
     * @param int $patientId
     * @return null|string 'primary' or 'secondary', this means, e.g.:  patient id was submited for merge as secondary patient
     */
    public function isPatientInRequestList($patientId)
    {
        $criteria = new CDbCriteria();
        
        $criteria->condition = 'secondary_id=:patient_id OR ( primary_id=:patient_id AND status = ' . PatientMergeRequest::STATUS_NOT_PROCESSED . ') AND deleted = 0';
        
        $criteria->params = array(':patient_id' => $patientId);
        
        $mergeRequest = PatientMergeRequest::model()->find($criteria);
        
        return $mergeRequest ? ( $mergeRequest->primary_id == $patientId ? 'primary' : 'secondary' ) : null;
    }
   
    public function actionSearch()
    {
        $term = trim(\Yii::app()->request->getParam("term", ""));
        $result = array();
        
        $patientSearch = new PatientSearch();
        
        if($patientSearch->isValidSearchTerm($term)){
            $dataProvider = $patientSearch->search($term);
            foreach($dataProvider->getData() as $patient){
                
                // check if the patient is already in the Request List
                $warning = '';
                $isInList = $this->isPatientInRequestList($patient->id);
                if($isInList){
                    $warning = "This patient is already requested for merge as $isInList patient.";
                }
                
                $result[] =  array(
                    'id' => $patient->id,
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'age' => ($patient->isDeceased() ? 'Deceased' : $patient->getAge()),
                    'gender' => $patient->getGenderString(),
                    'genderletter' => $patient->gender,
                    'dob' => ($patient->dob) ? $patient->NHSDate('dob') : 'Unknown',
                    'hos_num' => $patient->hos_num, 
                    'nhsnum' => $patient->nhsnum,
                    'all-episodes' => $this->getEpisodesHTML($patient),
                    'warning' => $warning
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
                $episodes_open++;
            } else {
                $episodes_closed++;
            }
        }
        
        $html = $this->renderPartial('//patient/_patient_all_episodes',array(
            'episodes' => $episodes,
            'ordered_episodes' => $patient->getOrderedEpisodes(),
            'legacyepisodes' => $patient->legacyepisodes,
            'episodes_open' => $episodes_open,
            'episodes_closed' => $episodes_closed,
            'firm' => $this->firm,
        ), true);
       
       // you don't know how much I hate this str_replace here, but now it seems a painless method to remove a class
       return str_replace("box patient-info episodes", "box patient-info", $html);
   }
}