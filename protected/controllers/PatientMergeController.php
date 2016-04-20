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

class PatientMergeController extends BaseController
{
    
    /**
     * @var string the default layout for the views
     */
    public $layout = '//layouts/main';
    
    public function accessRules()
    {
            return array(
                    array('allow',
                            'actions' => array('mergeRequest', 'search', 'save'),
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
        return parent::beforeAction($action);
    }
    
    public function actionMergeRequest()
    {   
        $patient = $this->loadModel(19434);

        $this->render('//patientmerge/merge_request', array('patient' => $patient));
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
    
    public function actionSave()
    {
        echo "<pre>" . print_r($_POST, true) . "</pre>";
        die;
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
                    'value' => $patient->id, 
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'age' => ($patient->isDeceased() ? 'Deceased' : $patient->getAge()),
                    'gender' => $patient->getGenderString(),
                    'dob' => ($patient->dob) ? $patient->NHSDate('dob') : 'Unknown',
                    'hos_num' => $patient->hos_num, 
                    'nhsnum' => $patient->nhsnum,
                );
            }
        }
        
            
       echo CJavaScript::jsonEncode($result);
       Yii::app()->end();
       
   }
}
