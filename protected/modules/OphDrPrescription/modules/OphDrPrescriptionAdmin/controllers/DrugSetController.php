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
class DrugSetController extends BaseAdminController
{
    /**
     * @var int
     */
    public $itemsPerPage = 100;

    public $group = 'Drugs';

    public $assetPath;

    public function getFilters()
    {
        $default = [
            'query' => null,
            'subspecialty_id' => null,
            'site_id' => null,
            'usage_code_ids' => [MedicationUsageCode::model()->find()->id], // default to start with
        ];

        $filters = \Yii::app()->request->getParam('search');

        if (!$filters) {
            $filters = \Yii::app()->session->get('sets_filters');
            $filters = $filters ? $filters : $default;
        } else {
            \Yii::app()->session['sets_filters'] = $filters;
        }

        // make sure all the required keys are set
        foreach (['query', 'subspecialty_id', 'site_id'] as $item) {
            if (!isset($filters[$item])) {
                $filters[$item] = null;
            }
        }

        if (!isset($filters['usage_code_ids']) || !is_array($filters['usage_code_ids'])) {
            $filters['usage_code_ids'] = [];
        }

        return $filters;
    }

    public function actionIndex()
    {
        $asset_manager = \Yii::app()->getAssetManager();
        $base_assets_path = \Yii::getPathOfAlias('application.modules.OphDrPrescription.modules.OphDrPrescriptionAdmin.assets.js');
        $asset_manager->publish($base_assets_path);

        Yii::app()->clientScript->registerScriptFile($asset_manager->getPublishedUrl($base_assets_path).'/OpenEyes.OphDrPrescriptionAdmin.js', \CClientScript::POS_HEAD);

        $model = new MedicationSet();
        $model->unsetAttributes();
        if (isset($_GET['MedicationSet'])) {
            $model->attributes = $_GET['MedicationSet'];
        }

        $filters = $this->getFilters();
        $criteria = $this->getSearchCriteria($filters);

        $data_provider = new CActiveDataProvider('MedicationSet', [
            'criteria' => $criteria,
        ]);

        $pagination = new CPagination($data_provider->totalItemCount);
        $pagination->pageSize = $this->itemsPerPage;
        $pagination->applyLimit($criteria);

        $data_provider->pagination = $pagination;

        $this->render('/DrugSet/index', [
            'data_provider' => $data_provider,
            'search' => $filters
        ]);
    }

    private function getSearchCriteria($filters = [])
    {
        $criteria = new \CDbCriteria();

        $criteria->with = ['medicationSetRules'];
        $criteria->together = true;

        if (isset($filters['usage_code_ids']) && $filters['usage_code_ids'] ) {
            $criteria->addInCondition('usage_code_id', $filters['usage_code_ids']);
        }

        if (isset($filters['automatic'])) {
            $criteria->addCondition('automatic', $filters['automatic']);
        }

        if (isset($filters['query']) && $filters['query']) {
            $criteria->addSearchCondition('name', $filters['query']);
        }

        foreach (['site_id', 'subspecialty_id'] as $search_key) {
            if (isset($filters[$search_key]) && $filters[$search_key]) {
                $criteria->addCondition("medicationSetRules.{$search_key} = :$search_key");
                $criteria->params[":$search_key"] = $filters[$search_key];
            }
        }

        // just make sure usage_code_ids is set every time
        if (!isset($filters['usage_code_ids']) || !is_array($filters['usage_code_ids'])) {
            $filters['usage_code_ids'] = [];
        }

        return $criteria;
    }

    public function actionSearch()
    {
        $filters = $this->getFilters();
        $criteria = $this->getSearchCriteria($filters);
        $data['items'] = [];

        //OphDrPrescription/admin/DrugSet/index?MedicationSet_sort=name.asc
        $sort = new \CSort();
        $sort->attributes = array(
            'name' => [
                'asc' => 'name asc',
                'desc' => 'name desc',
            ],
        );

        $data_provider = new CActiveDataProvider('MedicationSet', [
            'sort' => $sort,
            'criteria' => $criteria,
        ]);

        $pagination = new CPagination($data_provider->totalItemCount);
        $pagination->pageSize = $this->itemsPerPage;
        $pagination->applyLimit($criteria);

        $data_provider->pagination = $pagination;

        foreach ($data_provider->getData() as $set) {

            $set_attributes = $set->attributes;
            $set_attributes['count'] = $set->itemsCount();
            $set_attributes['hidden'] = $set->attributes['hidden'] ? $set->attributes['hidden'] : null;
            $set_attributes['automatic'] = $set->attributes['automatic'] ? $set->attributes['automatic'] : null;
            $rules = MedicationSetRule::model()->findAllByAttributes(['medication_set_id' => $set->id]);
            $ret_val = [];

            foreach ($rules as $rule) {
                $ret_val[]= "Site: " . (!$rule->site ? "-" : $rule->site->name) .
                    ", SS: " . (!$rule->subspecialty ? "-" : $rule->subspecialty->name) .
                    ", Usage code: " . ($rule->usageCode ? $rule->usageCode->name : '-');
            }

            $set_attributes['rules'] = implode(" // ", $ret_val);
            $data['items'][] = $set_attributes;
        }

        ob_start();
        $this->widget('LinkPager', ['pages' => $pagination]);
        $pagination = ob_get_contents();
        ob_clean();
        $data['pagination'] = $pagination;

        echo CJSON::encode($data);
        \Yii::app()->end();
    }

    public function actionSearchMedication()
    {
        $search = \Yii::app()->request->getParam('search');
        $set_id = isset($search['set_id']) ? $search['set_id'] : null;
        $data['items'] = [];

        $filters = \Yii::app()->request->getParam('search', []);
        $criteria = new \CDbCriteria();

        if (isset($filters['set_id']) && $filters['set_id']) {
            $criteria->together = true;
            $criteria->with = ['medication', 'medicationSet'];

            $criteria->addCondition('medication_set_id = :set_id');
            $criteria->params[':set_id'] = $filters['set_id'];
        }

        if (isset($filters['query']) && $filters['query']) {
            $criteria->addSearchCondition('preferred_term', trim($filters['query']));
        }

        $data_provider = new CActiveDataProvider('MedicationSetItem', [
            'criteria' => $criteria,
        ]);

        $pagination = new \CPagination($data_provider->totalItemCount);
        $pagination->pageSize = 20;
        //$pagination->applyLimit($criteria);

        $data_provider->pagination = $pagination;

        foreach ($data_provider->getData() as $set_item) {

            $item = $set_item->attributes;
            $item['default_route'] = $set_item->defaultRoute ? $set_item->defaultRoute->term : null;
            $item['default_duration'] = $set_item->defaultDuration ? $set_item->defaultDuration->name : null;
            $item['default_frequency'] = $set_item->defaultFrequency ? $set_item->defaultFrequency->term : null;
            $item['preferred_term'] = $set_item->medication ? $set_item->medication->preferred_term : null;
            $item['medication_id'] = $set_item->medication ? $set_item->medication->id : null;

            $data['items'][] = $item;
        }

        ob_start();
        $this->widget('LinkPager', ['pages' => $pagination]);
        $pagination = ob_get_clean();
        $data['pagination'] = $pagination;

        header('Content-type: application/json');
        echo CJSON::encode($data);
        \Yii::app()->end();
    }

    /**
     * Edits or adds drug sets.
     *
     * @param bool $id
     * @throws Exception
     */
    public function actionEdit($id = null)
    {
        $assetManager = \Yii::app()->getAssetManager();
        $baseAssetsPath = \Yii::getPathOfAlias('application.modules.OphDrPrescription.modules.OphDrPrescriptionAdmin.assets.js');
        $assetManager->publish($baseAssetsPath);

        Yii::app()->clientScript->registerScriptFile($assetManager->getPublishedUrl($baseAssetsPath).'/OpenEyes.OphDrPrescriptionAdmin.js', \CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($assetManager->getPublishedUrl($baseAssetsPath).'/OpenEyes.UI.TableInlieEdit.js', \CClientScript::POS_HEAD);

        $data = \Yii::app()->request->getParam('MedicationSet');
        $filters = \Yii::app()->request->getParam('search', []);

        $set = MedicationSet::model()->findByPk($id);

        if(!$set) {
            $set = new MedicationSet;
        }

        // automatic sets cannot be edited here
        if ($set->automatic) {
            $this->redirect("/OphDrPrescription/admin/DrugSet/index");
        }

        $is_new_record = $set->isNewRecord;

        if (\Yii::app()->request->isPostRequest) {

            if (!$set->automatic) {
                $set->name = $data['name'];

                // set relation
                $relation = [];
                $rules = \Yii::app()->request->getParam('MedicationSetRule', []);
                $keep_rule_ids = [];
                foreach ($rules as $rule) {
                    if (isset($rule['id']) && $rule['id']) {
                        $keep_rule_ids[] = $rule['id'];
                        $rule_model = MedicationSetRule::model()->findByPk($rule['id']);
                        if ($rule_model) {
                            $rule_model->attributes = $rule;
                        }
                    } else {
                        $rule_model = new MedicationSetRule;
                        $rule_model->attributes = $rule;
                    }

                    if ($rule_model) {
                        $relation[] = $rule_model;
                    }
                }

                $set->medicationSetRules = $relation;
                $criteria = new \CDbCriteria();
                $criteria->addNotInCondition('id', $keep_rule_ids);
                $criteria->addCondition('medication_set_id = :set_id');
                $criteria->params['set_id'] = $set->id;
                \MedicationSetRule::model()->deleteAll($criteria);

                if ($set->autoValidateRelation(true)->validate() && !$set->getErrors()) {
                    if ($set->save()) {
                        foreach ($set->medicationSetRules as $rule_model) {
                            $rule_model->medication_set_id = $set->id;
                            $rule_model->save();
                        }

                        $this->redirect($is_new_record ? "/OphDrPrescription/admin/DrugSet/edit/{$set->id}" : "/OphDrPrescription/admin/DrugSet/index");
                    }
                }
            } else {

                // if the set is an auto set we just managing site, subspecialty and usage_code


            }




        }

        $criteria = new \CDbCriteria();
        $criteria->with = ['medicationSetItems', 'medicationSetItems.medicationSet'];
        $criteria->together = true;
        $criteria->addCondition('medicationSet.id = :set_id');
        $criteria->params[':set_id'] = $set->id;

        if (isset($filters['query']) && $filters['query']) {
            $criteria->addSearchCondition('preferred_term', $filters['query']);
        }

        $data_provider = new CActiveDataProvider('Medication', [
            'criteria' => $criteria,
        ]);

        $pagination = new CPagination($data_provider->totalItemCount);
        $pagination->pageSize = 20;
        $pagination->applyLimit($criteria);

        $data_provider->pagination = $pagination;

        $this->render('/DrugSet/edit', ['medication_set' => $set, 'medication_data_provider' => $data_provider]);
    }

    public function actionDelete()
    {
        $ids = \Yii::app()->request->getParam('delete-ids', []);
        $usage_code = \Yii::app()->request->getParam('usage-code');
        $response['message'] = '';

        foreach ($ids as $id) {
            $set = \MedicationSet::model()->findByPk($id);

            if ($set && $usage_code) {

                // if the set is automatic we just remove the usage code
                if ($set->automatic) {
                    $deleted_rows = $set->removeUsageCode($usage_code);

                } else {
                    $count = $set->itemsCount();
                    if (!$count) {
                        if (\MedicationSetRule::model()->deleteAllByAttributes(['medication_set_id' => $id])) {
                            $set->delete();
                        }
                    } else {
                        $response['message'] .= "Set '{$set->name}' is not empty. Please delete the medications first.<br>";
                    }
                }
            }
        }

        // This is because of handleButton.js handles the deletion - yes should be refactored...
        // protected/assets/js/handleButtons.js
        if ($response['message']) {
            echo \CJSON::encode($response);
        } else {
            echo "1";
        }

        \Yii::app()->end();

    }

    public function actionUpdateMedicationDefaults()
    {
        $result['success'] = false;
        if (\Yii::app()->request->isPostRequest) {
            $set_id = \Yii::app()->request->getParam('set_id');
            $item_data = \Yii::app()->request->getParam('MedicationSetItem', []);
            $medication_data = \Yii::app()->request->getParam('Medication', []);

            if ($set_id && isset($medication_data['id']) && $medication_data['id'] && isset($item_data['id'])) {

                $item = \MedicationSetItem::model()->findByPk($item_data['id']);

                if($item) {
                    $item->default_dose = isset($item_data['default_dose']) ? $item_data['default_dose'] : $item->default_dose;
                    $item->default_route_id = isset($item_data['default_route_id']) ? $item_data['default_route_id'] : $item->default_route_id;
                    $item->default_frequency_id = isset($item_data['default_frequency_id']) ? $item_data['default_frequency_id'] : $item->default_frequency_id;
                    $item->default_duration_id = isset($item_data['default_duration_id']) ? $item_data['default_duration_id'] : $item->default_duration_id;

                    $result['success'] = $item->save();
                    $result['errors'] = $item->getErrors();
                }
            }
        }

        echo \CJSON::encode($result);
        \Yii::app()->end();
    }

    public function actionAddMedicationToSet()
    {
        $result['success'] = false;
        if (\Yii::app()->request->isPostRequest) {
            $set_id = \Yii::app()->request->getParam('set_id');
            $set = \MedicationSet::model()->findByPk($set_id);
            $medication_id = \Yii::app()->request->getParam('medication_id');

            if($set && $medication_id) {
                $id = $set->addMedication($medication_id);
                $result['success'] = (bool)$id;
                $result['id'] = $id;
            }
        }

        echo \CJSON::encode($result);
        \Yii::app()->end();
    }

    public function actionRemoveMedicationFromSet()
    {
        $result['success'] = false;
        if (\Yii::app()->request->isPostRequest) {
            $item = \Yii::app()->request->getParam('MedicationSetItem');

            if(isset($item['id'])) {
                $affected_rows = \MedicationSetItem::model()->deleteByPk($item['id']);
                $result['success'] = (bool)$affected_rows;
            } else {
                $result['success'] = false;
                $result['error'] = "Missing ID.";
            }
        }

        echo \CJSON::encode($result);
        \Yii::app()->end();
    }
}
