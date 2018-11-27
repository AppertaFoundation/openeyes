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

use OEModule\OphCiExamination\models\OphCiExaminationSystemicDiagnosesSet;

class SystemicDiagAssignmentController extends \ExaminationAdminController
{
    public $group = 'Examination';
    public $entry_model_name = 'OEModule\OphCiExamination\models\OphCiExaminationSystemicDiagnosesSetEntry';
    public $set_model_name = 'OEModule\OphCiExamination\models\OphCiExaminationSystemicDiagnosesSet';

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

        $diagnoses_set = new OphCiExaminationSystemicDiagnosesSet();
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
        $errors = false;
        $model = new OphCiExaminationSystemicDiagnosesSet();

        if(\Yii::app()->request->isPostRequest) {
            $errors = $this->populateAndSaveModel($model);
        }

        $this->render('/admin/systemicdiagnosesassignment/edit',array(
            'model' => $model,
            'errors' => $errors,
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
        $errors = false;
        $model = $this->loadModel($id);

        if(\Yii::app()->request->isPostRequest) {
            $errors = $this->populateAndSaveModel($model);
        }

        $this->render('/admin/systemicdiagnosesassignment/edit', array(
            'errors' => isset($errors) ? $errors : '',
            'model' => $model,
            'title' => 'Edit required risk set',
        ));
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
        $model = OphCiExaminationSystemicDiagnosesSet::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }
}