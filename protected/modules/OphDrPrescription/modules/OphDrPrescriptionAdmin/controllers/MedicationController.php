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
        // 'preferred_term' will be search differently
    ];


    public function actionIndex()
    {
        $asset_manager = \Yii::app()->getAssetManager();
        $assets_path = \Yii::getPathOfAlias('application.modules.OphDrPrescription.modules.OphDrPrescriptionAdmin.assets.js');
        $url = $asset_manager->publish($assets_path . '/OpenEyes.OphDrPrescriptionAdmin.js');

        \Yii::app()->clientScript->registerScriptFile($url, \CClientScript::POS_HEAD);

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

        $add_search = function ($field) use ($criteria, $filters) {
            if (isset($filters[$field]) && !empty($filters[$field])) {
                $criteria->addCondition($field . ' = :' . $field);
                $criteria->params[':' . $field] = $filters[$field];
            }
        };

        array_map($add_search, $this->searchFields);

        if (isset($filters['preferred_term']) && $filters['preferred_term']) {
            $criteria->addSearchCondition('preferred_term', $filters['preferred_term']);
        }

        return $criteria;
    }

    public function actionSearch()
    {
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

    public function actionEdit($id = null)
    {
        $medication = Medication::model()->findByPk($id);

        if (!isset($medication)) {
            $medication = new Medication();
        }

        if (\Yii::app()->request->isPostRequest) {
            if ($medication->isNewRecord) {
                //User created medications must be local
                $data['source_type'] = 'local';
            }

            $medication->attributes = \Yii::app()->request->getParam('Medication');
            $medication->medicationAttributeAssignments = \Yii::app()->request->getParam('MedicationAttributeAssignment', []);
            $medication->medicationSetItems = \Yii::app()->request->getParam('MedicationSetItem', []);
            $medication->medicationSearchIndexes = \Yii::app()->request->getParam('MedicationSearchIndex', []);

            if ($medication->save()) {
                $this->redirect("/OphDrPrescription/admin/Medication/index");
            }
        }

        $this->render('/Medication/edit', [
            'model' => $medication
        ]);
    }

    public function actionDelete()
    {
        $ids = \Yii::app()->request->getParam('delete-ids', []);
        $transaction = Yii::app()->db->beginTransaction();

        try {
            \Medication::model()->deleteAll('id IN (:ids)', [':ids' => implode(',', $ids)]);
            $transaction->commit();
            echo "1";
        } catch (Exception $e) {
            $transaction->rollback();
            echo "0";
            \OELog::log($e->getMessage());
        }

        \Yii::app()->end();
    }
}
