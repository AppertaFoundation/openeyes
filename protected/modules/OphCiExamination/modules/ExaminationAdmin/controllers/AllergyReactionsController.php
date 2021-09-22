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

class AllergyReactionsController extends \ModuleAdminController
{
    public function actionIndex()
    {
        $this->group = 'Examination';
        $asset_manager = Yii::app()->getAssetManager();
        $asset_manager->registerScriptFile('/js/oeadmin/OpenEyes.admin.js');
        $asset_manager->registerScriptFile('/js/oeadmin/list.js');

        $this->render('/AllergyReactions/index', [
          'model' => OphCiExaminationAllergyReaction::model(),
          'model_list' => OphCiExaminationAllergyReaction::model()->findAll(array('order' => 'display_order asc')),
        ]);
    }

    public function actionUpdate()
    {
        $request = Yii::app()->getRequest();
        $post = $request->getPost('OphCiExamination_AllergyReaction');
        $display_order = 1;
        $display_order_counter = 0;
        foreach ($post as $attributes) {
            $display_order_counter++;
            if (isset($attributes['id'])) {
                $allergy = OphCiExaminationAllergyReaction::model()->findByPk($attributes['id']);
                $model_action = 'edit';
                $flash_message = 'Allergy reactions updated';
            } else {
                $allergy = new OphCiExaminationAllergyReaction();
                $model_action = 'create';
                $flash_message = 'Allergy reactions created';
            }

            $attributes['display_order'] = $display_order_counter;
            $attributes['active'] = isset($attributes['active']);

            $allergy->setAttributes($attributes);
            if ($allergy->isModelDirty() && $allergy->save()) {
                Audit::add(
                    'admin',
                    $model_action,
                    serialize($allergy->attributes),
                    false,
                    ['model' => 'OEModule_OphCiExamination_models_OphCiExaminationAllergyReaction']
                );
                Yii::app()->user->setFlash('success', $flash_message);
            }
            $display_order++;
        }

        $this->redirect(['AllergyReactions/index']);
    }

    public function actionDelete($id)
    {
        $allergy_reaction = OphCiExaminationAllergyReaction::model()->findByPk($id);
        $allergy_reaction->delete();
        $this->redirect(['AllergyReactions/index']);
    }
}
