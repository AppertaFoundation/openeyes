<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class CommonPrescriptionDrugSetsAdminController extends BaseDrugSetsAdminController {

    public $group = 'Drugs';
    public $usage_code = 'PRESCRIPTION_SET';
    public $modelDisplayName = 'Common Prescription Drug Sets';
    public $editSetTemaplate = 'application.modules.OphDrPrescription.views.admin.common_prescription_drug_sets.edit_sets';


    public function actionSave($id = null) {

        if (is_null($id)) {
            $model = new MedicationSet();
        } else {
            if (!$model = MedicationSet::model()->findByPk($id)) {
                throw new CHttpException(404, 'Page not found');
            }
        }

        /** @var MedicationSet $model */

        $data = Yii::app()->request->getPost('MedicationSet');

        $existing_item_ids = array();
        $existing_taper_ids = array();
        foreach ($model->medicationSetItems as $item) {
            $existing_item_ids[] = $item->id;
            foreach ($item->tapers as $taper) {
                $existing_taper_ids[] = $taper->id;
            }
        }

        $this->_setModelData($model, $data);
        if(!$model->validate()) {
            $response['errors'] = $model->errors;
            echo json_encode($response);
            exit;
        } else {
            $model->save();
        }


        $existing_ids = array();
        $updated_ids = array();
        foreach ($model->medicationSetRules as $rule) {
            $existing_ids[] = $rule->id;
        }


        $ids = @Yii::app()->request->getPost('MedicationSet')['medicationSetRules']['id'];

        if (is_array($ids)) {
            foreach ($ids as $key => $rid) {
                if ($rid == -1) {
                    $medSetRule = new MedicationSetRule();
                } else {
                    $medSetRule = MedicationSetRule::model()->findByPk($rid);
                    $updated_ids[] = $rid;
                }

                $medSetRule->setAttributes(array(
                    'medication_set_id' => $model->id,
                    'site_id' => Yii::app()->request->getPost('MedicationSet')['medicationSetRules']['site_id'][$key],
                    'subspecialty_id' => Yii::app()->request->getPost('MedicationSet')['medicationSetRules']['subspecialty_id'][$key],
                    'usage_code' => Yii::app()->request->getPost('MedicationSet')['medicationSetRules']['usage_code'][$key],
                ));

                $medSetRule->save();
            }
        }

        $deleted_ids = array_diff($existing_ids, $updated_ids);
        if (!empty($deleted_ids)) {
            MedicationSetRule::model()->deleteByPk($deleted_ids);
        }


        $updated_item_ids = array();
        foreach ($model->medicationSetItems as $item) {
            $item->medication_set_id = $model->id;
            $item->save();
        }

        $itemids = @Yii::app()->request->getPost('MedicationSet')['medicationSetItems']['id'];
        if (is_array($itemids)) {
            foreach ($itemids as $key => $rid) {

                if ($rid == -1) {
                    $medSetItem = new MedicationSetItem();
                } else {
                    $medSetItem = MedicationSetItem::model()->findByPk($rid);
                    $updated_item_ids[] = $rid;
                }
            }
        }

        $updated_taper_ids = array();
        $taperDatas = @Yii::app()->request->getPost('MedicationSet')['medicationSetItems']['medicationSetItemTapers'];

        if (is_array($taperDatas)) {

            foreach ($taperDatas as $key => $tapers) {
                foreach ($tapers as $taperKey => $taper) {
                    $index = $key-1;
                    if ($taper['id'] == -1) {
                        $medSetItemTaper = new MedicationSetItemTaper();
                        $medSetItemTaper->medication_set_item_id = $model->medicationSetItems[$index]->id;
                        $medSetItemTaper->frequency_id = $taper['default_frequency_id'];
                        $medSetItemTaper->duration_id = $taper['default_duration_id'];
                        $medSetItemTaper->save();
                    } else {
                        $medSetItemTaper = MedicationSetItemTaper::model()->findByPk($taper['id']);
                        $medSetItemTaper->frequency_id = $taper['default_frequency_id'];
                        $medSetItemTaper->duration_id = $taper['default_duration_id'];
                        $medSetItemTaper->update();
                        $updated_taper_ids[] = $taper['id'];
                    }
                }
            }
        }


        $deleted_taper_ids = array_diff($existing_taper_ids, $updated_taper_ids);
        if (!empty($deleted_taper_ids)) {
            MedicationSetItemTaper::model()->deleteByPk($deleted_taper_ids);
        }


        $deleted_item_ids = array_diff($existing_item_ids, $updated_item_ids);
        if (!empty($deleted_item_ids)) {
            MedicationSetItem::model()->deleteByPk($deleted_item_ids);
        }


        $this->redirect('/OphDrPrescription/' . Yii::app()->controller->id . '/list');

    }

    private function _setModelData(MedicationSet $model, $data) {
        $model->setAttributes($data);
        $model->validate();

        $medicationSetItems = array();
        if (array_key_exists('medicationSetItems', $data)) {

            foreach ($data['medicationSetItems']['id'] as $key => $medicationSetItem_id) {
                $attributes = array();

                foreach (MedicationSetItem::model()->attributeNames() as $attr_name) {
                    if (array_key_exists($attr_name, $data['medicationSetItems'])) {
                        $attributes[$attr_name] = array_key_exists($key, $data['medicationSetItems'][$attr_name]) ? $data['medicationSetItems'][$attr_name][$key] : null;
                    }
                }

                if ($medicationSetItem_id == -1) {
                    $medicationSetItem = new MedicationSetItem();
                } else {
                    $medicationSetItem = MedicationSetItem::model()->findByPk($medicationSetItem_id);
                }

                $medicationSetItem->setAttributes($attributes);
                $medicationSetItem->medication_set_id = $model->id;

                if (!$medicationSetItem->validate(array('medication_id', 'default_form_id', 'default_route_id', 'default_frequency_id', 'default_duration_id'))) {
                    $model->addErrors($medicationSetItem->getErrors());
                }

                $medicationSetItems[] = $medicationSetItem;

            }
        }


        $model->medicationSetItems = $medicationSetItems;
    }


}