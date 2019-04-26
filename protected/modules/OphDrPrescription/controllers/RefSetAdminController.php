<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class RefSetAdminController extends BaseAdminController
{
	public $group = 'Drugs';

    public function actionList()
    {
        $admin = new Admin(MedicationSet::model(), $this);
        $admin->setListFields(array(
            'id',
            'name',
            'rulesString',
            'itemsCount',
            'hiddenString',
            'adminListAction'
        ));

        $admin->getSearch()->addSearchItem('name');
        $admin->getSearch()->setItemsPerPage(30);
        $admin->getSearch()->getCriteria()->order = 'name ASC';

        $admin->setListFieldsAction('edit');


        $admin->setModelDisplayName("Medication sets");
        $admin->listModel();
    }

    public function actionToList($id)
    {
        $this->redirect('/OphDrPrescription/refMedicationAdmin/list?ref_set_id='.$id);
    }

    public function actionEdit($id = null, $usage_code = null)
    {

        $admin = new Admin(MedicationSet::model(), $this);
        $admin->setModelDisplayName("Medication set");

        if($id) {
            $medicationSet = MedicationSet::model()->findByAttributes(['id' => $id]);

            if ($medicationSet->automatic != 1) {
                $admin->setEditFields(array(
                    'name' => 'Name',
                    'rules' => array(
                        'widget' => 'CustomView',
                        'viewName' => 'application.modules.OphDrPrescription.views.admin.medication_set.edit_rules',
                        'viewArguments' => array(
                            'medication_set' => !is_null($id) ? MedicationSet::model()->findByPk($id) : new MedicationSet(),
                            'usage_code' => !empty($usage_code) ? $usage_code : ''
                        )
                    ),
                    'sets' => array(
                        'widget' => 'CustomView',
                        'viewName' => 'application.modules.OphDrPrescription.views.admin.common_ophthalmic_drug_sets.edit_sets',
                        'viewArguments' => array(
                            'id' => $id
                        )
                    ),
                ));
                $admin->setModelDisplayName("Medication automatic set");
            } else {
                $admin->setEditFields(array(
                    'name' => 'Name',
                    'rules' => array(
                        'widget' => 'CustomView',
                        'viewName' => 'application.modules.OphDrPrescription.views.admin.medication_set.edit_rules',
                        'viewArguments' => array(
                            'medication_set' => !is_null($id) ? MedicationSet::model()->findByPk($id) : new MedicationSet(),
                            'usage_code' => !empty($usage_code) ? $usage_code : ''
                        )
                    ),
                ));
            }

            $admin->setModelId($id);
        } else {
            $admin->setEditFields(array(
                'name' => 'Name',
                'rules' => array(
                    'widget' => 'CustomView',
                    'viewName' => 'application.modules.OphDrPrescription.views.admin.medication_set.edit_rules',
                    'viewArguments' => array(
                        'medication_set' => !is_null($id) ? MedicationSet::model()->findByPk($id) : new MedicationSet(),
                        'usage_code' => !empty($usage_code) ? $usage_code : ''
                    )
                ),
                'sets' => array(
                    'widget' => 'CustomView',
                    'viewName' => 'application.modules.OphDrPrescription.views.admin.common_ophthalmic_drug_sets.edit_sets',
                    'viewArguments' => array(
                        'id' => $id
                    )
                ),
            ));


        }

        if (!empty($usage_code)) {
            $admin->setCustomSaveURL('/OphDrPrescription/refSetAdmin/save/'.$id.'?usage_code='.$usage_code);
        } else {
            $admin->setCustomSaveURL('/OphDrPrescription/refSetAdmin/save/'.$id);
        }

        $admin->editModel();
    }

    public function actionSave($id = null, $usage_code = null)
    {

        if(is_null($id)) {
            $model = new MedicationSet();
        }
        else {
            if(!$model = MedicationSet::model()->findByPk($id)) {
                throw new CHttpException(404, 'Page not found');
            }
        }

        /** @var MedicationSet $model */

        $data = Yii::app()->request->getPost('MedicationSet');
        $existing_item_ids = array();
        foreach ($model->medicationSetItems as $item) {
            $existing_item_ids[] = $item->id;
        }

        $this->_setModelData($model, $data);
        $model->save();

        $existing_ids = array();
        $updated_ids = array();
        foreach ($model->medicationSetRules as $rule) {
            $existing_ids[] = $rule->id;
        }


        $ids = @Yii::app()->request->getPost('MedicationSet')['medicationSetRules']['id'];

        if(is_array($ids)) {
            foreach ($ids as $key => $rid) {
                if($rid == -1) {
                    $medSetRule = new MedicationSetRule();
                }
                else {
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
        if(!empty($deleted_ids)) {
            MedicationSetRule::model()->deleteByPk($deleted_ids);
        }


        $updated_item_ids = array();
        foreach ($model->medicationSetItems as $item) {
            $item->medication_set_id = $model->id;
            $item->save();
        }

        $itemids = @Yii::app()->request->getPost('MedicationSet')['medicationSetItems']['id'];
        if(is_array($itemids)) {
            foreach ($itemids as $key => $rid) {

                if($rid == -1) {
                    $medSetItem = new MedicationSetItem();
                }
                else {
                    $medSetItem = MedicationSetItem::model()->findByPk($rid);
                    $updated_item_ids[] = $rid;
                }
            }
        }

        $deleted_item_ids = array_diff($existing_item_ids, $updated_item_ids);
        if(!empty($deleted_item_ids)) {
            MedicationSetItem::model()->deleteByPk($deleted_item_ids);
        }


        if (empty($usage_code)) {
            $this->redirect('/OphDrPrescription/refSetAdmin/list');
        } else {
            if ($usage_code == 'COMMON_SYSTEMIC') {
                $this->redirect('/OphDrPrescription/commonSystemicDrugSetsAdmin/list');
            } else if ($usage_code == 'COMMON_OPH') {
                $this->redirect('/OphDrPrescription/commonOphthalmicDrugSetsAdmin/list');
            }

        }

    }

    private function _setModelData(MedicationSet $model, $data)
    {
        $model->setAttributes($data);
        $model->validate();

        $medicationSetItems = array();
        if(array_key_exists('medicationSetItems', $data)) {

            foreach ($data['medicationSetItems']['id'] as $key => $medicationSetItem_id) {
                $attributes = array();

                foreach (MedicationSetItem::model()->attributeNames() as $attr_name) {
                    if(array_key_exists($attr_name, $data['medicationSetItems'])) {
                        $attributes[$attr_name] = array_key_exists($key, $data['medicationSetItems'][$attr_name]) ? $data['medicationSetItems'][$attr_name][$key] : null;
                    }
                }

                if($medicationSetItem_id == -1) {
                    $medicationSetItem = new MedicationSetItem();
                }
                else {
                    $medicationSetItem = MedicationSetItem::model()->findByPk($medicationSetItem_id);
                }

                $medicationSetItem->setAttributes($attributes);
                $medicationSetItem->medication_set_id = $model->id;

                if(!$medicationSetItem->validate(array('medication_id', 'default_form_id', 'default_route_id', 'default_frequency_id', 'default_duration_id'))) {
                    $model->addErrors($medicationSetItem->getErrors());
                }

                $medicationSetItems[] = $medicationSetItem;

            }
        }


        $model->medicationSetItems = $medicationSetItems;
    }

    public function actionDelete()
    {
        $ids_to_delete = Yii::app()->request->getPost('MedicationSet')['id'];

        if(is_array($ids_to_delete)) {
            foreach ($ids_to_delete as $id) {
                $model = MedicationSet::model()->findByPk($id);
                /** @var MedicationSet $model */
                foreach ($model->medicationSetRules as $rule) {
                    $rule->delete();
                }
                foreach ($model->items as $i) {
                    $i->delete();
                }
                $model->delete();
            }
        }

        exit("1");
    }
}