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
        $path = Yii::getPathOfAlias('application.widgets.js');
        $assetManager = Yii::app()->getAssetManager();
        $assetManager->registerScriptFile('/js/oeadmin/OpenEyes.admin.js');
        $assetManager->registerScriptFile('/js/oeadmin/list.js');
        $generic_admin = $assetManager->publish($path . '/GenericAdmin.js');
        Yii::app()->getClientScript()->registerScriptFile($generic_admin);

        if (Yii::app()->request->isPostRequest) {
            $dispense_conditions = \Yii::app()->request->getPost(CHtml::modelName($dispense_conditions_model), []);
            foreach ($dispense_conditions as $dispense_condition) {
                $model = $dispense_conditions_model->findByPk($dispense_condition['id']);
                $model->display_order = $dispense_condition['display_order'];
                $model->save();
            }
        }
        $criteria = new \CDbCriteria();
        $criteria->order = 'display_order';
        $dispense_conditions = $dispense_conditions_model->findAll($criteria);

        $this->render(
            '/admin/dispense_condition/index',
            [
                'dispense_conditions' => $dispense_conditions
            ]
        );
    }

    public function actionEdit($id)
    {
        if (!$model = OphDrPrescription_DispenseCondition::model()->find('`id`=?', array($id))) {
            $this->redirect(array('/OphDrPrescription/admin/DispenseCondition/index'));
        }

        $errors = array();

        if (Yii::app()->request->isPostRequest) {
            $model->attributes = $_POST['OphDrPrescription_DispenseCondition'];
            $model->locations = isset($_POST['OphDrPrescription_DispenseCondition']['locations']) ? $_POST['OphDrPrescription_DispenseCondition']['locations'] : [];
            if ($model->save()) {
                $this->redirect(array('/OphDrPrescription/admin/DispenseCondition/index'));
            } else {
                $errors = $model->errors;
            }
        }

        $this->render('/admin/edit', array(
            'model' => $model,
            'errors' => $errors,
            'title' => 'Edit dispense condition'
        ));
    }

    public function actionCreate()
    {
        $model = new OphDrPrescription_DispenseCondition();

        $errors = array();

        if (Yii::app()->request->isPostRequest) {
            $model->attributes = $_POST['OphDrPrescription_DispenseCondition'];
            $model->display_order = $this->getNextHighestDisplayOrder();
            $model->locations = isset($_POST['OphDrPrescription_DispenseCondition']['locations']) ? $_POST['OphDrPrescription_DispenseCondition']['locations'] : [];
            if ($model->save()) {
                $this->redirect(array('/OphDrPrescription/admin/DispenseCondition/index'));
            } else {
                $errors = $model->errors;
            }
        }

        $this->render('/admin/edit', array(
            'model' => $model,
            'errors' => $errors,
            'title' => 'Create dispense condition'
        ));
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

    private function getNextHighestDisplayOrder()
    {
        $query = 'SELECT MAX(display_order) AS maxdisplay FROM '.OphDrPrescription_DispenseCondition::model()->tableName();
        $val = Yii::app()->db->createCommand($query)->queryRow();

        return $val['maxdisplay'] + 1;
    }

}
