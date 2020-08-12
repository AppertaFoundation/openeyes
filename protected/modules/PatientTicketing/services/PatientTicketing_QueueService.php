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
use Yii;

class PatientTicketing_QueueService extends \services\ModelService
{
    protected static $operations = array(self::OP_READ, self::OP_SEARCH, self::OP_DELETE);

    protected static $primary_model = 'OEModule\PatientTicketing\models\Queue';

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
     * @param OEModule\PatientTicketing\models\Queue $queue
     *
     * @return resource
     */
    public function modelToResource($queue)
    {
        $res = parent::modelToResource($queue);
        foreach (array('name', 'description', 'action_label', 'active', 'is_initial') as $pass_thru) {
            $res->$pass_thru = $queue->$pass_thru;
        }
        if ($queue->assignment_fields) {
            $res->assignment_fields = \CJSON::decode($queue->assignment_fields);
        }

        return $res;
    }

    /**
     * Wrapper to get the current ticket count for the Queue.
     *
     * @param $queue_id
     *
     * @return mixed
     */
    public function getCurrentTicketCount($queue_id)
    {
        $queue = $this->readModel($queue_id);

        return $queue->getCurrentTicketCount();
    }

    /**
     * Check if the given Queue can be deleted (has no tickets assigned and no dependent queues with tickets).
     *
     * @param $queue_id
     *
     * @return bool
     */
    public function canDeleteQueue($queue_id)
    {
        if ($this->getCurrentTicketCount($queue_id)) {
            return false;
        }
        $queue = $this->readModel($queue_id);
        if ($queue && $queue->is_initial) {
            return false;
        }
        foreach ($queue->getDependentQueueIds() as $dep_id) {
            if ($this->getCurrentTicketCount($dep_id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the dependent queues for given Queue resource.
     *
     * @param PatientTicketing_Queue $qr
     losing
     * @return models\Queue[]
     * @todo: return resources instead of models
     */
    public function getDependentQueues(PatientTicketing_Queue $qr, $include_closing = true)
    {
        $queue = $this->readModel($qr->getId());
        $res = array();
        $d_ids = $queue->getDependentQueueIds();
        $dependents = $include_closing ?
                $this->model->active()->findAllByPk($d_ids)
                : $this->model->active()->notClosing()->findAllByPk($d_ids);

        return $dependents;
        /*
        foreach ($dependents as $dq) {
            $res[] = $this->modelToResource($dq);
        }
        return $res;
        */
    }

    public function getDependentClosingQueues(PatientTicketing_Queue $qr)
    {
        $queue = $this->readModel($qr->getId());
        $d_ids = $queue->getDependentQueueIds();

        return $this->model->closing()->findAllByPk($d_ids);
    }

    public function getRootQueue($queue_id)
    {
        $queue = $this->readModel($queue_id);

        return $queue->getRootQueue();
    }

    /**
     * Delete the queue and the queues that are are solely dependent on it.
     *
     * @param $queue_id
     *
     * @throws \Exception
     * @throws \Exception
     */
    public function delete($queue_id)
    {
        $transaction = Yii::app()->db->getCurrentTransaction() === null
                ? Yii::app()->db->beginTransaction()
                : false;

        try {
            $queue = $this->readModel($queue_id);
            // remove dependendent outcomes
            $remove_ids = $queue->getDependentQueueIds();
            $remove_ids[] = $queue_id;

            // how I'd do it if BaseActiveRecordVersioned supported delete with an in condition
            /*
            $criteria = new \CDbCriteria();
            $criteria->addInCondition('outcome_queue_id', $remove_ids);
            $criteria->addInCondition('queue_id', $remove_ids, 'OR');
            models\QueueOutcome::model()->deleteAll($criteria);

            // remove dependent and actual queues
            $criteria = new \CDbCriteria();
            $criteria->addInCondition($this->model->getPrimaryKey(), $remove_ids);
            $this->model->deleteAll($criteria);
            */

            // instead ...
            foreach ($remove_ids as $rid) {
                $criteria = new \CDbCriteria();
                $criteria->addColumnCondition(array('outcome_queue_id' => $rid, 'queue_id' => $rid), 'OR');
                models\QueueOutcome::model()->deleteAll($criteria);
                $this->model->deleteByPk($rid);
            }

            \Audit::add('admin', 'delete', $queue->id, null, array('module' => 'PatientTicketing', 'model' => $queue->getShortModelName()));

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
}
