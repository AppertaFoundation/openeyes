<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PatientTicketing\services;

use Yii;

class PatientTicketing_QueueSetCategoryService extends \services\ModelService
{
    protected static $operations = array(self::OP_READ, self::OP_SEARCH, self::OP_DELETE);

    protected static $primary_model = 'OEModule\PatientTicketing\models\QueueSetCategory';

    public function search(array $params)
    {
        $model = $this->getSearchModel();
        if (isset($params['id'])) {
            $model->id = $params['id'];
        }

        $searchParams = array('pageSize' => null);
        if (isset($params['name'])) {
            $searchParams['name'] = $params['name'];
        }

        return $this->getResourcesFromDataProvider($model->search($searchParams));
    }

    /**
     * Pass through wrapper to generate Queue Resource.
     *
     * @param OEModule\PatientTicketing\models\QueueSetCategory $queue
     *
     * @return resource
     */
    public function modelToResource($queuesetcategory)
    {
        $res = parent::modelToResource($queuesetcategory);
        foreach (array('name', 'display_order') as $pass_thru) {
            $res->$pass_thru = $queuesetcategory->$pass_thru;
        }

        return $res;
    }

    /**
     * Only return Category if it is active.
     *
     * @param $id
     *
     * @return resource
     */
    public function readActive($id)
    {
        return $this->modelToResource($this->model->findByPk($id));
    }

    /**
     * Get the categories that the user has permission to process.
     *
     * @param $user_id
     *
     * @return PatientTicketing_QueueSetCategory[]
     */
    public function getCategoriesForUser($user_id)
    {
        $permissioned = array();
        foreach ($this->model->findAll() as $qsc) {
            $qscr = $this->modelToResource($qsc);
            if ($this->isCategoryPermissionedForUser($qscr, $user_id)) {
                $permissioned[] = $qscr;
            }
        }

        return $permissioned;
    }

    /**
     * @param PatientTicketing_QueueSetCategory $qscr
     * @param $user_id
     *
     * @return bool
     */
    public function isCategoryPermissionedForUser(PatientTicketing_QueueSetCategory $qscr, $user_id)
    {
        $ct = count($this->getCategoryQueueSetsForUser($qscr, $user_id));
        if ($ct) {
            return true;
        }

        return false;
    }

    /**
     * @param PatientTicketing_QueueSetCategory $qscr
     * @param $user_id
     *
     * @return PatientTicketing_QueueSet[]
     */
    public function getCategoryQueueSetsForUser(PatientTicketing_QueueSetCategory $qscr, $user_id)
    {
        $res = array();
        $qs_svc = Yii::app()->service->getService('PatientTicketing_QueueSet');
        foreach ($qs_svc->getQueueSetsForCategory($qscr) as $qsr) {
            if ($qs_svc->isQueueSetPermissionedForUser($qsr, $user_id)) {
                $res[] = $qsr;
            }
        }

        return $res;
    }

    public function getCategoryQueueSetsList(PatientTicketing_QueueSetCategory $qscr, $user_id)
    {
        $res = array();
        foreach ($this->getCategoryQueueSetsForUser($qscr, $user_id) as $qsr) {
            $res[$qsr->getID()] = $qsr->name;
        }

        return $res;
    }
}
