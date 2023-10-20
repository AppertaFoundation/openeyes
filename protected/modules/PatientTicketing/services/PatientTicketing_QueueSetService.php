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

use OEModule\PatientTicketing\models;
use OEModule\PatientTicketing\models\QueueSet;
use ReferenceData;
use Yii;

class PatientTicketing_QueueSetService  extends \services\ModelService
{
    public static $QUEUE_SERVICE = 'PatientTicketing_Queue';
    public static $QUEUESETCATEGORY_SERVICE = 'PatientTicketing_QueueSetCategory';

    protected static $operations = array(self::OP_READ, self::OP_SEARCH, self::OP_DELETE);
    protected static $primary_model = 'OEModule\PatientTicketing\models\QueueSet';

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
     * Pass through wrapper to generate QueueSet Resource.
     *
     * @param OEModule\PatientTicketing\models\QueueSet $queueset
     *
     * @return resource
     */
    public function modelToResource($queueset)
    {
        $res = parent::modelToResource($queueset);
        foreach (array('name', 'description', 'filter_priority', 'filter_subspecialty', 'filter_firm', 'filter_my_tickets', 'filter_closed_tickets') as $pass_thru) {
            $res->$pass_thru = $queueset->$pass_thru;
        }

        $qsvc = Yii::app()->service->getService(self::$QUEUE_SERVICE);
        $qscsvc = Yii::app()->service->getService(self::$QUEUESETCATEGORY_SERVICE);

        if ($queueset->initial_queue_id) {
            $res->initial_queue = $qsvc->read($queueset->initial_queue_id);
        }

        if ($queueset->permissioned_users) {
            foreach ($queueset->permissioned_users as $u) {
                $res->permissioned_user_ids[] = $u->id;
            }
        }

        if ($queueset->category_id) {
            $res->category = $qscsvc->read($queueset->category_id);
        }

        if ($queueset->default_queue_id) {
            $res->default_queue = $qsvc->read($queueset->default_queue_id);
        }

        return $res;
    }

    /**
     * Get all the queue set resources that are part of the given category.
     *
     * @param PatientTicketing_QueueSetCategory $qscr
     *
     * @return array
     */
    public function getQueueSetsForCategory(PatientTicketing_QueueSetCategory $qscr, bool $filter_institutions = true)
    {
        $class = self::$primary_model;
        $criteria = new \CDbCriteria();
        $criteria->addColumnCondition(['category_id' => $qscr->getId()]);

        if ($filter_institutions) {
            $criteria->addColumnCondition(['institutions.id' => Yii::app()->session['selected_institution_id']]);
        }

        $res = [];
        foreach ($class::model()->with('permissioned_users', 'institutions')->findAll($criteria) as $qs) {
            $res[] = $this->modelToResource($qs);
        }

        return $res;
    }

    /**
     * @param PatientTicketing_QueueSet $qsr
     * @param bool                      $include_closing
     *
     * @return models\Queue[]
     */
    public function getQueueSetQueues(PatientTicketing_QueueSet $qsr, $include_closing = true)
    {
        $q_svc = Yii::app()->service->getService(self::$QUEUE_SERVICE);
        $initial_qr = $qsr->initial_queue;
        if (!$initial_qr) {
            return array();
        }
        $res = array($q_svc->readModel($initial_qr->getId()));
        foreach ($q_svc->getDependentQueues($initial_qr, $include_closing) as $d_qr) {
            $res[] = $d_qr;
        };

        return $res;
    }

    public function getQueueSetClosingQueues(PatientTicketing_QueueSet $qsr)
    {
        $q_svc = Yii::app()->service->getService(self::$QUEUE_SERVICE);
        $initial_qr = $qsr->initial_queue;
        if (!$initial_qr) {
            return [];
        }

        return $q_svc->getDependentClosingQueues($initial_qr);
    }

    /**
     * Returns the roles configured to allow processing of queue sets.
     *
     * @return array
     */
    public function getQueueSetRoles()
    {
        $res = array();
        // iterate through roles and pick out those that have the operation as a child
        foreach (Yii::app()->authManager->getAuthItems(2) as $role) {
            if ($role->hasChild('TaskProcessQueueSet')) {
                $res[] = $role->name;
            }
        }

        return $res;
    }
    /**
     * @param int $queueset_id
     * @param int $user_ids[]
     *
     * @throws \Exception
     */
    public function setPermisssionedUsers($queueset_id, $user_ids, $role = null)
    {
        $qs = $this->readModel($queueset_id);
        $users = array();
        foreach ($user_ids as $id) {
            if (!$user = \User::model()->findByPk($id)) {
                throw new \Exception("User not found for id {$id}");
            }
            $users[] = $user;
        }

        $role_item = null;
        if ($role) {
            $role_item = Yii::app()->authManager->getAuthItem($role);
            if (!$role_item) {
                throw new \Exception("Unrecognised role {$role} for permissioning");
            }
        }

        $transaction = Yii::app()->db->getCurrentTransaction() === null
                ? Yii::app()->db->beginTransaction()
                : false;

        try {
            $qs->permissioned_users = $users;
            $qs->save();
            \Audit::add('admin', 'set-permissions', $qs->id, null, array('module' => 'PatientTicketing', 'model' => $qs->getShortModelName()));

            if ($role_item) {
                foreach ($users as $user) {
                    if (!$role_item->getAssignment($user->id)) {
                        $role_item->assign($user->id);
                        \Audit::add('admin-User', 'assign-role', "{$user->id}:{$role_item->name}");
                    }
                }
            }

            if ($transaction) {
                $transaction->commit();
            }
        } catch (\Exception $e) {
            if ($transaction) {
                $transaction->rollback();
            }
            throw $e;
        }
    }

    /**
     * @param PatientTicketing_QueueSet $qsr
     * @param $user_id
     *
     * @return bool
     */
    public function isQueueSetPermissionedForUser(PatientTicketing_QueueSet $qsr, $user_id)
    {
        return Yii::app()->getAuthManager()->checkAccess('OprnProcessQueueSet', $user_id, array($user_id, $qsr));
    }

    /**
     * @param $ticket_id
     *
     * @return PatientTicketing_QueueSet
     */
    public function getQueueSetForTicket($ticket_id)
    {
        $t = models\Ticket::model()->findByPk($ticket_id);

        return $this->modelToResource($this->model->findByAttributes(array('initial_queue_id' => $t->initial_queue->id)));
    }

    /**
     * @param int $queue_id
     *
     * @return PatientTicketing_QueueSet
     */
    public function getQueueSetForQueue($queue_id)
    {
        $q_svc = Yii::app()->service->getService(self::$QUEUE_SERVICE);
        $root = $q_svc->getRootQueue($queue_id);

        return $this->modelToResource($this->model->findByAttributes(array('initial_queue_id' => $root->id)));
    }

    /**
     * @param \Firm $firm
     *
     * @return array
     */
    public function getQueueSetsForFirm(\Firm $firm = null, \Institution $institution = null): array
    {
        $res = [];
        $queue_sets = $institution
            ? QueueSet::model()->findAllAtLevels(ReferenceData::LEVEL_ALL, null, $institution)
            : QueueSet::model()->findAll();

        foreach ($queue_sets as $qs) {
            $res[] = $this->modelToResource($qs);
        }

        return $res;
    }

    /**
     * Returns true if current rules allow the patient to be added to the given queueset.
     *
     * @param \Patient $patient
     * @param $queueset_id
     *
     * @return bool
     */
    public function canAddPatientToQueueSet(\Patient $patient, $queueset_id)
    {
        $tickets = models\Ticket::model()->with('current_queue')->findAllByAttributes(array('patient_id' => $patient->id));
        $q_rs = $this->getQueueSetQueues($this->read($queueset_id), false);
        $q_ids = array_map(function ($a) {
            return $a->id;
        }, $q_rs);

        foreach ($tickets as $t) {
            if (!isset($t->current_queue)) {
                return false;
            }

            if (in_array($t->current_queue->id, $q_ids)) {
                return false;
            }
        }

        return true;
    }
}
