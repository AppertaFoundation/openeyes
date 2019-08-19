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
use OEModule\OphCiExamination\models\OphCiExamination_PupillaryAbnormalities_Abnormality;

class PupillaryAbnormalitiesController extends \ModuleAdminController
{
    public function actions() {
        return [
            'sortPupillaryAbnormalities' => [
                'class' => 'SaveDisplayOrderAction',
                'model' => OphCiExamination_PupillaryAbnormalities_Abnormality::model(),
                'modelName' => 'OphCiExamination_PupillaryAbnormalities_Abnormality',
            ],
        ];
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $this->group = 'Examination';
        $asset_manager = Yii::app()->getAssetManager();
        $asset_manager->registerScriptFile('/js/oeadmin/OpenEyes.admin.js');
        $asset_manager->registerScriptFile('/js/oeadmin/list.js');

        $this->render('/pupillaryabnormalities/index', [
            'model' => OphCiExamination_PupillaryAbnormalities_Abnormality::model(),
            'model_list' => OphCiExamination_PupillaryAbnormalities_Abnormality::model()->findAll(['order' => 'display_order']),
        ]);
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @throws Exception
     */
    public function actionUpdate()
    {
        $request = Yii::app()->getRequest();
        $model = OphCiExamination_PupillaryAbnormalities_Abnormality::model()->findByPk((int)$request->getParam('id'));
        if (!$model) {
            throw new Exception('OphCiExamination_PupillaryAbnormalities_Abnormality not found with id ' . $request->getParam('id'));
        }
        if ($request->getPost('OphCiExamination_PupillaryAbnormalities_Abnormality')) {
            $model->attributes = $request->getPost('OphCiExamination_PupillaryAbnormalities_Abnormality');
            if ($model->save()) {
                Yii::app()->user->setFlash('success', 'Pupillary Abnormality saved');
                $this->redirect(['index']);
            } else {
                $errors = $model->getErrors();
            }
        }

        $this->render('/pupillaryabnormalities/edit', [
            'model' => $model,
            'errors' => isset($errors) ? $errors : null,
        ]);
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new OphCiExamination_PupillaryAbnormalities_Abnormality();
        $request = Yii::app()->getRequest();
        if ($request->getPost('OEModule_OphCiExamination_models_OphCiExamination_PupillaryAbnormalities_Abnormality')) {
            $model->attributes = $request->getPost('OEModule_OphCiExamination_models_OphCiExamination_PupillaryAbnormalities_Abnormality');

            if ($model->save()) {
                Audit::add('admin', 'create', serialize($model->attributes), false,
                    ['model' => 'OEModule_OphCiExamination_models_OphCiExamination_PupillaryAbnormalities_Abnormality']);
                Yii::app()->user->setFlash('success', 'Pupillary Abnormality created');
                $this->redirect(['index']);
            } else {
                $errors = $model->getErrors();
            }
        }
        $this->render('/pupillaryabnormalities/edit', [
            'model' => $model,
            'errors' => isset($errors) ? $errors : null,
        ]);
    }

    /**
     * Deletes the selected models
     */
    public function actionDelete()
    {
        $delete_ids = isset($_POST['select']) ? $_POST['select'] : [];
        $transaction = Yii::app()->db->beginTransaction();
        $success = true;
        $result = [];
        $result['status'] = 1;
        $result['errors'] = "";
        try {
            foreach ($delete_ids as $abnormality_id) {
                $abnormality = OphCiExamination_PupillaryAbnormalities_Abnormality::model()->findByPk($abnormality_id);
                if ($abnormality) {
                    if (!$abnormality->delete()) {
                        $success = false;
                        $result['status'] = 0;
                        $result['errors'][]= $abnormality->getErrors();
                        break;
                    } else {
                        Audit::add('admin-pupillary-abnormality', 'delete', $abnormality);
                    }
                }
            }
        } catch (Exception $e) {
            \OELog::log($e->getMessage());
            $result['status'] = 0;
            $result['errors'][]= $e->getMessage();
            $success = false;
        }

        if ($success) {
            $transaction->commit();
        } else {
            $transaction->rollback();
        }

        echo json_encode($result);
    }
}