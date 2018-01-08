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


use OEModule\OphCiExamination\models\HistoryRisks;
use OEModule\OphCiExamination\models\OphCiExaminationRisk;

class RisksAssignmentController extends \ModuleAdminController
{
    public function accessRules()
    {
        return array(
            array('allow', 'users' => array('@')),
        );
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

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['OEModule_OphCiExamination_models_OphCiExaminationRisk']))
        {
            $model->attributes=$_POST['OEModule_OphCiExamination_models_OphCiExaminationRisk'];
            if($model->save())
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
    public function actionDelete($id)
    {
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {

        $model= new OphCiExaminationRisk('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['OphCiExaminationRisk']))
            $model->attributes=$_GET['OphCiExaminationRisk'];

        $this->render('/admin/riskassignment/index',array(
            'model' => $model,
        ));
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
        $model=OphCiExaminationRisk::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param OphCiExaminationRisk $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='oph-ci-examination-risk-form')
        {
            echo CActiveForm::validate($model);
            \Yii::app()->end();
        }
    }

}