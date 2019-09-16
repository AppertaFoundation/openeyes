<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use \OEModule\OphCiExamination\models\OphCiExaminationAllergy;

class AllergiesController extends \ModuleAdminController
{
    public function actionIndex()
    {
        $this->group = 'Examination';
        $asset_manager = Yii::app()->getAssetManager();
        $asset_manager->registerScriptFile('/js/oeadmin/OpenEyes.admin.js');
        $asset_manager->registerScriptFile('/js/oeadmin/list.js');

        $this->render('/Allergies/index', [
          'model' => OphCiExaminationAllergy::model(),
          'model_list' => OphCiExaminationAllergy::model()->findAll(),
          'medication_set_list_options' => \MedicationSet::model()->findAll('id NOT IN (SELECT id FROM openeyes.medication_set_rule)'),
        ]);
    }

    public function actions() {
        return [
          'sortAllergies' => [
            'class' => 'SaveDisplayOrderAction',
            'model' => OphCiExaminationAllergy::model(),
            'modelName' => 'OphCiExamination_Allergy',
          ],
        ];
    }

    /**
     * Updates the selected Model
     */
    public function actionUpdate()
    {
        $request = Yii::app()->getRequest();
        $id = $request->getParam('id');
        $model = OphCiExaminationAllergy::model()->findByPk($id);

        if (!$model) {
            $this->redirect(['dilationDrugs']);
        }

        $new_attributes = $request->getPost('OEModule_OphCiExamination_models_OphCiExaminationAllergy');
        if ($new_attributes) {
            $model->setAttributes($new_attributes);

            if ($model->save()) {
                Audit::add('admin', 'edit', serialize($model->attributes), false,
                ['model' => 'OEModule_OphCiExamination_models_OphCiExaminationAllergy']);
                Yii::app()->user->setFlash('success', 'Drop edited');
                $this->redirect(['dilationDrugs']);
            }
        }
        $this->render('/Drug/edit', [
          'model' => $model,
          'errors' => $model->getErrors(),
        ]);
    }

    /**
    * Deletes the selected models
    */
    public function actionDelete()
    {

        $delete_ids = Yii::app()->request->getPost('select', []);
        $transaction = Yii::app()->db->beginTransaction();
        $success = true;
        try {
            foreach ($delete_ids as $drug_id) {
                $drug = OphCiExaminationAllergy::model()->findByPk($drug_id);
                if ($drug) {
                    if (!$drug->delete()) {
                        $success = false;
                        break;
                    } else {
                        Audit::add('admin-dilation-drugs', 'delete', serialize($drug));
                    }
                }
            }
        } catch (Exception $e) {
            \OELog::log($e->getMessage());
            $success = false;
        }

        if ($success) {
            $transaction->commit();
            echo '1';
        } else {
            $transaction->rollback();
            echo '0';
        }
    }

    /**
    * Creates a new model.
    * If creation is successful, the browser will be redirected to the 'view' page.
    */
    public function actionCreate()
    {
        $model = new OphCiExaminationAllergy();
        $request = Yii::app()->getRequest();
        $new_attributes = $request->getPost('OEModule_OphCiExamination_models_OphCiExaminationAllergy');
        if ($new_attributes) {
            $model->setAttributes($new_attributes);

            if ($model->save()) {
                Audit::add('admin', 'create', serialize($model->attributes), false,
                ['model' => 'OEModule_OphCiExamination_models_OphCiExaminationAllergy']);
                Yii::app()->user->setFlash('success', 'Drop created');
                $this->redirect(['dilationDrugs']);
            }
        }
        $this->render('/Drug/edit', [
          'model' => $model,
          'errors' => $model->getErrors(),
        ]);
    }
}
