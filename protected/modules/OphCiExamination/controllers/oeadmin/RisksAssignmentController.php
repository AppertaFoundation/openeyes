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

namespace OEModule\OphCiExamination\controllers;

use OEModule\OphCiExamination\models\OphCiExaminationRisk;
use OEModule\OphCiExamination\models\OphCiExaminationRiskSet;
use OEModule\OphCiExamination\models\OphciexaminationRiskSetAssignment;
use WebDriver\Exception;

class RisksAssignmentController extends \ModuleAdminController
{
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

        $model= new OphCiExaminationRiskSet();
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
        $model = new OphCiExaminationRisk;

        if(isset($_POST['OEModule_OphCiExamination_models_OphCiExaminationRisk']))
        {
            $model->attributes=$_POST['OEModule_OphCiExamination_models_OphCiExaminationRisk'];
            if($model->save())
                $this->redirect(array('index'));
        }

        $this->render('/admin/riskassignment/create',array(
            'model'=>$model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model=$this->loadModel($id);

        if(isset($_POST['OEModule_OphCiExamination_models_OphciexaminationRiskSet']))
        {
            $model->attributes=$_POST['OEModule_OphCiExamination_models_OphciexaminationRiskSet'];

            if($model->save()){

                $risks = \Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExaminationRisk', array());

                foreach($risks as $risk){

                    if(isset($risk['id'])){
                        $risk_model = OphCiExaminationRisk::model()->findByPk($risk['id']);
                    } else {
                        throw new \Exception("OphCiExaminationRisk not found");
                    }

                    $risk_model->gender = $risk['gender'];
                    $risk_model->age_min = $risk['age_min'];
                    $risk_model->age_max = $risk['age_max'];

                    if($risk_model->save()){
                        $criteria = new \CDbCriteria();
                        $criteria->addCondition('risk_set_id = :set_id');
                        $criteria->addCondition('ophciexamination_risk_id = :set_id');
                        $criteria->params[':set_id'] = $model->id;
                        $criteria->params[':ophciexamination_risk_id'] = $risk_model->id;

                        $assignment = OphciexaminationRiskSetAssignment::model()->find($criteria);

                        if(!$assignment){
                            $assignment = new OphciexaminationRiskSetAssignment;
                            $assignment->ophciexamination_risk_id = $risk_model->id;
                            $assignment->risk_set_id = $model->id;

                            $assignment->save();
                        }
                    }else{
                        throw new \Exception('OphCiExaminationRisk cannot be saved.');
                    }
                }



            } else {

            }
                $this->redirect(array('index'));
        }

        $this->render('/admin/riskassignment/update',array(
            'model'=>$model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete()
    {
        $model_ids = \Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExaminationRisk', array());

        foreach($model_ids as $model_id){
            $this->loadModel($model_id)->delete();
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
        $model=OphCiExaminationRiskSet::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

}