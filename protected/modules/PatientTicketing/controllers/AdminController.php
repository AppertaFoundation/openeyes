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

namespace OEModule\PatientTicketing\controllers;

use OEModule\PatientTicketing\models;
use Yii;

class AdminController extends \ModuleAdminController
{
    public static $TICKET_SERVICE = 'PatientTicketing_Ticket';
    public static $QUEUE_SERVICE = 'PatientTicketing_Queue';
    public static $QUEUESET_SERVICE = 'PatientTicketing_QueueSet';

    public $group = 'PatientTicketing';

    protected function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            Yii::app()->clientScript->registerScriptFile(
                Yii::app()->createUrl($this->assetPath . '/js/jquery.jOrgChart.js')
            );

            Yii::app()->clientScript->registerCssFile(
                Yii::app()->createUrl($this->assetPath . '/css/jquery.jOrgChart.css')
            );

            return true;
        }
    }

    /**
     * Define the actions limited to POST requests.
     *
     * @return array
     */
    public function filters()
    {
        $filters = parent::filters();
        $filters[] = 'postOnly + activateQueue, deactivateQueue, deleteQueue';

        return $filters;
    }

    /**
     * Render the main admin screen.
     */
    public function actionIndex()
    {
        $this->render('index', array('queuesets' => models\QueueSet::model()->findAll(), 'title' => 'Queue Sets'));
    }

    public function actionQueueSetCategories()
    {
        $this->genericAdmin(
            'Edit Queue Set Categories',
            'OEModule\PatientTicketing\models\QueueSetCategory',
            ['div_wrapper_class' => 'cols-5']
        );
    }

    public function actionTicketAssignOutcomes()
    {
        $this->genericAdmin(
            'Edit Outcome Options',
            'OEModule\PatientTicketing\models\TicketAssignOutcomeOption',
            array(
                'extra_fields' => array(
                    array(
                        'field' => 'episode_status_id',
                        'type' => 'lookup',
                        'model' => 'EpisodeStatus',
                    ),
                    array(
                        'field' => 'followup',
                        'type' => 'boolean',
                    ),
                ),
                'div_wrapper_class' => 'cols-5',
            )
        );
    }

    /**
     * Create a new QueueSet along with its initial queue (cannot have a queue set without an initial queue).
     *
     * @throws \CHttpException
     */
    public function actionAddQueueSet()
    {
        $queueset = new models\QueueSet('formCreate');
        $queue = new models\Queue();
        $queue->is_initial = true;
        $errors = array();

        if (!empty($_POST)) {
            $queueset->attributes = $_POST[\CHtml::modelName($queueset)];
            $queue->attributes = $_POST[\CHtml::modelName($queue)];
            if (!$queueset->validate()) {
                $errors['Queue Set'] = array();
                foreach ($queueset->getErrors() as $errs) {
                    foreach ($errs as $e) {
                        $errors['Queue Set'][] = $e;
                    }
                }
            }
            if (!$queue->validate()) {
                $errors['Initial Queue'] = array_values($queue->getErrors());
            }

            if (!count($errors)) {
                $transaction = Yii::app()->db->beginTransaction();
                try {
                    $queue->save();

                    $queueset->initial_queue_id = $queue->id;
                    $queueset->setScenario('insert');
                    $queueset->save();
                    \Audit::add('admin', 'create', $queueset->id, null, array('module' => 'PatientTicketing', 'model' => $queueset->getShortModelName()));

                    $transaction->commit();
                    $resp = array(
                        'success' => true,
                        'queuesetId' => $queueset->id,
                        'initialQueueId' => $queueset->initial_queue_id,
                    );
                    echo \CJSON::encode($resp);
                } catch (Exception $e) {
                    $transaction->rollback();
                    throw new \CHttpException(500, 'Could not save queue set and/or initial queue');
                }
            } else {
                $resp = array(
                    'success' => false,
                    'form' => $this->renderPartial('form_queueset', array(
                        'queueset' => $queueset,
                        'queue' => $queue,
                        'errors' => $errors,
                    ), true),
                );
                echo \CJSON::encode($resp);
            }
        } else {
            $this->renderPartial('form_queueset', array(
                'queueset' => $queueset,
                'queue' => $queue,
                'errors' => null,
            ));
        }
    }

    /**
     * Update the Queue Set specified by the id.
     *
     * @param $id
     *
     * @throws \CHttpException
     */
    public function actionUpdateQueueSet($id)
    {
        if (!$queueset = models\QueueSet::model()->findByPk($id)) {
            throw new \CHttpException(404, "Queue Set not found with id {$id}");
        }

        if (!empty($_POST)) {
            $queueset->attributes = $_POST[\CHtml::modelName($queueset)];

            if (!$queueset->validate()) {
                $resp = array(
                    'success' => false,
                    'form' => $this->renderPartial(
                        'form_queue',
                        array(
                            'queueset' => $queueset,
                            'queue' => null,
                            'errors' => $queueset->getErrors(),
                        ),
                        true
                    ),
                );
                echo \CJSON::encode($resp);
            } else {
                $transaction = Yii::app()->db->beginTransaction();
                try {
                    $queueset->save();
                    \Audit::add('admin', 'update', $queueset->id, null, array('module' => 'PatientTicketing', 'model' => $queueset->getShortModelName()));
                    $transaction->commit();
                    $resp = array(
                        'success' => true,
                        'queueSetId' => $queueset->id,
                        'initialQueueId' => $queueset->initial_queue_id,
                    );
                    echo \CJSON::encode($resp);
                } catch (Exception $e) {
                    $transaction->rollback();
                    throw new \CHttpException(500, 'Unable to create queue');
                }
            }
        } else {
            $this->renderPartial('form_queueset', array(
                'queueset' => $queueset,
                'queue' => null,
                'errors' => null,
            ));
        }
    }

    /**
     * interface for setting the user permissions for a queueset.
     *
     * @param $id
     *
     * @throws \CHttpException
     */
    public function actionQueueSetPermissions($id)
    {
        if (!$queueset = models\QueueSet::model()->findByPk($id)) {
            throw new \CHttpException(404, "Queue Set not found with id {$id}");
        }

        if (Yii::app()->request->isPostRequest) {
            $qs_svc = Yii::app()->service->getService(self::$QUEUESET_SERVICE);
            $ids = array();
            $user_ids = Yii::app()->request->getPost('user_ids', []);
            $user_role = Yii::app()->request->getPost('user_role');
            foreach ($user_ids as $id) {
                $ids[] = (int) $id;
            }
            $resp = array();

            $qs_svc->setPermisssionedUsers($queueset->id, $ids, $user_role);
            $resp['success'] = true;
            $resp['message'] = 'Queue set permissions updated';

            echo \CJSON::encode($resp);
            Yii::app()->end();
        }

        $this->renderPartial('form_queueset_perms', array(
                    'queueset' => $queueset,
                ), false, true);
    }
    /**
     * Create a new Queue with the optional given parent.
     *
     * @param null $parent_id
     *
     * @throws \CHttpException
     */
    public function actionAddQueue($parent_id = null)
    {
        $parent = null;
        $queue = new models\Queue();

        if ($parent_id) {
            if (!$parent = models\Queue::model()->findByPk($parent_id)) {
                throw new \CHttpException(404, "Queue not found with id {$parent_id}");
            }
            $queue->is_initial = false;
        }

        if (!empty($_POST)) {
            if (@$_POST['parent_id']) {
                if (!$parent = models\Queue::model()->findByPk($_POST['parent_id'])) {
                    throw new \CHttpException(404, "Queue not found with id {$_POST['parent_id']}");
                }
            }
            $this->saveQueue($queue, $parent);
        } else {
            $this->renderPartial('form_queue', array(
                    'parent' => $parent,
                    'queue' => $queue,
                    'errors' => null,
                ));
        }
    }

    /**
     * Update the given Queue.
     *
     * @param $id
     *
     * @throws \CHttpException
     */
    public function actionUpdateQueue($id)
    {
        if (!$queue = models\Queue::model()->findByPk($id)) {
            throw new \CHttpException(404, "Queue not found with id {$id}");
        }

        if (!empty($_POST)) {
            $this->saveQueue($queue);
        } else {
            $this->renderPartial('form_queue', array(
                    'parent' => null,
                    'queue' => $queue,
                    'errors' => null,
                ));
        }
    }

    /**
     * Performs the update/create process on a Queue.
     *
     * @param $queue
     * @param null $parent
     *
     * @throws \CHttpException
     */
    protected function saveQueue($queue, $parent = null)
    {
        // try and process form
        $queue->attributes = $_POST;
        if ($queue->isNewRecord) {
            $queue->is_initial = $parent ? false : true;
        }

        if (!$queue->validate()) {
            $resp = array(
                'success' => false,
                'form' => $this->renderPartial('form_queue', array(
                        'parent' => $parent,
                        'queue' => $queue,
                        'errors' => $queue->getErrors(),
                    ), true),
            );
            echo \CJSON::encode($resp);
        } else {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $action = $queue->isNewRecord ? 'create' : 'update';
                $queue->save();
                if ($parent) {
                    $outcome = new models\QueueOutcome();
                    $outcome->queue_id = $parent->id;
                    $outcome->outcome_queue_id = $queue->id;
                    $outcome->save();
                }
                \Audit::add('admin', $action, $queue->id, null, array('module' => 'PatientTicketing', 'model' => $queue->getShortModelName()));

                $transaction->commit();
                $resp = array(
                    'success' => true,
                    'queueId' => $queue->id,
                );
                echo \CJSON::encode($resp);
            } catch (Exception $e) {
                $transaction->rollback();
                throw new \CHttpException(500, 'Unable to create queue');
            }
        }
    }

    /**
     * Generates an HTML list layout of the given Queue and its Outcome Queues.
     *
     * @param $id
     *
     * @throws \CHttpException
     */
    public function actionLoadQueueNav($id)
    {
        if (!$queue = models\Queue::model()->findByPk((int) $id)) {
            throw new \CHttpException(404, "Queue not found with id {$id}");
        }
        $root = $queue->getRootQueue();
        if (is_array($root)) {
            throw new \CHttpException(501, "Don't currently support queues with multiple roots");
        }

        $queueset = models\QueueSet::model()->findByAttributes(array('initial_queue_id' => $root->id));

        $resp = array(
            'rootid' => $root->id,
            'queuesetid' => $queueset->id,
            'nav' => $this->renderPartial('queue_nav_item', array('queueset' => $queueset, 'queue' => $root), true),
        );
        echo \CJSON::encode($resp);
    }

    /**
     * Marks the given Queue as active.
     *
     * @throws \CHttpException
     */
    public function actionActivateQueue()
    {
        if (!$queue = models\Queue::model()->findByPk((int) @$_POST['id'])) {
            throw new \CHttpException(404, 'Queue not found with id '.@$_POST['id']);
        }
        $queue->active = true;
        if (!$queue->save()) {
            throw new \CHttpException(500, 'Could not change queue state');
        }
        \Audit::add('admin', 'update', $queue->id, null, array('module' => 'PatientTicketing', 'model' => $queue->getShortModelName()));
        echo 1;
    }

    /**
     * Marks the given Queue inactive.
     *
     * @throws \CHttpException
     */
    public function actionDeactivateQueue()
    {
        if (!$queue = models\Queue::model()->findByPk((int) @$_POST['id'])) {
            throw new \CHttpException(404, 'Queue not found with id '.@$_POST['id']);
        }
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $this->deactivateQueue($queue);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            throw new \CHttpException(500, 'Could not change queue state');
        }
        echo 1;
    }

    /**
     * Deactivate a Queue, and if $cascade is true, then deactivate it's children.
     *
     * @param $queue
     * @param bool $cascade
     */
    protected function deactivateQueue($queue, $cascade = true)
    {
        $queue->active = false;
        if ($cascade) {
            foreach ($queue->outcome_queues as $oc) {
                $this->deactivateQueue($oc);
            }
        }
        $queue->save();
        \Audit::add('admin', 'update', $queue->id, null, array('module' => 'PatientTicketing', 'model' => $queue->getShortModelName()));
    }

    /**
     * Retrieve the count of ticket assignments for the given Queue and whether it can be deleted.
     *
     * @param $id
     *
     * @throws \CHttpException
     */
    public function actionGetQueueTicketStatus($id)
    {
        $qs = Yii::app()->service->getService(self::$QUEUE_SERVICE);
        $qr = $qs->read((int) $id);

        $resp = array(
                'current_count' => $qs->getCurrentTicketCount($qr->getId()),
                'can_delete' => $qs->canDeleteQueue($qr->getId()), );

        echo \CJSON::encode($resp);
    }

    /**
     * Will only successfully delete a Queue if no ticket has ever been assigned to it, otherwise will throw
     * an exception. Should only have been called when the values return by actionGetQueueTicketCount are zero.
     *
     * @throws \Exception
     * @throws \CHttpException
     */
    public function actionDeleteQueue()
    {
        $qs = Yii::app()->service->getService(self::$QUEUE_SERVICE);
        $qr = $qs->read((int) @$_POST['id']);

        $qs->delete($qr->getId());
        echo 1;
    }

    public function actionClinicLocations()
    {
        $this->genericAdmin(
            'Clinic locations',
            'OEModule\PatientTicketing\models\ClinicLocation',
            ['div_wrapper_class' => 'cols-5']
        );
    }
}
