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

    public function actionUpdate()
    {
        $request = Yii::app()->getRequest();
        $post = $request->getPost('OphCiExamination_Allergy');
        $display_order = 1;
        foreach ($post as $attributes) {
            if (isset($attributes['id'])) {
                $attributes['display_order'] = (string)$display_order; // Changing display_order type to string so that isModelDirty() doesn't pick it up as a new change
                $attributes['medication_set_id'] = ($attributes['medication_set_id'] === '0' ? NULL : $attributes['medication_set_id']);
                $attributes['active'] = isset($attributes['active']);
                $allergy = OphCiExaminationAllergy::model()->findByPk($attributes['id']);
                if ($allergy) {
                    $allergy->setAttributes($attributes);
                    if ($allergy->isModelDirty() && $allergy->save()) {
                        Audit::add('admin', 'edit', serialize($allergy->attributes), false,
                        ['model' => 'OEModule_OphCiExamination_models_OphCiExaminationAllergy']);
                        Yii::app()->user->setFlash('success', 'Allergies updated');
                    }
                } else {
                    $this->createAllergy($attributes);
                }
                $display_order++;
            }
        }
        $this->redirect(['Allergies/index']);
    }

    private function createAllergy($new_attributes)
    {
        $model = new OphCiExaminationAllergy();
        if ($new_attributes['id'] === 'new') {
            unset($new_attributes['id']);
            $model->setAttributes($new_attributes);

            if ($model->save()) {
                Audit::add('admin', 'create', serialize($model->attributes), false,
                ['model' => 'OEModule_OphCiExamination_models_OphCiExaminationAllergy']);
                Yii::app()->user->setFlash('success', 'Allergy created');
            }
        }
    }
}
