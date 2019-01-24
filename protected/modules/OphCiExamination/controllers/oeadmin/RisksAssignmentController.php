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

class RisksAssignmentController extends \ModuleAdminController
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

        $model= new models\OphCiExaminationRiskSet();
        $model->unsetAttributes();
        if(isset($_GET['OphCiExaminationRisk']))
            $model->attributes=$_GET['OphCiExaminationRisk'];

        $this->render('/admin/riskassignment/index',array(
            'model' => $model,
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $risk_set = new models\OphCiExaminationRiskSet;

        if (isset($_POST['OEModule_OphCiExamination_models_OphCiExaminationRiskSet'])) {
            $risk_set->attributes = $_POST['OEModule_OphCiExamination_models_OphCiExaminationRiskSet'];
            $transaction = \Yii::app()->db->beginTransaction();
            try {
                if ($risk_set->save()) {
                    $risks = \Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExaminationRiskSetEntry', array());
                    foreach ($risks as $risk) {
                        $risk_model = new models\OphCiExaminationRiskSetEntry;

                        $risk_model->gender = $risk['gender'];
                        $risk_model->age_min = $risk['age_min'];
                        $risk_model->age_max = $risk['age_max'];
                        $risk_model->ophciexamination_risk_id = $risk['ophciexamination_risk_id'];

                        if ($risk_model->save()) {
                            $this->saveAssignment($risk_set, $risk_model);
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

        $this->render('/admin/riskassignment/edit', array(
            'model' => $risk_set,
            'title' => 'Create required risk set',
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
        $risk_set = $this->loadModel($id);

        if(isset($_POST['OEModule_OphCiExamination_models_OphCiExaminationRiskSet']))
        {

            $risk_set->attributes=$_POST['OEModule_OphCiExamination_models_OphCiExaminationRiskSet'];
            $risks = \Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExaminationRiskSetEntry', array());

            $transaction = \Yii::app()->db->beginTransaction();

            try {

                $posted_entry_ids = array();
                foreach($risks as $risk){
                    if(isset($risk['id'])){
                        $posted_entry_ids[] = $risk['id'];
                    }
                }

                if($risk_set->save()){

                    foreach($risks as $risk){

                        if(isset($risk['id'])){
                            $risk_model = models\OphCiExaminationRiskSetEntry::model()->findByPk($risk['id']);
                        } else {
                            $risk_model = new models\OphCiExaminationRiskSetEntry;
                        }

                        $risk_model->gender = $risk['gender'];
                        $risk_model->age_min = $risk['age_min'];
                        $risk_model->age_max = $risk['age_max'];
                        $risk_model->ophciexamination_risk_id = $risk['ophciexamination_risk_id'];

                        if($risk_model->save()){
                           $this->saveAssignment($risk_set, $risk_model);
                            $posted_entry_ids[] = $risk_model->id;
                        }
                    }

                    // Removed items
                    $criteria = new \CDbCriteria();
                    $criteria->addCondition('risk_set_id =:risk_set_id');
                    $criteria->addNotInCondition('ophciexamination_risk_entry_id', $posted_entry_ids);
                    $criteria->params[':risk_set_id'] = $risk_set->id;
                    
                    $assignments = models\OphCiExaminationRiskSetAssignment::model()->findAll($criteria);
                    foreach($assignments as $assignment){
                        $entry_id = $assignment->ophciexamination_risk_entry_id;

                         if($assignment->delete()){
                             models\OphCiExaminationRiskSetEntry::model()->findByPk($entry_id)->delete();
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

        $this->render('/admin/riskassignment/edit',array(
            'model' => $risk_set,
            'title' => 'Edit required risk set',
        ));
    }

    private function saveAssignment($risk_set, $risk_model)
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('risk_set_id = :set_id');
        $criteria->addCondition('ophciexamination_risk_entry_id = :ophciexamination_risk_entry_id');
        $criteria->params[':set_id'] = $risk_set->id;
        $criteria->params[':ophciexamination_risk_entry_id'] = $risk_model->id;

        $assignment = models\OphCiExaminationRiskSetAssignment::model()->find($criteria);

        if(!$assignment){
            $assignment = new models\OphCiExaminationRiskSetAssignment;
            $assignment->ophciexamination_risk_entry_id = $risk_model->id;
            $assignment->risk_set_id = $risk_set->id;

            if(!$assignment->save()){
                throw new \Exception('OphCiExaminationRisk assignment cannot be saved.');
            }
        }
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete()
    {
        $model_ids = \Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExaminationRiskSet', array());

        foreach($model_ids as $model_id){

            $model = $this->loadModel($model_id);
            if(!$model->ophciexamination_risks_entry){
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
     * @return OphCiExaminationRisk the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = models\OphCiExaminationRiskSet::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    /**
     * Returns the consultants by subspecialty
     * @param null $subspecialty_id
     */
    public function actionGetFirmsBySubspecialty($subspecialty_id = null , $runtime_selectable = null)
    {
        $firms = \Firm::model()->getList($subspecialty_id , null , $runtime_selectable);
        echo \CJSON::encode($firms);

        \Yii::app()->end();
    }

}