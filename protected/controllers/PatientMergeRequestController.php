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
                'actions' => array('index', 'create', 'view', 'search'),
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
        
        $this->render('index', array(
            'dataProvider'=>$dataProvider,
        ));
    }
    
    public function actionCreate()
    {
        $model = new PatientMergeRequest;
            
        if(isset($_POST['PatientMergeRequest'])) {
            $model->attributes = $_POST['PatientMergeRequest'];
            if($model->save()){
                $this->redirect(array('view', 'id' => $model->id));
            }
        }
       
        $this->render('create',array(
            'model' => $model,
        ));
    }
    
    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view', array(
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
        
        if(isset($_POST['PatientMergeRequest']) && $_POST['PatientMergeRequest']['confirm']) {
            
            $mergeHandler = new PatientMerge;
            
            // use load to set the properties
            $mergeHandler->load($mergeRequest);
            
            if($mergeHandler->merge()){
                $this->redirect(array('view', 'id' => $mergeRequest->id));
            } else {
                $this->redirect(array('conflict', 'id' => $mergeRequest->id));
            }
        } 
        
        $this->render('merge', array(
            'model' => $mergeRequest,
        ));
    }
    
    public function actionConflict($id){
        echo "Lollipop: $id"; die;
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
        $model=PatientMergeRequest::model()->findByPk($id);
        if($model===null)
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
                
                $helper = new Helper;
                
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
                    'all-episodes' => $this->renderPartial('//patient/_patient_all_episodes',array(
                                                    'episodes' => $episodes,
                                                    'ordered_episodes' => $patient->getOrderedEpisodes(),
                                                    'legacyepisodes' => $patient->legacyepisodes,
                                                    'episodes_open' => $episodes_open,
                                                    'episodes_closed' => $episodes_closed,
                                                    'firm' => $this->firm,
                                            ), true),
                );
            }
        }
        
       echo CJavaScript::jsonEncode($result);
       Yii::app()->end();
       
   }
   
   
}
