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
class DispenseConditionController extends BaseAdminController
{
    public $group = 'Prescription';

    public function actionIndex()
    {
        $dispense_conditions_model = OphDrPrescription_DispenseCondition::model();
        $assetManager = Yii::app()->getAssetManager();
        $assetManager->registerScriptFile('/js/oeadmin/OpenEyes.admin.js');
        $assetManager->registerScriptFile('/js/oeadmin/list.js');

        $this->render(
            '/admin/dispense_condition/index',
            [
                'dispense_conditions' => $dispense_conditions_model->findAll()
            ]
        );
    }

    public function actionEdit($id)
    {
        if (!$model = OphDrPrescription_DispenseCondition::model()->findByPk($id)) {
            $this->redirect(['/OphDrPrescription/admin/DispenseCondition/index']);
        }

        $model_saved = $this->saveModel($model);
        if ($model_saved) {
            $this->redirect(['/OphDrPrescription/admin/DispenseCondition/index']);
        }

        $this->render('/admin/edit', [
            'model' => $model,
            'errors' => $model->errors,
            'title' => 'Edit dispense condition'
        ]);

    }

    public function actionCreate()
    {
        $model = new OphDrPrescription_DispenseCondition();
        $model_saved = $this->saveModel($model);
        if ($model_saved) {
            $this->redirect(['/OphDrPrescription/admin/DispenseCondition/index']);
        }

        $this->render('/admin/edit', [
            'model' => $model,
            'errors' => $model->errors,
            'title' => 'Create dispense condition'
        ]);
    }

    private function saveModel($model)
    {
        if (Yii::app()->request->isPostRequest) {
            $model->attributes = $_POST['OphDrPrescription_DispenseCondition'];
            $model->all_locations = isset($_POST['OphDrPrescription_DispenseCondition']['all_locations']) ? $_POST['OphDrPrescription_DispenseCondition']['all_locations'] : [];
            $model->display_order =  isset($model->id) ? $model->display_order : $model->getNextHighestDisplayOrder(1);

            return $model->save();
        }

        return false;
    }

    public function actions() {
        return [
            'sortConditions' => [
                'class' => 'SaveDisplayOrderAction',
                'model' => OphDrPrescription_DispenseCondition::model(),
                'modelName' => 'OphDrPrescription_DispenseCondition',
            ],
        ];
    }

}
