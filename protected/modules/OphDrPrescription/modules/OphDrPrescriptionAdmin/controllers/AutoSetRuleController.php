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

        $command = new PopulateAutoMedicationSetsCommand('PopulateAutoMedicationSets', new CConsoleCommandRunner());
        $command_is_running = $command->actionCheckRunning();

        $this->render('/AutoSetRule/index', [
            'data_provider' => $data_provider,
            'search' => $filters,
            'button_name' => $command_is_running ? 'Processing, may take a few minutes' : 'Rebuild all sets now',
            'button_status' => $command_is_running ? 'disabled' : '',
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

        $unique_med = [];

        foreach ($data_provider->getData() as $set_item) {
            $item = $set_item->attributes;
            if (!in_array($item['medication_id'],$unique_med)){
                $item['default_route'] = $set_item->defaultRoute ? $set_item->defaultRoute->term : null;
                $item['default_duration'] = $set_item->defaultDuration ? $set_item->defaultDuration->name : null;
                $item['default_frequency'] = $set_item->defaultFrequency ? $set_item->defaultFrequency->term : null;
                $item['preferred_term'] = $set_item->medication ? $set_item->medication->preferred_term : null;
                $item['medication_id'] = $set_item->medication ? $set_item->medication->id : null;

                $data['items'][] = $item;
                array_push($unique_med,$item['medication_id']);
            }
        }

        $item_num = count($data['items']);

        $pagination = new \CPagination($item_num ?? $data_provider->totalItemCount);
        $pagination->pageSize = 20;

        $data_provider->pagination = $pagination;

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
            $set_rules = \Yii::app()->request->getParam('MedicationSetRule', []);

            // so we can display what user set previously
            $set->medicationAutoRuleAttributes = $this->repopulateFields('attrs', \Yii::app()->request->getParam('MedicationSetAutoRuleAttributes', []));
            $set->medicationSetAutoRuleSetMemberships = $this->repopulateFields('sets', \Yii::app()->request->getParam('MedicationSetAutoRuleSetMemberships', []));
            $set->medicationSetRules = $this->repopulateFields('rules', $set_rules);
            $set->medicationSetAutoRuleMedications = $this->repopulateFields('meds', \Yii::app()->request->getParam('MedicationSetAutoRuleMedication', []));


            $set->name = $data['name'];
            $set->hidden = $data['hidden'];

            $set->validate();
            $set->validateRelations();

            if (!$set_rules && $set->name !== "medication_management" && !$set->hidden) {
                $set->addError('medicationSetRules', 'Usage rules must be set for visible sets.');
            }

            if (!$set->hasErrors() && $set->save()) {
                $this->actionPopulateAll($set->id);
            }
        }

        $criteria = new \CDbCriteria();
        $criteria->with = ['medicationSet'];
        $criteria->together = true;
        $criteria->addCondition('medicationSet.id = :set_id');
        $criteria->params[':set_id'] = $set->id;
        $criteria->order = 'medicationSet.id';
        $criteria->limit = 20;

        if (isset($filters['query']) && $filters['query']) {
            $criteria->addSearchCondition('preferred_term', $filters['query']);
        }

        $data_provider = new CActiveDataProvider('MedicationSetAutoRuleMedication', [
            'criteria' => $criteria,
        ]);

        if (!empty($set->medicationSetAutoRuleMedications)) {
            $unique_med = [];

            foreach($set->medicationSetAutoRuleMedications as $med) {
                if (!array_key_exists($med->medication_id,$unique_med)){
                    $unique_med[$med->medication_id] = $med;
                }
            }

            $set->medicationSetAutoRuleMedications = array_values($unique_med);
            $item_num = count($set->medicationSetAutoRuleMedications);
            $data_provider->setData($set->medicationSetAutoRuleMedications);
        }

        $pagination = new CPagination($item_num ?? $data_provider->totalItemCount);
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
        $this->redirect('/OphDrPrescription/admin/AutoSetRule/index');
    }

    public function actionCheckRebuildIsRunning()
    {
        $command = new PopulateAutoMedicationSetsCommand('PopulateAutoMedicationSets', new CConsoleCommandRunner());
        echo $command->actionCheckRunning();
    }

    public function actionDelete()
    {
        $ids = Yii::app()->request->getParam("delete-ids", []);
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

    public function actionRemoveMedicationFromSet()
    {
        $result['success'] = false;
        if (\Yii::app()->request->isPostRequest) {
            $id = \Yii::app()->request->getPost('id');

            if ($id) {
                $med = \MedicationSetAutoRuleMedication::model()->findByPk($id);
                $result['success'] = $med->deleteWithTapers()->delete();
            } else {
                $result['success'] = false;
                $result['error'] = "Missing ID.";
            }
        }

        echo \CJSON::encode($result);
        \Yii::app()->end();
    }

    private function repopulateFields($field_set, $tmp_set)
    {
        $set_m = [];
        switch ($field_set) {
            case 'attrs':
                foreach ($tmp_set as $row => $med_attr) {
                    $set_m[$row] = MedicationSetAutoRuleAttribute::model()->findByPk($med_attr['id']) ?? new MedicationSetAutoRuleAttribute;
                    $set_m[$row]->medication_attribute_option_id = $med_attr['medication_attribute_option_id'];
                }
                break;

            case 'sets':
                foreach ($tmp_set as $row => $med_set) {
                    $set_m[$row] = MedicationSetAutoRuleSetMembership::model()->findByPk($med_set['id']) ?? new MedicationSetAutoRuleSetMembership();
                    $set_m[$row]->attributes =  $med_set;
                }
                break;

            case 'meds':
                foreach ($tmp_set as $row => $med_meds) {
                    $set_m[$row] = isset($med_meds['id']) && $med_meds['id'] !== '' ? MedicationSetAutoRuleMedication::model()->findByPk($med_meds['id']) : new MedicationSetAutoRuleMedication();
                    $set_m[$row]->attributes =  $med_meds;
                    $tapers = \Yii::app()->request->getParam('MedicationSetAutoRuleMedicationTaper', []);

                    // tapers
                    if (isset($tapers[$row])) {
                        $new_tapers = [];
                        foreach ($tapers[$row] as $taper) {
                            $new_taper = MedicationSetAutoRuleMedicationTaper::model()->findByPk($taper['id']);
                            if (!$new_taper) {
                                $new_taper = new MedicationSetAutoRuleMedicationTaper();
                            }
                            $new_taper->dose = $taper['dose'];
                            $new_taper->duration_id = $taper['duration_id'];
                            $new_taper->frequency_id = $taper['frequency_id'];
                            $new_tapers[] = $new_taper;
                        }
                        $set_m[$row]->tapers = $new_tapers;
                    }
                }
                break;

            case 'rules':
                foreach ($tmp_set as $row => $med_rules) {
                    $set_m[$row] = MedicationSetRule::model()->findByPk($med_rules['id']) ?? new MedicationSetRule();
                    $set_m[$row]->attributes =  $med_rules;
                }
                break;
        }

        return $set_m;
    }
}
