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

    public function actionIndex() {
        $asset_manager = \Yii::app()->getAssetManager();
        $base_assets_path = \Yii::getPathOfAlias('application.modules.OphDrPrescription.modules.OphDrPrescriptionAdmin.assets.js');
        $asset_manager->publish($base_assets_path);

        Yii::app()->clientScript->registerScriptFile($asset_manager->getPublishedUrl($base_assets_path).'/OpenEyes.OphDrPrescriptionAdmin.js', \CClientScript::POS_HEAD);

        $model = new Medication();
        $model->unsetAttributes();
        if (isset($_GET['Medication'])) {
            $model->attributes = $_GET['Medication'];
        }

        $criteria = $this->getSearchCriteria();
        $filters = \Yii::app()->request->getParam('search');

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

    private function getSearchCriteria()
    {
        $filters = \Yii::app()->request->getParam('search', []);
        $criteria = new \CDbCriteria();

        $searchFields = [
            'source_type',
            'source_subtype',
            'preferred_code',
            'preferred_term'
        ];

        $addSearch = function ($field) use ($criteria, $filters) {
            if (isset($filters[$field])) {
                $criteria->addCondition($field . ' = :' . $field);
                $criteria->params[':' . $field] = $filters[$field];
            }
        };

        array_map($addSearch, $searchFields);

        return $criteria;
    }

    public function actionSearch()
    {
        $search = \Yii::app()->request->getParam('search');
        $set_id = isset($search['set_id']) ? $search['set_id'] : null;
        $criteria = $this->getSearchCriteria();
        $data['items'] = [];

        $data_provider = new CActiveDataProvider('Medication', [
            'criteria' => $criteria,
        ]);

        $pagination = new \CPagination($data_provider->totalItemCount);
        $pagination->pageSize = 20;
        //$pagination->applyLimit($criteria);

        $data_provider->pagination = $pagination;

        foreach ($data_provider->getData() as $med) {

            $item = $med->attributes;
            $link = \MedicationSetItem::model()->findByAttributes(['medication_id' => $med->id, 'medication_set_id' => $set_id]);
            if ($link) {
                foreach (['default_dose', 'default_route_id', 'default_frequency_id', 'default_duration_id', 'default_dose_unit_term'] as $key) {
                    $item[$key] = $link->{$key};
                }

                $item['default_route'] = $link->defaultRoute ? $link->defaultRoute->term : null;
                $item['default_duration'] = $link->defaultDuration ? $link->defaultDuration->name : null;
                $item['default_frequency'] = $link->defaultFrequency ? $link->defaultFrequency->term : null;
                $item['set_item_id'] = $link->id;
                $data['items'][] = $item;
                $item = null;
            }
        }

        ob_start();
        $this->widget('LinkPager', ['pages' => $pagination]);
        $pagination = ob_get_clean();
        $data['pagination'] = $pagination;

        header('Content-type: application/json');
        echo CJSON::encode($data);
        \Yii::app()->end();
    }


}
