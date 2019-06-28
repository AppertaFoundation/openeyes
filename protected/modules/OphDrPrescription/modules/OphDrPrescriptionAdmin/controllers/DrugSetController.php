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
    public $itemsPerPage = 50;

    public $group = 'Drugs';

    public $assetPath;

    public function actionIndex()
    {
        $model = new MedicationSet();
        $model->unsetAttributes();
        if (isset($_GET['MedicationSet'])) {
            $model->attributes = $_GET['MedicationSet'];
        }

        $search = [
            'query' => '',
            'subspecialty_id' => null,
            'site_id' => null,
        ];

        $criteria = $this->getSearchCriteria();

        $dataProvider = new CActiveDataProvider('MedicationSet', [
            'criteria' => $criteria,
        ]);

        $pagination = new CPagination($dataProvider->totalItemCount);
        $pagination->pageSize = $this->itemsPerPage;
        $pagination->applyLimit($criteria);

        $dataProvider->pagination = $pagination;

        $this->render('/drugset/index', [
            'dataProvider' => $dataProvider,
            'search' => $search
        ]);
    }

    private function getSearchCriteria()
    {
        $filters = \Yii::app()->request->getParam('search', []);
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

        return $criteria;
    }

    public function actionSearch()
    {
        $criteria = $this->getSearchCriteria();
        $data['sets'] = [];

        $data_provider = new CActiveDataProvider('MedicationSet', [
            'criteria' => $criteria,
        ]);

        $pagination = new CPagination($data_provider->totalItemCount);
        $pagination->pageSize = $this->itemsPerPage;
        $pagination->applyLimit($criteria);

        $data_provider->pagination = $pagination;

        foreach ($data_provider->getData() as $set) {
            $set_attributes = $set->attributes;

            $rules = MedicationSetRule::model()->findAllByAttributes(['medication_set_id' => $set->id]);
            $ret_val = [];
            foreach ($rules as $rule) {
                $ret_val[]= "Site: ".(!$rule->site ? "-" : $rule->site->name).
                    ", SS: ".(!$rule->subspecialty ? "-" : $rule->subspecialty->name).
                    ", Usage code: ".$rule->usage_code;
            }

            $set_attributes['rules'] = implode(" // ", $ret_val);
            $data['sets'][] = $set_attributes;
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
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        $set = new MedicationSet;
        $data = \Yii::app()->request->getParam('MedicationSet');

        if ($id) {
            $set = MedicationSet::model()->findByPk($id);
        }

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

                $set->medicationSetRules = $relation;
                if ($set->autoValidateAndSaveRelation(true)->validate() && $set->save()) {
                    $this->redirect("/OphDrPrescription/admin/drugset/edit/{$id}");
                }
            }
        }

        $this->render('/drugset/edit', ['medication_set' => $set]);
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        // instead of delete we just set the active field to false
        if (Yii::app()->request->isPostRequest) {
            $ids = Yii::app()->request->getPost('DrugSet');
            foreach ($ids as $id) {
                $model = DrugSet::model()->findByPk($id);
                if ($model) {
                    $model->active = 0;
                    $model->save();
                }
            }
        }
        echo 1;
    }

    /**
     * Save drug set data from the admin interface.
     */
    public function actionSaveDrugSet()
    {
        // we need to decide if it's a new set or modification
        $drugSet = Yii::app()->request->getParam('DrugSet');
        $prescriptionItem = Yii::app()->request->getParam('prescription_item');

        if (isset($drugSet['id'])) {
            $drugSetId = $drugSet['id'];
        }
        if ($drugSetId > 0) {
            $drugset = DrugSet::model()->findByPk($drugSetId);
        } else {
            $drugset = new DrugSet();
        }
        $drugset->name = $drugSet['name'];
        $drugset->subspecialty_id = $drugSet['subspecialty'];
        $drugset->active = $drugSet['active'];

        if ($drugset->save()) {

            // we delete previous tapers and items, and insert the new ones

            $currentDrugRows = DrugSetItem::model()->findAll(new CDbCriteria(array('condition' => "drug_set_id = '".$drugset->id."'")));
            foreach ($currentDrugRows as $currentDrugRow) {
                DrugSetItemTaper::model()->deleteAll(new CDbCriteria(array('condition' => "item_id = '".$currentDrugRow->id."'")));
                $currentDrugRow->delete();
            }

            if (isset($prescriptionItem) && is_array($prescriptionItem)) {
                foreach ($prescriptionItem as $item) {
                    $item_model = new DrugSetItem();
                    $item_model->drug_set_id = $drugset->id;
                    $item_model->attributes = $item;
                    $item_model->save(); // we need an id to save tapers
                    if (isset($item['taper'])) {
                        $tapers = array();
                        foreach ($item['taper'] as $taper) {
                            $taper_model = new DrugSetItemTaper();
                            $taper_model->attributes = $taper;
                            $taper_model->item_id = $item_model->id;
                            $taper_model->save();
                            $tapers[] = $taper_model;
                        }
                        //$item_model->tapers = $tapers;
                    }
                    //$items[] = $item_model;
                    //$item_model->save();
                }
                Yii::app()->user->setFlash('info.save_message', 'Save successful.');
            } else {
                Yii::app()->user->setFlash('info.save_message',
                    'Unable to save drugs, please add at least one drug to the set. Set name and subspecialty saved.');
            }
            $this->redirect('/OphDrPrescription/admin/drugSet/list');
        } else {
            if ($drugSetId > 0) {
                $admin = $this->initAdmin($drugSetId);
            } else {
                $admin = $this->initAdmin(false);
            }
            $this->render('//admin/generic/edit', array('admin' => $admin, 'errors' => $drugset->getErrors()));
        }
    }
}
