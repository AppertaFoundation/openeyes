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
class MedicationController extends BaseAdminController
{
    /**
     * @var int
     */
    public $itemsPerPage = 50;

    public $group = 'Drugs';

    public $assetPath;

    private $searchFields = [
        'source_type',
        'source_subtype',
        'preferred_code',
        'preferred_term'
    ];


    public function actionIndex() {
        $asset_manager = \Yii::app()->getAssetManager();
        $base_assets_path = \Yii::getPathOfAlias('application.modules.OphDrPrescription.modules.OphDrPrescriptionAdmin.assets.js');
        $asset_manager->publish($base_assets_path);

        Yii::app()->clientScript->registerScriptFile($asset_manager->getPublishedUrl($base_assets_path).'/OpenEyes.OphDrPrescriptionAdminMedication.js', \CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScript('OphDrPrescriptionAdminMedication', "medicationController.addOption('{ \"searchFields\": " . json_encode($this->searchFields)."}');");

        $filters = \Yii::app()->request->getParam('search');
        $criteria = $this->getSearchCriteria($filters);

        $data_provider = new CActiveDataProvider('medication', [
            'criteria' => $criteria,
        ]);

        $pagination = new CPagination($data_provider->totalItemCount);
        $pagination->pageSize = $this->itemsPerPage;
        $pagination->applyLimit($criteria);

        $data_provider->pagination = $pagination;

        $this->render('/Medication/index', [
            'data_provider' => $data_provider,
            'search' => $filters
        ]);
    }

    private function getSearchCriteria($filters = [])
    {
        $criteria = new \CDbCriteria();

        $addSearch = function ($field) use ($criteria, $filters) {
            if (isset($filters[$field]) && !empty($filters[$field])) {
                $criteria->addCondition($field . ' = :' . $field);
                $criteria->params[':' . $field] = $filters[$field];
            }
        };

        array_map($addSearch, $this->searchFields);

        return $criteria;
    }

    public function actionSearch()
    {
        $model = new Medication();
        $model->unsetAttributes();
        if (isset($_GET['Medication'])) {
            $model->attributes = $_GET['Medication'];
        }

        $search = \Yii::app()->request->getParam('search');
        $criteria = $this->getSearchCriteria($search);
        $data['items'] = [];

        $data_provider = new CActiveDataProvider('medication', [
            'criteria' => $criteria,
        ]);

        $pagination = new \CPagination($data_provider->totalItemCount);
        $pagination->pageSize = 20;
        $data_provider->pagination = $pagination;

        foreach ($data_provider->getData() as $med) {
            $item = $med->attributes;
            $data['items'][] = $item;
            $item = null;
        }

        ob_start();
        $this->widget('LinkPager', ['pages' => $pagination]);
        $pagination = ob_get_clean();
        $data['pagination'] = $pagination;

        header('Content-type: application/json');
        echo CJSON::encode($data);
        \Yii::app()->end();
    }

    public function actionEdit($id = null) {
        if (!\Yii::app()->request->isPostRequest) {
            $model;
            if (isset($id)) {
                $model = Medication::model()->findByPk($id);
            } else {
                $model = Medication::model();
                $model->isNewRecord = true;
            }

            $this->render('/Medication/edit', [
                'model' => $model
            ]);
            return;
        }

        $data = \Yii::app()->request->getParam('Medication');
        $filters = \Yii::app()->request->getParam('search', []);

        $medication = Medication::model()->findByPk($id);

        if(!$medication) {
            $medication = new Medication;
        }

        $is_new_record = $medication->isNewRecord;

        $transpose = function ($arr) {
            $out = [];
            foreach ($arr as $key => $all_vals) {
                foreach ($all_vals as $val_key => $val) {
                    if (!array_key_exists($val_key, $out)) {
                        $out[$val_key] = [];
                    }
                    $out[$val_key][$key] = $val;
                }
            }
            return $out;
        };

        $updateRelation = function ($relation_name, $relation_type, &$data) use ($transpose, $medication) {
            $transposed_relations = array_key_exists($relation_name, $data) ?
                $transpose($data[$relation_name]) : [];

            $data[$relation_name] = [];

            foreach ($transposed_relations as $relation) {
                $relation_model = $relation['id'] == '-1' ?
                    new $relation_type() :
                    $relation_type::model()->findByPk($relation['id']);

                $relation['medication_id'] = $medication->id;
                $relation_model->setAttributes($relation);
                if ($relation_model->save()) {
                    array_push($data[$relation_name], $relation_model);
                } else {
                    return;
                }
            }
        };

        $updateRelation('medicationAttributeAssignments', 'MedicationAttributeAssignment', $data);
        $updateRelation('medicationSetItems', 'MedicationSetItem', $data);
        $updateRelation('medicationSearchIndexes', 'MedicationSearchIndex', $data);

        $medication->setAttributes($data);

        if ($medication->autoValidateRelation(true)->validate() && !$medication->getErrors()) {
            if ($medication->save()) {
                $this->redirect("/OphDrPrescription/admin/Medication/index");
            }
        }
    }

    public function actionDelete() {
        $ids = \Yii::app()->request->getParam('delete-ids', []);

        try {
            foreach ($ids as $id) {
                $medication = \Medication::model()->findByPk($id);

                if (!$medication)
                    break;

                $to_delete = array_merge(
                    $medication->medicationSearchIndexes,
                    $medication->medicationSetItems,
                    $medication->medicationAttributeAssignments
                );
                foreach ($to_delete as $relation) {
                    $relation->delete();
                }
                $medication->delete();
            }
        } catch (Exception $e) {
            echo '0';
            return;
        }

        echo "1";

        \Yii::app()->end();
    }
}
