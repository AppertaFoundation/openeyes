<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\controllers\oeadmin;

use OEModule\OphCiExamination\models;

class SystemicDiagAssignmentController extends \ModuleAdminController
{
    public $group = 'Examination';

    public function accessRules()
    {
        return array(
            array('allow', 'users' => array('@')),
        );
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {

        $diagnoses_set = new models\OphCiExaminationSystemicDiagnosesSet();
        $diagnoses_set->unsetAttributes();
        if(isset($_GET['OphCiExaminationSystemicDiagnoses']))
            $diagnoses_set->attributes=$_GET['OphCiExaminationSystemicDiagnoses'];

        $this->render('/admin/systemicdiagnosesassignment/index',array(
            'model' => $diagnoses_set,
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $diagnoses_set = new models\OphCiExaminationSystemicDiagnosesSet;

        if(isset($_POST['OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSet']))
        {
            $diagnoses_set->attributes=$_POST['OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSet'];

            $transaction = \Yii::app()->db->beginTransaction();

            try {
                if($diagnoses_set->save()){

                    $entries_array = \Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry', array());
                    foreach($entries_array as $entry_array){
                        $entry = new models\OphCiExaminationSystemicDiagnosesSetEntry;

                        $entry->gender = $entry_array['gender'];
                        $entry->age_min = $entry_array['age_min'];
                        $entry->age_max = $entry_array['age_max'];
                        $entry->disorder_id = $entry_array['disorder_id'];

                        if($entry->save()){
                            $this->saveAssignment($diagnoses_set, $entry);
                        }
                    }

                    $transaction->commit();

                    $this->redirect(array('index'));
                }
            } catch (\Exception $e) {
                \OELog::log($e->getMessage());
                $transaction->rollback();
            }
        }

        $this->render('/admin/systemicdiagnosesassignment/edit',array(
            'model' => $diagnoses_set,
            'title' => 'Create required Systemic Diagnoses set',
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     * @throws CHttpException
     */
    public function actionUpdate($id)
    {
        $diagnoses_set = $this->loadModel($id);

        if(isset($_POST['OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSet']))
        {
            $diagnoses_set->attributes=$_POST['OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSet'];
            $entries = \Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry', array());

            $transaction = \Yii::app()->db->beginTransaction();

            try {
                $posted_entry_ids = array();
                foreach($entries as $entry){
                    if(isset($entry['id'])){
                        $posted_entry_ids[] = $entry['id'];
                    }
                }

                if($diagnoses_set->save()){

                    foreach($entries as $entry){

                        if(isset($entry['id']) && $entry['id']){
                            $entry_model = models\OphCiExaminationSystemicDiagnosesSetEntry::model()->findByPk($entry['id']);
                        } else {
                            $entry_model = new models\OphCiExaminationSystemicDiagnosesSetEntry;
                        }

                        $entry_model->gender = $entry['gender'];
                        $entry_model->age_min = $entry['age_min'];
                        $entry_model->age_max = $entry['age_max'];
                        $entry_model->disorder_id = $entry['disorder_id'];

                        if($entry_model->save()){
                           $this->saveAssignment($diagnoses_set, $entry_model);
                            $posted_entry_ids[] = $entry_model->id;
                        }
                    }

                    // Removed items
                    $criteria = new \CDbCriteria();
                    $criteria->addCondition('systemic_diagnoses_set_id =:diagnoses_set');
                    $criteria->addNotInCondition('systemic_diagnoses_set_entry_id', $posted_entry_ids);
                    $criteria->params[':diagnoses_set'] = $diagnoses_set->id;
                    
                    $assignments = models\OphCiExaminationSystemicDiagnosesSetAssignment::model()->findAll($criteria);
                    foreach($assignments as $assignment){
                        $entry_id = $assignment->systemic_diagnoses_set_entry_id;

                         if($assignment->delete()){
                             models\OphCiExaminationSystemicDiagnosesSetEntry::model()->findByPk($entry_id)->delete();
                         }
                    }
                }

                $transaction->commit();
                \Yii::app()->user->setFlash('success', 'Set updated.');
                $this->redirect(array('index'));

            } catch (\Exception $e) {
                \OELog::log($e->getMessage());
                $transaction->rollback();
                \Yii::app()->user->setFlash('error', 'Something went wrong. Set did not updated.');
            }
        }

        $this->render('/admin/systemicdiagnosesassignment/edit',array(
            'model' => $diagnoses_set,
            'title' => 'Edit required Systemic Diagnoses set',
        ));
    }

    private function saveAssignment($set, $entry)
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('systemic_diagnoses_set_id = :set_id');
        $criteria->addCondition('systemic_diagnoses_set_entry_id = :entry_id');
        $criteria->params[':set_id'] = $set->id;
        $criteria->params[':entry_id'] = $entry->id;

        $assignment = models\OphCiExaminationSystemicDiagnosesSetAssignment::model()->find($criteria);

        if(!$assignment){
            $assignment = new models\OphCiExaminationSystemicDiagnosesSetAssignment;
            $assignment->systemic_diagnoses_set_entry_id = $entry->id;
            $assignment->systemic_diagnoses_set_id = $set->id;

            if(!$assignment->save()){
                throw new \Exception('OphCiExaminationSystemicDiagnosesSetAssignment cannot be saved.');
            }
        }
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     * @throws CHttpException
     */
    public function actionDelete()
    {
        $model_ids = \Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSet', array());

        foreach($model_ids as $model_id){

            $model = $this->loadModel($model_id);
            if(!$model->entries){
                $model->delete();
            } else {
                echo "0";
                \Yii::app()->end();
            }
        }

        //handleButton.js's handleButton($('#et_delete') function needs this return
        echo "1";
        \Yii::app()->end();
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return OphCiExaminationSystemicDiagnosesSet the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = models\OphCiExaminationSystemicDiagnosesSet::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

}