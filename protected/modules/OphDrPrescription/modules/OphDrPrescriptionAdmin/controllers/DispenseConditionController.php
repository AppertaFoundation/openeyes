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

    public function actionAddMapping()
    {
        $model = $_POST['model']::model();

        $ids = Yii::app()->request->getPost('select');

        $transaction = Yii::app()->db->beginTransaction();
        $errors = array();
        $records = $model->findAllByPk($ids);
        try {
            foreach ($records as $record) {
                $record->createMapping(ReferenceData::LEVEL_INSTITUTION, $model->getIdForLevel(ReferenceData::LEVEL_INSTITUTION));
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }

        if (!empty($errors)) {
            $transaction->rollback();
        } else {
            $transaction->commit();
        }
        $this->redirect(['/OphDrPrescription/admin/DispenseCondition/index']);
    }

    public function actionRemoveMapping()
    {
        $model = $_POST['model']::model();
        $level = ReferenceData::LEVEL_INSTITUTION;

        $ids = Yii::app()->request->getPost('select');
        $transaction = Yii::app()->db->beginTransaction();
        $errors = array();
        $records = $model->findAllByPk($ids);
        try {
            foreach ($records as $record) {
                $record->deleteMapping($level, $model->getIdForLevel($level));
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }

        if (!empty($errors)) {
            $transaction->rollback();
        } else {
            $transaction->commit();
        }
        $this->redirect(['/OphDrPrescription/admin/DispenseCondition/index']);
    }

    private function saveModel($model)
    {
        if (!Yii::app()->request->isPostRequest) {
            return false;
        }
        $institution_id = Yii::app()->session['selected_institution_id'];
        $model->attributes = Yii::app()->request->getParam('OphDrPrescription_DispenseCondition', array());
        $dci_data = Yii::app()->request->getParam('OphDrPrescription_DispenseCondition_Institution', array());
        $dci_associated_dli_ids = array_key_exists('dispense_location_institutions', $dci_data) ? $dci_data['dispense_location_institutions'] : array();
        $errors = array();
        $transaction = \Yii::app()->db->beginTransaction();
        foreach ($model->dispense_condition_institutions as $dci) {
            if (intval($dci->institution_id) !== intval($institution_id)) {
                continue;
            }
            $temp_dlis = array_map(function ($dci_associated_dli_id) {
                $dli = \OphDrPrescription_DispenseLocation_Institution::model()->findByPk($dci_associated_dli_id);
                return $dli;
            }, $dci_associated_dli_ids);
            $dci->dispense_location_institutions = $temp_dlis;
            $dci->save();
            $errors = array_merge($dci->getErrors(), $errors);
        }
        $model->display_order =  isset($model->id) ? $model->display_order : $model->getNextHighestDisplayOrder(1);
        $model->save();
        $errors = array_merge($model->getErrors(), $errors);
        if ($errors) {
            $transaction->rollback();
            return false;
        }
        $transaction->commit();
        return true;
    }

    public function actions()
    {
        return [
            'sortConditions' => [
                'class' => 'SaveDisplayOrderAction',
                'model' => OphDrPrescription_DispenseCondition::model(),
                'modelName' => 'OphDrPrescription_DispenseCondition',
            ],
        ];
    }
}
