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
class DispenseLocationController extends BaseAdminController
{
    public $group = 'Prescription';

    public function actionIndex()
    {
        $dispense_locations_model = OphDrPrescription_DispenseLocation::model();
        $assetManager = Yii::app()->getAssetManager();
        $assetManager->registerScriptFile('//js/oeadmin/OpenEyes.admin.js');
        $assetManager->registerScriptFile('//js/oeadmin/list.js');

        $this->render(
            '/admin/dispense_location/index',
            [
                'dispense_locations' => $dispense_locations_model->findAll()
            ]
        );
    }

    public function actionEdit($id)
    {
        if (!$model = OphDrPrescription_DispenseLocation::model()->findByPk($id)) {
            $this->redirect(['/OphDrPrescription/admin/DispenseLocation/index']);
        }

        $model_saved = $this->saveModel($model);
        if ($model_saved) {
            $this->redirect(['/OphDrPrescription/admin/DispenseLocation/index']);
        }

        $this->render('/admin/edit', [
            'model' => $model,
            'errors' => $model->errors,
            'title' => 'Edit dispense location'
        ]);
    }

    public function actionCreate()
    {
        $model = new OphDrPrescription_DispenseLocation();
        $model_saved = $this->saveModel($model);
        if ($model_saved) {
            $this->redirect(['/OphDrPrescription/admin/DispenseLocation/index']);
        }

        $this->render('/admin/edit', [
            'model' => $model,
            'errors' => $model->errors,
            'title' => 'Create dispense location'
        ]);
    }

    private function saveModel($model)
    {
        if (Yii::app()->request->isPostRequest) {
            $model->attributes = $_POST['OphDrPrescription_DispenseLocation'];
            $model->display_order =  isset($model->id) ? $model->display_order : $model->getNextHighestDisplayOrder(1);

            return $model->save();
        }

        return false;
    }

    public function actions() {
        return [
            'sortLocations' => [
                'class' => 'SaveDisplayOrderAction',
                'model' => OphDrPrescription_DispenseLocation::model(),
                'modelName' => 'OphDrPrescription_DispenseLocation',
            ],
        ];
    }

}
