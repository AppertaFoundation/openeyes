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
class AutoSetRuleController extends BaseAdminController
{
    /**
     * @var int
     */
    public $itemsPerPage = 100;

    public $group = 'Drugs';

    public $assetPath;

    const FILTER_USAGE_CODE_ID_FOR_ALL = 'ALL';

    public function actionIndex()
    {
        $asset_manager = \Yii::app()->getAssetManager();
        $base_assets_path = \Yii::getPathOfAlias('application.modules.OphDrPrescription.modules.OphDrPrescriptionAdmin.assets.js');
        $asset_manager->publish($base_assets_path, true);

        Yii::app()->clientScript->registerScriptFile($asset_manager->getPublishedUrl($base_assets_path, true) . '/OpenEyes.OphDrPrescriptionAdmin.js', \CClientScript::POS_HEAD);

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

        $this->render('/AutoSetRule/index', [
            'data_provider' => $data_provider,
            'search' => $filters
        ]);
    }

    public function getFilters()
    {
        $default = [
            'query' => null,
            'subspecialty_id' => null,
            'site_id' => null,
            'usage_code_ids' => [self::FILTER_USAGE_CODE_ID_FOR_ALL],
        ];

        $filters = \Yii::app()->request->getParam('search');

        if (!$filters) {
            $filters = \Yii::app()->session->get('sets_filters');
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

    private function getSearchCriteria($filters = [])
    {
        $criteria = new \CDbCriteria();

        $criteria->with = ['medicationSetRules'];
        $criteria->together = true;

        if (isset($filters['usage_code_ids']) &&
            $filters['usage_code_ids'] &&
            !in_array(self::FILTER_USAGE_CODE_ID_FOR_ALL, $filters['usage_code_ids'])) {
            $criteria->addInCondition('usage_code_id', $filters['usage_code_ids']);
        }

        if (isset($filters['query']) && $filters['query']) {
            $criteria->addSearchCondition('name', $filters['query']);
        }

        $criteria->addCondition("automatic = 1");

        foreach (['site_id', 'subspecialty_id'] as $search_key) {
            if (isset($filters[$search_key]) && $filters[$search_key]) {
                $criteria->addCondition("medicationSetRules . {$search_key} = :$search_key");
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
                $ret_val[] = "Site: " . (!$rule->site ? "-" : $rule->site->name) .
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

        $data_provider = new CActiveDataProvider('MedicationSetAutoRuleMedication', [
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
        $error = [];
        $asset_manager = \Yii::app()->getAssetManager();
        $base_assets_path = \Yii::getPathOfAlias('application.modules.OphDrPrescription.modules.OphDrPrescriptionAdmin.assets.js');
        $asset_manager->publish($base_assets_path, true);

        Yii::app()->clientScript->registerScriptFile($asset_manager->getPublishedUrl($base_assets_path, true) . '/OpenEyes.OphDrPrescriptionAdmin.js', \CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($asset_manager->getPublishedUrl($base_assets_path, true) . '/OpenEyes.UI.TableInlineEdit.js', \CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($asset_manager->getPublishedUrl($base_assets_path, true).'/OpenEyes.UI.TableInlineEdit.PrescriptionAdminMedicationSet.js', \CClientScript::POS_HEAD);

        $filters = \Yii::app()->request->getParam('search', []);
        $data = \Yii::app()->request->getParam('MedicationSet');

        $set = MedicationSet::model()->findByPk($id);

        if (!$set) {
            $set = new MedicationSet;
            $set->automatic = 1;
        }

        if (\Yii::app()->request->isPostRequest) {
            $set->tmp_attrs = \Yii::app()->request->getParam('MedicationSetAutoRuleAttributes', []);
            $set->tmp_sets = \Yii::app()->request->getParam('MedicationSetAutoRuleSetMemberships', []);
            $set->tmp_rules = \Yii::app()->request->getParam('MedicationSetRule', []);
            $this->actionPopulateAll($set->id);
            // $set->tmp_meds (MedicationSetAutoRuleMedication) are added via ajax

            $set->name = $data['name'];
            $set->hidden = $data['hidden'];

            //validate here so if tmp_rules are empty we can return these errors as well
            $set->validate();

            if (!$set->tmp_rules && $set->name !== "medication_management" && !$set->hidden) {
                $set->addError('medicationSetRules', 'Usage rules must be set for visible sets.');
            }

            if (!$set->hasErrors() && $set->save()) {
                $this->redirect('/OphDrPrescription/admin/autoSetRule/index');
            }
        }

        $criteria = new \CDbCriteria();
        $criteria->with = ['medicationSetAutoRuleMedication'];
        $criteria->together = true;
        $criteria->addCondition('medicationSetAutoRuleMedication.medication_set_id = :set_id');
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

        $this->render('/AutoSetRule/edit/edit', [
            'set' => $set, 'error' => $error,
            'medication_data_provider' => $data_provider,
        ]);
    }

    public function actionPopulateAll($set_id = '')
    {
        shell_exec("php " . Yii::app()->basePath . "/yiic populateautomedicationsets ". $set_id ." >/dev/null 2>&1 &");
        Yii::app()->user->setFlash('success', "Rebuild process started at " . date('H:i') . ".");
        if ($set_id === 'null') {
            $this->redirect('/OphDrPrescription/admin/AutoSetRule/index');
        }
    }

    public function actionDelete()
    {
        $ids = Yii::app()->request->getPost("delete-ids", []);
        foreach ($ids as $id) {
            $set = MedicationSet::model()->findByPk($id);
            $trans = Yii::app()->db->beginTransaction();
            try {
                $set->delete();
            } catch (Exception $e) {
                $trans->rollback();
                \OELog::log($e->getMessage());
                echo 0;
                exit;
            }

            $trans->commit();
        }

        echo 1;
        exit;
    }

    public function actionUpdateMedicationDefaults()
    {
        $result['success'] = false;
        $transaction = Yii::app()->db->beginTransaction();
        try {
            if (\Yii::app()->request->isPostRequest) {
                $set_id = \Yii::app()->request->getParam('set_id');
                $item_data = \Yii::app()->request->getParam('MedicationSetAutoRuleMedication', []);
                $medication_data = \Yii::app()->request->getParam('Medication', []);
                $tapers = json_decode(\Yii::app()->request->getParam('tapers', []), true);

                if ($set_id && $medication_data['id'] && isset($item_data['id'])) {
                    $item = \MedicationSetAutoRuleMedication::model()->findByPk($item_data['id']);

                    if ($item) {
                        $item->default_dose = isset($item_data['default_dose']) ? $item_data['default_dose'] : $item->default_dose;
                        $item->default_route_id = isset($item_data['default_route_id']) ? $item_data['default_route_id'] : $item->default_route_id;
                        $item->default_frequency_id = isset($item_data['default_frequency_id']) ? $item_data['default_frequency_id'] : $item->default_frequency_id;
                        $item->default_duration_id = isset($item_data['default_duration_id']) ? $item_data['default_duration_id'] : $item->default_duration_id;
                        $item->default_dispense_condition_id = isset($item_data['default_dispense_condition_id']) ? $item_data['default_dispense_condition_id'] : $item->default_dispense_condition_id;
                        $item->default_dispense_location_id = isset($item_data['default_dispense_location_id']) ? $item_data['default_dispense_location_id'] : $item->default_dispense_location_id;
                        $item->include_parent = isset($item_data['include_parent']) ? $item_data['include_parent'] : $item->include_parent;
                        $item->include_children = isset($item_data['include_children']) ? $item_data['include_children'] : $item->include_children;

                        $item->tapers = array();


                        if ($tapers) {
                            $taper_array = array();
                            foreach ($tapers as $taper) {
                                $taper = json_decode($taper, true);
                                $new_taper = new MedicationSetAutoRuleMedicationTaper();
                                if (isset($taper['MedicationSetAutoRuleMedicationTaper[id]']) && $taper['MedicationSetAutoRuleMedicationTaper[id]'] !== "") {
                                    $new_taper = MedicationSetAutoRuleMedicationTaper::model()->findByPk($taper['MedicationSetAutoRuleMedicationTaper[id]']);
                                }
                                $new_taper->medication_set_auto_rule_id = $item->id;
                                $new_taper->dose = $taper['MedicationSetAutoRuleMedicationTaper[dose]'];
                                $new_taper->duration_id = $taper['MedicationSetAutoRuleMedicationTaper[duration_id]'];
                                $new_taper->frequency_id = $taper['MedicationSetAutoRuleMedicationTaper[frequency_id]'];
                                $taper_array[] = $new_taper;
                            }
                            $item->tapers = $taper_array;
                        }

                        // auto relation update throws an error
                        try {
                            $result['success'] = $item->save();
                        } catch (Exception $e) {
                            $result['success'] = false;

                            //interesting behaviour, after $item->save() fails the original relation is restored
                            if ($tapers) {
                                $item->tapers = $taper_array;
                            }
                        }

                        $result['errors'] = [];
                        foreach ($item->tapers as $taper) {
                            if (!$taper->validate() && $taper->hasErrors()) {
                                foreach ($taper->getErrors() as $key => $error) {
                                    $result['errors'][$taper->id] = $error[0];
                                }
                            }
                        }

                        if ($result['success'] === true) {
                            $transaction->commit();
                        } else {
                            $transaction->rollback();
                        }
                    }
                } else {
                    $result['errorsMessage'] = 'Missing required parameters.';
                }
            }
        } catch (Exception $e) {
                $transaction->rollback();
                $result['errorsMessage'] = $e->getMessage();
        } finally {
            echo \CJSON::encode($result);
            \Yii::app()->end();
        }
    }

    public function actionListMedications()
    {
        $set_id = \Yii::app()->request->getParam('set_id');
        $search = \Yii::app()->request->getParam('search');
        if (!$set_id) {
            \Yii::app()->user->setFlash('error', 'Set not found.');
            $this->redirect('/OphDrPrescription/admin/AutoSetRule/index');
        }

        $medication_set = \MedicationSet::model()->findByPk($set_id);

        $criteria = new \CDbCriteria();
        $criteria->join = 'JOIN medication_set_item i ON t.id = i.medication_id ';
        $criteria->join .= 'JOIN medication_set s ON s.id = i.medication_set_id';
        $criteria->addCondition('s.id = :set_id');
        $criteria->params[':set_id'] = $set_id;

        if ($search) {
            if (is_numeric($search)) {
                $criteria->addSearchCondition('preferred_code', $search, true);
            } else {
                $criteria->addSearchCondition('LOWER(t.preferred_term)', strtolower($search), true);
            }
        }

        $data_provider = new CActiveDataProvider('Medication', [
            'criteria' => $criteria
        ]);

        $pagination = new CPagination($data_provider->totalItemCount);
        $pagination->pageSize = $this->itemsPerPage;
        $pagination->applyLimit($criteria);

        $data_provider->pagination = $pagination;

        $this->render('/AutoSetRule/listMedications', [
            'medication_set_name' => $medication_set->name,
            'data_provider' => $data_provider,
        ]);
    }

    private function addMedication($set_id, $medication_id)
    {
        $set_auto_rule_med = new MedicationSetAutoRuleMedication();
        $set_auto_rule_med->medication_id = $medication_id;
        $set_auto_rule_med->medication_set_id = $set_id;
        $set_auto_rule_med->include_children = 1;
        $set_auto_rule_med->include_parent = 1;
        $set_auto_rule_med->created_date = date('Y-m-d H:i:s');

        $set_auto_rule_med->save();

        return $set_auto_rule_med;
    }

    public function actionAddMedicationToSet()
    {
        $result['success'] = false;
        if (\Yii::app()->request->isPostRequest) {
            $set_id = \Yii::app()->request->getParam('set_id');
            $set = \MedicationSet::model()->findByPk($set_id);
            $medication_id = \Yii::app()->request->getParam('medication_id');

            if ($set && $medication_id) {
                $med = $this->addMedication($set->id, $medication_id);

                if (!$med->hasErrors()) {
                    $result['success'] = true;
                    $result['id'] = $med->id;
                } else {
                    $result['success'] = false;
                    $result['id'] = null;
                    $result['msg'] = $med->getErrors();
                }
            } else {
                $result['msg'][] = !$set_id ? 'Set id is required. ' : '';
                $result['msg'][] = ($set_id && !$set) ? 'Set not found' : '';
            }
        } else {
            $result['msg'][]= "Only POST request is supported";
        }

        echo \CJSON::encode($result);
        \Yii::app()->end();
    }

    public function actionRemoveMedicationFromSet()
    {
        $result['success'] = false;
        if (\Yii::app()->request->isPostRequest) {
            $item = \Yii::app()->request->getParam('MedicationSetAutoRuleMedication');

            if (isset($item['id'])) {
                $med = \MedicationSetAutoRuleMedication::model()->findByPk($item['id']);
                $result['success'] = $med->deleteWithTapers()->delete();
            } else {
                $result['success'] = false;
                $result['error'] = "Missing ID.";
            }
        }

        echo \CJSON::encode($result);
        \Yii::app()->end();
    }
}
