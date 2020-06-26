<?php
/**
 * (C) Copyright Apperta Foundation 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class RequestController extends \AdminController
{
    public $layout = '//layouts/admin';
    public $group = 'API';
    public $items_per_page = 20;
    public $filters_list = ['order_by', 'from_date', 'to_date', 'from_id', 'show_complete',
        'show_incomplete', 'show_failed', 'extra-filters',
        'show_trycount_higher_than_one', 'routine_and_status_filter'];


    public function actionIndex()
    {
        $this->render('/request/index', [
            'data' => $this->getRequests(),
        ]);
    }

    public function getRequests()
    {
        $criteria = $this->createCriteriaFromFilters();

        $page = Yii::app()->getRequest()->getParam('page');
        $count = Request::model()->count($criteria);
        $pages = ceil($count / $this->items_per_page);

        if ($page < 1) {
            $page = 1;
        }
        if ($page > $pages) {
            $page = $pages;
        }

        $criteria->limit = $this->items_per_page;
        $criteria->offset = ($page - 1) * $this->items_per_page;

        return [
            'requests' => Request::model()->findAll($criteria),
            'pagination' => $this->initPagination(Request::model(), $criteria),
        ];
    }

    public function createCriteriaFromFilters()
    {
        $criteria = new CDbCriteria();
        $show_filters = ['COMPLETE' => 1, 'INCOMPLETE' => 1, 'FAILED' => 1];

        foreach ($this->filters_list as $filter_name) {
            $filter = Yii::app()->getRequest()->getParam($filter_name);

            switch ($filter_name) {
                case 'order_by':
                    if ($filter === 'latest') {
                        $criteria->order = 'last_modified_date DESC';
                    } elseif ($filter === 'earliest') {
                        $criteria->order = 'last_modified_date';
                    }
                    break;
                case 'from_date':
                    if (!empty($filter)) {
                        $criteria->addCondition('last_modified_date >= :from_date');
                        $time = Yii::app()->getRequest()->getParam('from_time');
                        if (strlen($time) === 2) {
                            $time .= ':00';
                        }
                        $criteria->params[':from_date'] = date('Y-m-d H:i:s', strtotime($filter . ' ' . $time));
                    }
                    break;
                case 'to_date':
                    if (!empty($filter)) {
                        $criteria->addCondition('last_modified_date <= :to_date');
                        $time = Yii::app()->getRequest()->getParam('to_time');
                        if (empty($time)) {
                            $time = "23:59:59";
                        }
                        if (strlen($time) === 2) {
                            $time .= ':00';
                        }
                        $criteria->params[':to_date'] = date('Y-m-d H:i:s', strtotime($filter . ' ' . $time));
                    }
                    break;
                case 'from_id':
                    if (!empty($filter)) {
                        $criteria->addCondition('id >= :from_id');
                        $criteria->params[':from_id'] = $filter;
                        $to_id = Yii::app()->getRequest()->getParam('to_id');
                        $criteria->addCondition('id <= :to_id');
                        if (!empty($to_id)) {
                            $criteria->params[':to_id'] = $to_id;
                        } else {
                            $criteria->params[':to_id'] = $filter;
                        }
                    }
                    break;
                case 'show_complete':
                    if ($filter === '0') {
                        $show_filters['COMPLETE'] = 0;
                    }
                    break;
                case 'show_incomplete':
                    if ($filter === '0') {
                        $show_filters['INCOMPLETE'] = 0;
                    }
                    break;
                case 'show_failed':
                    if ($filter === '0') {
                        $show_filters['FAILED'] = 0;
                    }
                    break;
                case 'show_trycount_higher_than_one':
                    if ($filter === '1') {
                        $criteria->addCondition('t.id IN (SELECT DISTINCT(request_id) FROM request_routine WHERE try_count > 1)');
                    }
                    break;

                case 'routine_and_status_filter':
                    if (!empty($filter)) {
                        $routine_name = isset($filter['routine_name']) ? $filter['routine_name'] : null;
                        $routine_status = isset($filter['routine_status']) ? $filter['routine_status'] : null;
                        $condition_added = false;

                        $condition = 't.id IN (SELECT DISTINCT(request_id) FROM request_routine WHERE ';
                        if ($routine_name !== null && $routine_name !== '') {
                            $condition .= ' routine_name = :routine_name';
                            $condition_added = true;
                        }

                        if ($routine_status !== null && $routine_status !== '') {
                            if ($condition_added) {
                                $condition .= ' AND ';
                            }
                            $condition .= ' status = :routine_status';
                            $condition_added = true;
                        }

                        $condition .= ')';

                        if ($condition_added) {
                            $criteria->addCondition($condition);
                            if ($routine_name !== null && $routine_name !== '') {
                                $criteria->params[':routine_name'] = $routine_name;
                            }
                            if ($routine_status !== null && $routine_status !== '') {
                                $criteria->params[':routine_status'] = $routine_status;
                            }
                        }
                    }

                    break;
                case 'extra-filters':
                    if (!empty($filter)) {
                        $condition = '';
                        $first = true;
                        $bindParams = array();
                        $counter = 0;
                        foreach ($filter as $name => $value) {
                            if ($first) {
                                $condition = "(SELECT request_id FROM request_details WHERE name = :extra_filter_name_{$counter} AND value LIKE CONCAT('%', :extra_filter_value_{$counter}, '%'))";
                                $first = false;
                            } else {
                                $condition = "(SELECT request_id FROM request_details WHERE name = :extra_filter_name_{$counter} AND value LIKE CONCAT('%', :extra_filter_value_{$counter}, '%') AND request_id IN " . $condition . ')';
                            }
                            $bindParams[":extra_filter_name_$counter"] = $name;
                            $bindParams[":extra_filter_value_$counter"] = trim($value);
                            $counter++;
                        }

                        if (!empty($condition)) {
                            $criteria->addCondition('t.id IN ' . $condition);
                            $criteria->params = array_merge($criteria->params, $bindParams);
                        }
                    }
                    break;
            }
        }

        if (array_search(0, $show_filters)) {
            if ($show_filters['COMPLETE'] || $show_filters['INCOMPLETE'] || $show_filters['FAILED']) {
                $condition = '';
                foreach ($show_filters as $name => $show_filter) {
                    if ($show_filter) {
                        if (!empty($condition)) {
                            $condition .= ' OR ';
                        }

                        switch ($name) {
                            case 'COMPLETE':
                                $condition .= 't.id NOT IN (SELECT DISTINCT(request_id) FROM request_routine WHERE status = "' . RequestRoutineStatus::NEW_STATUS . '" OR status = "' . RequestRoutineStatus::RETRY_STATUS . '" OR status = "' . RequestRoutineStatus::FAILED_STATUS . '")';
                                break;
                            case 'INCOMPLETE':
                                $condition .= 't.id IN 
                                (SELECT DISTINCT(request_id) 
                                FROM request_routine rr WHERE rr.status IN("'. RequestRoutineStatus::NEW_STATUS .'","'. RequestRoutineStatus::RETRY_STATUS .'")
                                AND NOT EXISTS (SELECT * FROM request_routine subrr 
                                                WHERE subrr.request_id = rr.request_id
                                                AND subrr.execute_sequence < rr.execute_sequence
                                                AND subrr.status NOT IN("'. RequestRoutineStatus::COMPLETE_STATUS .'","' . RequestRoutineStatus::VOID_STATUS . '")
                                ))';
                                break;
                            case 'FAILED':
                                $condition .= 't.id IN 
                                (SELECT DISTINCT(request_id) 
                                FROM request_routine rr WHERE rr.status IN("'. RequestRoutineStatus::FAILED_STATUS .'")
                                AND NOT EXISTS (SELECT * FROM request_routine subrr 
                                                 WHERE subrr.request_id = rr.request_id
                                                 AND subrr.execute_sequence < rr.execute_sequence
                                                   AND subrr.status IN("'. RequestRoutineStatus::NEW_STATUS .'","' . RequestRoutineStatus::RETRY_STATUS . '")
                                 ))';
                        }
                    }
                }

                $criteria->addCondition('t.id IN (SELECT DISTINCT(request_id) FROM request_routine WHERE ' . $condition . ')');
            }
        }

        return $criteria;
    }
}
