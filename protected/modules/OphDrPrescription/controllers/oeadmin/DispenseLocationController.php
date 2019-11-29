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
class DispenseLocationController extends \ModuleAdminController
{
    public $group = 'Prescription';

    public function actionIndex()
    {
        $dispense_locations_model = OphDrPrescription_DispenseLocation::model();
        $path = Yii::getPathOfAlias('application.widgets.js');
        $generic_admin = Yii::app()->assetManager->publish($path . '/GenericAdmin.js');
        Yii::app()->getClientScript()->registerScriptFile($generic_admin);

        if (Yii::app()->request->isPostRequest) {
            $dispense_locations = \Yii::app()->request->getPost(CHtml::modelName($dispense_locations_model), []);
            foreach ($dispense_locations as $dispense_location) {
                $model = $dispense_locations_model->findByPk($dispense_location['id']);
                $model->display_order = $dispense_location['display_order'];
                $model->save();
            }
        }
        $criteria = new \CDbCriteria();
        $criteria->order = 'display_order';
        $dispense_locations = $dispense_locations_model->findAll($criteria);

        $this->render(
            '/admin/dispense_location/index',
            [
                'dispense_locations' => $dispense_locations,
                'pagination' => $this->initPagination($dispense_locations_model),
            ]
        );
    }

    public function actionEdit($id = null)
    {
        if (!isset($id)) {
            $model = new OphDrPrescription_DispenseLocation();
        } else if (!$model = OphDrPrescription_DispenseLocation::model()->find('`id`=?', array($_GET['id']))) {
            $this->redirect(array('/OphDrPrescription/oeadmin/DispenseLocation'));
        }

        $errors = array();

        if (Yii::app()->request->isPostRequest) {
            $model->attributes = $_POST['OphDrPrescription_DispenseLocation'];
            if ($model->save()) {
                $this->redirect(array('/OphDrPrescription/oeadmin/DispenseLocation'));
            } else {
                $errors = $model->errors;
            }
        }

        $this->render('/admin/dispense_location/edit', array(
            'model' => $model,
            'errors' => $errors
        ));
    }

}
