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
                'actions' => array('index', 'create', 'view', 'merge', 'editConflict', 'search'),
                'roles' => array('admin'),
            )
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
        $dataProvider = new CActiveDataProvider('PatientMergeRequest');
        
        $this->render('//patientmergerequest/index', array(
            'dataProvider'=>$dataProvider,
        ));
    }
    
    public function actionCreate()
    {
        $model = new PatientMergeRequest;
            
        if(isset($_POST['PatientMergeRequest'])) {
            $model->attributes = $_POST['PatientMergeRequest'];
            if($model->save()){
                $this->redirect(array('index'));
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
    public function actionView($id)
    {
        $this->render('//patientmergerequest/view', array(
            'model' => $this->loadModel($id),
        ));
    }
    
    
    /**
     * Merging patients
     * @param integer $id the ID of the model to be displayed
     */
    public function actionMerge($id)
    {
        $mergeRequest = $this->loadModel($id);
        
        $mergeHandler = new PatientMerge;
        
        // if the personal details are conflictng (DOB and Gender at the moment) we need extra confirmation
        $personalDetailsConflictConfirm = $mergeHandler->comparePatientDetails($mergeRequest->primaryPatient, $mergeRequest->secondaryPatient);
        
        if(isset($_POST['PatientMergeRequest']) && isset($_POST['PatientMergeRequest']['confirm'])){
                
            // if personal details are not conflictin than its fine, 
            // but if there is a conflict we need the extra confirmation
            if( !$personalDetailsConflictConfirm || ($personalDetailsConflictConfirm && isset($_POST['PatientMergeRequest']['personalDetailsConflictConfirm'])) ){

                // Load data from PatientMergeRequest AR record
                $mergeHandler->load($mergeRequest);

                if($mergeHandler->merge()){
                    $mergeRequest->status = $mergeRequest::STATUS_MERGED;
                    $mergeRequest->save();
                    Audit::add('Patient Merge', "Merge Request " . $mergeRequest->secondaryPatient->hos_num . " INTO " . $mergeRequest->primaryPatient->hos_num . "(hos_num) successfully done.");
                    $this->redirect(array('view', 'id' => $mergeRequest->id));
                } else {
                    $mergeRequest->status = $mergeRequest::STATUS_CONFLICT;
                    $mergeRequest->save();
                    Yii::app()->user->setFlash('warning.search_error', "Merge failed.");
                    $this->redirect(array('index'));
                }
            }
        } 
        
        $this->render('//patientmergerequest/merge', array(
            'model' => $mergeRequest,
            'personalDetailsConflictConfirm' => $personalDetailsConflictConfirm
        ));
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
   
    public function actionSearch()
    {
        $term = trim(\Yii::app()->request->getParam("term", ""));
        $result = array();
        
        $patientSearch = new PatientSearch();
        
        if($patientSearch->isValidSearchTerm($term)){
            $dataProvider = $patientSearch->search($term);
            foreach($dataProvider->getData() as $patient){
                
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
                    'all-episodes' => $this->getEpisodesHTML($patient)
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
