<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCiExamination\models\HistoryMedicationsStopReason;


class MedicationStopReasonController extends \ModuleAdminController
{
    public $reasons_that_cannot_be_edited = ['Medication parameters changed'];
    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $this->group = 'Examination';
        $asset_manager = Yii::app()->getAssetManager();
        $asset_manager->registerScriptFile('/js/oeadmin/OpenEyes.admin.js');
        $asset_manager->registerScriptFile('/js/oeadmin/list.js');
        $this->render('/medicationstopreason/index', [
            'model' => HistoryMedicationsStopReason::model(),
            'model_list' => HistoryMedicationsStopReason::model()->findAll(['order' => 'display_order']),
        ]);
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new HistoryMedicationsStopReason();
        $request = Yii::app()->getRequest();
        if ($request->getPost('OEModule_OphCiExamination_models_HistoryMedicationsStopReason')) {
            $model->attributes = $request->getPost('OEModule_OphCiExamination_models_HistoryMedicationsStopReason');
            if ($model->save()) {
                Audit::add('admin', 'create', serialize($model->attributes), false,
                    ['model' => 'OEModule_OphCiExamination_models_HistoryMedicationsStopReason']);
                Yii::app()->user->setFlash('success', 'Medication Stop Reason created');
                $this->redirect(['index']);
            } else {
                $errors = $model->getErrors();
            }
        }
        $this->render('/medicationstopreason/edit', [
            'model' => $model,
            'errors' => isset($errors) ? $errors : null,
            'is_new' => true,
        ]);
    }

    public function actionUpdate()
    {
        $request = Yii::app()->getRequest();
        $model = HistoryMedicationsStopReason::model()->findByPk((int)$request->getParam('id'));
        if (!$model) {
            throw new Exception('HistoryMedicationsStopReason not found with id ' . $request->getParam('id'));
        }
        if ($request->getPost('OEModule_OphCiExamination_models_HistoryMedicationsStopReason')) {
            $model->attributes = $request->getPost('OEModule_OphCiExamination_models_HistoryMedicationsStopReason');
            if ($model->save()) {
                Yii::app()->user->setFlash('success', 'Medication Stop Reason saved');
                $this->redirect(['index']);
            } else {
                $errors = $model->getErrors();
            }
        }
        $this->render('/medicationstopreason/edit', [
            'model' => $model,
            'errors' => isset($errors) ? $errors : null,
            'is_new' => false,
        ]);
    }

}