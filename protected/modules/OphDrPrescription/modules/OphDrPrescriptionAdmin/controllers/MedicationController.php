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

    private function getSearchCriteria()
    {
        $filters = \Yii::app()->request->getParam('search', []);
        $criteria = new \CDbCriteria();

        if (isset($filters['set_id']) && $filters['set_id']) {
            $criteria->together = true;
            $criteria->with = ['medicationSetItems', 'medicationSetItems.medicationSet'];

            $criteria->addCondition('medicationSet.id = :set_id');
            $criteria->params[':set_id'] = $filters['set_id'];
        }

        if (isset($filters['query']) && $filters['query']) {
            $criteria->addSearchCondition('preferred_term', $filters['query']);
        }

        return $criteria;
    }

    public function actionSearch()
    {
        $criteria = $this->getSearchCriteria();
        $data['items'] = [];

        $data_provider = new CActiveDataProvider('Medication', [
            'criteria' => $criteria,
        ]);

        $pagination = new \CPagination($data_provider->totalItemCount);
        $pagination->pageSize = 20;
        $pagination->applyLimit($criteria);

        $data_provider->pagination = $pagination;

        $data['items'] = $data_provider->getData();

        ob_start();
        $this->widget('LinkPager', ['pages' => $pagination]);
        $pagination = ob_get_clean();
        $data['pagination'] = $pagination;

        echo CJSON::encode($data);
        \Yii::app()->end();
    }
}
