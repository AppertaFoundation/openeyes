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
            'usage_codes' => [],
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

        if (!isset($filters['usage_codes']) || !is_array($filters['usage_codes'])) {
            $filters['usage_codes'] = [];
        }

        return $filters;
    }

    public function actionIndex()
    {
        $assetManager = \Yii::app()->getAssetManager();
        $baseAssetsPath = \Yii::getPathOfAlias('application.modules.OphDrPrescription.modules.OphDrPrescriptionAdmin.assets.js');
        $assetManager->publish($baseAssetsPath);

        Yii::app()->clientScript->registerScriptFile($assetManager->getPublishedUrl($baseAssetsPath).'/OpenEyes.OphDrPrescriptionAdmin.js', \CClientScript::POS_HEAD);

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

        if (isset($filters['usage_codes']) && $filters['usage_codes'] ) {
            $criteria->addInCondition('usage_code', $filters['usage_codes']);
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
        if (!isset($filters['usage_codes']) || !is_array($filters['usage_codes'])) {
            $filters['usage_codes'] = [];
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
            $set_attributes['hidden'] = $set->attributes['hidden'] ? 'Yes' : 'No';
            $rules = MedicationSetRule::model()->findAllByAttributes(['medication_set_id' => $set->id]);
            $ret_val = [];

            foreach ($rules as $rule) {
                $ret_val[]= "Site: ".(!$rule->site ? "-" : $rule->site->name).
                    ", SS: ".(!$rule->subspecialty ? "-" : $rule->subspecialty->name).
                    ", Usage code: ".$rule->usage_code;
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

    /**
     * Edits or adds drug sets.
     *
     * @param bool $id
     * @throws Exception
     */
    public function actionEdit($id = false)
    {
        $assetManager = \Yii::app()->getAssetManager();
        $baseAssetsPath = \Yii::getPathOfAlias('application.modules.OphDrPrescription.modules.OphDrPrescriptionAdmin.assets.js');
        $assetManager->publish($baseAssetsPath);

        Yii::app()->clientScript->registerScriptFile($assetManager->getPublishedUrl($baseAssetsPath).'/OpenEyes.OphDrPrescriptionAdmin.js', \CClientScript::POS_HEAD);

        $set = new MedicationSet;
        $data = \Yii::app()->request->getParam('MedicationSet');
        $filters = \Yii::app()->request->getParam('search', []);

        if ($id) {
            $set = MedicationSet::model()->findByPk($id);
        }

        $is_new_record = $set->isNewRecord;

        if (\Yii::app()->request->isPostRequest) {

            if ($set) {
                $set->name = $data['name'];

                // set relation
                $relation = [];
                $rules = \Yii::app()->request->getParam('MedicationSetRule', []);

                foreach ($rules as $rule) {
                    if (isset($rule['id']) && $rule['id']) {
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

                // nice, before we could save all the relations we need to save the set itself because the relations validation will fail
                // for missing a FK key
                if($set->save()) {
                    $set->medicationSetRules = $relation;
                    if ($set->autoValidateAndSaveRelation(true)->validate() && $set->save()) {
                        $this->redirect($is_new_record ? "/OphDrPrescription/admin/DrugSet/edit/{$set->id}" : "/OphDrPrescription/admin/DrugSet/index");
                    }
                }
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
        $response['message'] = '';

        foreach ($ids as $id) {
            $set = \MedicationSet::model()->findByPk($id);
            $count = $set->itemsCount();
            if (!$count) {
                if (\MedicationSetRule::model()->deleteAllByAttributes(['medication_set_id' => $id])) {
                    $set->delete();
                }
            } else {
                $response['message'] .= "Set '{$set->name}' is not empty. ";
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

    public function actionAddMedicationToSet()
    {
        $result['success'] = false;
        if (\Yii::app()->request->isPostRequest) {
            $set_id = \Yii::app()->request->getParam('set_id');
            $set = \MedicationSet::model()->findByPk($set_id);
            $medication_id = \Yii::app()->request->getParam('medication_id');

            if($set && $medication_id) {
                $result['success'] = $set->addMedication($medication_id);
            }
        }

        echo \CJSON::encode($result);
        \Yii::app()->end();
    }

    public function actionRemoveMedicationFromSet()
    {
        $result['success'] = false;
        if (\Yii::app()->request->isPostRequest) {
            $set_id = \Yii::app()->request->getParam('set_id');
            $medication_id = \Yii::app()->request->getParam('medication_id');

            if($set_id && $medication_id) {
                $affected_rows = \MedicationSetItem::model()->deleteAllByAttributes(['medication_id' => $medication_id, 'medication_set_id' => $set_id]);
                $result['success'] = (bool)$affected_rows;
            } else {
                $result['success'] = false;
                $result['error'] = "Missing parameter.";
            }
        }

        echo \CJSON::encode($result);
        \Yii::app()->end();
    }
}
