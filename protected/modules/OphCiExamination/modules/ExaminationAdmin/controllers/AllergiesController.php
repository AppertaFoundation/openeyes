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
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('index', 'update'),
                'roles' => array('admin'),
            ),
        );
    }

    public function actionIndex()
    {
        $this->group = 'Examination';
        $asset_manager = Yii::app()->getAssetManager();
        $asset_manager->registerScriptFile('/js/oeadmin/OpenEyes.admin.js');
        $asset_manager->registerScriptFile('/js/oeadmin/list.js');

        $this->render('/Allergies/index', [
          'model' => OphCiExaminationAllergy::model(),
          'model_list' => OphCiExaminationAllergy::model()->findAll(),
          'medication_set_list_options' => \MedicationSet::model()->findAll('id NOT IN (SELECT medication_set_id FROM openeyes.medication_set_rule)'),
        ]);
    }

    public function actionUpdate()
    {
        $request = Yii::app()->getRequest();
        $post = $request->getPost('OphCiExamination_Allergy');
        $display_order = 1;
        foreach ($post as $attributes) {
            if (isset($attributes['id'])) {
                $allergy = OphCiExaminationAllergy::model()->findByPk($attributes['id']);
                $model_action = 'edit';
                $flash_message = 'Allergies updated';
            } else {
                $allergy = new OphCiExaminationAllergy();
                $model_action = 'create';
                $flash_message = 'Allergy created';
            }

            $attributes['display_order'] = (string)$display_order; // Changing display_order type to string so that isModelDirty() doesn't pick it up as a new change
            $attributes['medication_set_id'] = ($attributes['medication_set_id'] === '' ? NULL : $attributes['medication_set_id']);
            $attributes['active'] = isset($attributes['active']);

            $allergy->setAttributes($attributes);
            if ($allergy->isModelDirty() && $allergy->save()) {
                Audit::add('admin', $model_action, serialize($allergy->attributes), false,
                ['model' => 'OEModule_OphCiExamination_models_OphCiExaminationAllergy']);
                Yii::app()->user->setFlash('success', $flash_message);
            }
            $display_order++;
        }
        $this->redirect(['Allergies/index']);
    }
}
