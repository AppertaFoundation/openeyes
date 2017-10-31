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
use OEModule\PatientTicketing\services;
use OEModule\PatientTicketing\components\AutoSaveTicket;
use Yii;

class DefaultController extends \BaseModuleController
{
    public $layout = '//layouts/main';
    public $renderPatientPanel = false;
    protected $page_size = 10;
    public static $QUEUESETCATEGORY_SERVICE = 'PatientTicketing_QueueSetCategory';
    public static $QUEUESET_SERVICE = 'PatientTicketing_QueueSet';
    public static $TICKET_SERVICE = 'PatientTicketing_Ticket';

    /**
     * Ensures firm is set on the controller.
     *
     * @param \CAction $action
     *
     * @return bool
     */
    protected function beforeAction($action)
    {
        $this->setFirmFromSession();

        return parent::beforeAction($action);
    }

    /**
     * List of print actions.
     *
     * @return array:
     */
    public function printActions()
    {
        return array('printTickets');
    }

    /**
     * Access rules for ticket actions.
     *
     * @return array
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('expandTicket', 'collapseTicket', 'getPatientAlert', 'navigateToEvent', 'getFirmsForSubspecialty'),
                'roles' => array('OprnViewClinical'),
            ),
            array('allow',
                'actions' => array('index', 'getTicketTableRow', 'getTicketTableRowHistory'),
                'roles' => array('OprnViewQueueSet'),
            ),
            array('allow',
                'actions' => $this->printActions(),
                'roles' => array('OprnPrint'),
            ),
            // these will have additional checks in the action methods
            // TODO: detemine cleaner way of defining this.
            array('allow',
                'actions' => array('moveTicket', 'navigateToEvent', 'getQueueAssignmentForm', 'takeTicket', 'releaseTicket', 'startTicketProcess'),
                'roles' => array('OprnViewQueueSet'),
            ),
            array('allow',
                'actions' => array('undoLastStep'),
                'roles' => array('OprnUndoTicketLastStep'),
            ),
        );
    }

    protected function buildTicketFilterCriteria($filter_options, services\PatientTicketing_QueueSet $queueset)
    {
        $patient_filter = null;
        // build criteria
        $criteria = new \CDbCriteria();
        $qs_svc = Yii::app()->service->getService(self::$QUEUESET_SERVICE);

        if (@$_GET['patient_id']) {
            // this is a simple way of handling this for the sake of demo-ing functionality
            $criteria->addColumnCondition(array('patient_id' => $_GET['patient_id']));
            $patient_filter = \Patient::model()->findByPk($_GET['patient_id']);
        } else {
            // TODO: we probably don't want to have such a gnarly approach to this, we might want to denormalise so that we are able to do eager loading
            // That being said, we might get away with setting together false on the with to do this filtering (multiple query eager loading).
            $criteria->join = 'JOIN '.models\TicketQueueAssignment::model()->tableName().' cqa ON cqa.ticket_id = t.id and cqa.id = (SELECT id from '.models\TicketQueueAssignment::model()->tableName().' qa2 WHERE qa2.ticket_id = t.id order by qa2.created_date desc limit 1)';

            // build queue id list
            $queue_ids = array();
            if (@$filter_options['queue-ids']) {
                $queue_ids = $filter_options['queue-ids'];
                if (@$filter_options['closed-tickets']) {
                    // get all closed tickets regardless of whether queue is active or not
                    foreach (models\Queue::model()->closing()->findAll() as $closed_queue) {
                        $queue_ids[] = $closed_queue->id;
                    }
                }
            } else {
                if ($qs_svc->isQueueSetPermissionedForUser($queueset, Yii::app()->user->id)) {
                    foreach ($qs_svc->getQueueSetQueues(
                                $queueset,
                                @$filter_options['closed-tickets'] ? true : false) as $queue) {
                        $queue_ids[] = $queue->id;
                    }
                }
            }

            if (@$filter_options['my-tickets']) {
                $criteria->addColumnCondition(array('assignee_user_id' => Yii::app()->user->id));
            }
            if (@$filter_options['priority-ids']) {
                $criteria->addInCondition('priority_id', $filter_options['priority-ids']);
            }
            if (count($queue_ids)) {
                $criteria->addInCondition('cqa.queue_id', $queue_ids);
            }
            if (@$filter_options['firm-id']) {
                $criteria->addColumnCondition(array('cqa.assignment_firm_id' => $filter_options['firm-id']));
            } elseif (@$filter_options['subspecialty-id']) {
                $criteria->join .= 'JOIN '.\Firm::model()->tableName().' f ON f.id = cqa.assignment_firm_id JOIN '.\ServiceSubspecialtyAssignment::model()->tableName().' ssa ON ssa.id = f.service_subspecialty_assignment_id';
                $criteria->addColumnCondition(array('ssa.subspecialty_id' => $filter_options['subspecialty-id']));
            }
        }

        $criteria->order = 't.created_date desc';

        return array($criteria, $patient_filter);
    }

    /**
     * Generate a list of current tickets.
     */
    public function actionIndex()
    {
        unset(Yii::app()->session['patientticket_ticket_in_review']);
        AutoSaveTicket::clear();

        $cat_id = Yii::app()->request->getParam('cat_id', null);
        $queueset_id = Yii::app()->request->getParam('queueset_id', null);
        $select_queue_set = Yii::app()->request->getParam('select_queue_set', null);

        if (!$cat_id) {
            throw new \CHttpException(404, 'Category ID required');
        }

        if ($qs_id = $queueset_id && $select_queue_set) {
            $this->redirect(array("/PatientTicketing/default/?queueset_id=$qs_id&cat_id=".$cat_id));
        }

        if ($queueset_id) {
            $qs_id = $queueset_id;
        }

        $qsc_svc = Yii::app()->service->getService(self::$QUEUESETCATEGORY_SERVICE);

        if (!$category = $qsc_svc->readActive((int) $cat_id)) {
            throw new \CHttpException(404, 'Invalid category id');
        }

        $queueset = null;
        $tickets = null;
        $pages = null;
        $patient_filter = null;

        if ($queuesets = $qsc_svc->getCategoryQueueSetsForUser($category, Yii::app()->user->id)) {
            // default to the single queueset if that is all that is available to the user
            if (count($queuesets) > 1) {
                if ($qs_id) {
                    foreach ($queuesets as $qs) {
                        if ($qs->getID() == $qs_id) {
                            $queueset = $qs;
                            break;
                        }
                    }
                }
            } else {
                $queueset = $queuesets[0];
            }

            if ($queueset) {
                // build the filter
                $filter_keys = array('queue-ids', 'priority-ids', 'subspecialty-id', 'firm-id', 'my-tickets', 'closed-tickets');
                $filter_options = array();

                if (empty($_POST)) {
                    if (($filter_options = Yii::app()->session['patientticket_filter'])
                            && @$filter_options['category-id'] == $category->getID()) {
                        foreach ($filter_options as $k => $v) {
                            $_POST[$k] = $v;
                        }
                    }
                } else {
                    foreach ($filter_keys as $k) {
                        if (isset($_POST[$k])) {
                            $filter_options[$k] = $_POST[$k];
                        }
                    }
                    $filter_options['category-id'] = $category->getID();
                }

                Yii::app()->session['patientticket_filter'] = $filter_options;

                list($criteria, $patient_filter) = $this->buildTicketFilterCriteria($filter_options, $queueset);

                $count = models\Ticket::model()->count($criteria);
                $pages = new \CPagination($count);

                $pages->pageSize = $this->page_size;
                $pages->applyLimit($criteria);

                // get tickets that match criteria
                $tickets = models\Ticket::model()->findAll($criteria);
                \Audit::add('queueset', 'view', $queueset->getId());
            }
        }

        // render
        $this->render('index', array(
                'category' => $category,
                'queueset' => $queueset,
                'tickets' => $tickets,
                'patient_filter' => $patient_filter,
                'pages' => $pages,
                'cat_id' => $cat_id,
            ));
    }

    /**
     * Generates the form for assigning a Ticket to the given Queue.
     *
     * @param $id
     *
     * @throws \CHttpException
     */
    public function actionGetQueueAssignmentForm($id)
    {
        if (!$q = models\Queue::model()->findByPk($id)) {
            throw new \CHttpException(404, 'Invalid queue id.');
        }

        $qs_svc = Yii::app()->service->getService(self::$QUEUESET_SERVICE);
        $queueset = $qs_svc->getQueueSetForQueue($q->id);

        if (!$this->checkQueueSetProcessAccess($queueset)) {
            throw new \CHttpException(403, 'Not authorised to take ticket');
        }

        $template_vars = array('queue_id' => $id, 'patient_id' => null);
        $p = new \CHtmlPurifier();

        foreach (array('label_width' => 2, 'data_width' => 8) as $id => $default) {
            $template_vars[$id] = @$_GET[$id] ? $p->purify($_GET[$id]) : $default;
        }

        // if this is for a ticket, then we pass the patient id through for any event creation links
        if (@$_GET['ticket_id']) {
            if (!$ticket = models\Ticket::model()->findByPk((int) $_GET['ticket_id'])) {
                throw new \CHttpException(404, 'Invalid ticket id.');
            };
            $template_vars['patient_id'] = $ticket->patient_id;
        }

        $this->renderPartial('form_queueassign', $template_vars, false, false);
    }

    /**
     * Handles the moving of a ticket to a new Queue.
     *
     * @param $id
     *
     * @throws \CHttpException
     */
    public function actionMoveTicket($id)
    {
        if (!$ticket = models\Ticket::model()->with('current_queue')->findByPk($id)) {
            throw new \CHttpException(404, 'Invalid ticket id.');
        }

        $qs_svc = Yii::app()->service->getService(self::$QUEUESET_SERVICE);
        $queueset = $qs_svc->getQueueSetForTicket($ticket->id);

        if (!$this->checkQueueSetProcessAccess($queueset)) {
            throw new \CHttpException(403, 'Not authorised to take ticket');
        }

        foreach (array('from_queue_id', 'to_queue_id') as $required_field) {
            if (!@$_POST[$required_field]) {
                throw new \CHttpException(400, "Missing required form field {$required_field}");
            }
        }

        if ($ticket->current_queue->id != $_POST['from_queue_id']) {
            throw new \CHttpException(409, 'Ticket has already moved to a different queue');
        }

        if (!$to_queue = models\Queue::model()->active()->findByPk($_POST['to_queue_id'])) {
            throw new \CHttpException(404, "Cannot find queue with id {$_POST['to_queue_id']}");
        }

        $api = Yii::app()->moduleAPI->get('PatientTicketing');
        list($data, $errs) = $api->extractQueueData($to_queue, $_POST, true);

        if (count($errs)) {
            echo json_encode(array('errors' => array_values($errs)));
            Yii::app()->end();
        }

        $transaction = Yii::app()->db->beginTransaction();

        try {
            if ($to_queue->addTicket($ticket, Yii::app()->user, $this->firm, $data)) {
                if ($ticket->assignee) {
                    $ticket->assignee_user_id = null;
                    $ticket->assignee_date = null;
                    $ticket->save();
                }
                $ticket->audit('ticket', 'move', $ticket->id);
                $transaction->commit();
                $t_svc = Yii::app()->service->getService('PatientTicketing_Ticket');

                AutoSaveTicket::clear();

                $flsh_id = 'patient-ticketing-';

                $flsh_id .= $queueset->getId();
                if ($to_queue->outcomes) {
                    Yii::app()->user->setFlash($flsh_id, $t_svc->getCategoryForTicket($ticket)->name.' - '.$ticket->patient->getHSCICName().' moved to '.$to_queue->name);
                } else {
                    Yii::app()->user->setFlash($flsh_id, $t_svc->getCategoryForTicket($ticket)->name.' - '.$ticket->patient->getHSCICName().' completed');
                }
            } else {
                throw new Exception('unable to assign ticket to queue');
            }
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }

        $queueset_id = $queueset->getId();
        $queueset_model = models\QueueSet::model()->findByPk($queueset_id);
        $queueset_category_id = $queueset_model->category_id;
        echo json_encode(array('redirectURL' => "/PatientTicketing/default/?queueset_id=$queueset_id&cat_id=$queueset_category_id"));
    }

    /**
     * Handles the moving of a ticket to a new Queue.
     *
     * @param $id
     *
     * @throws \CHttpException
     */
    public function actionNavigateToEvent($id)
    {
        $data = $_POST;

        $response = '1';

        if (strpos(strtolower($data['href']), 'ophcocorrespondence/default/create') !== false) {
            if ($errs = $this->validateForm($id)) {
                $response = json_encode(array('errors' => array_values($errs)));
            } else {
                $data['validated'] = true;
            }
        }

        $this->autoSaveTicket($data);

        echo $response;
    }

    private static function autoSaveTicket($data)
    {
        unset($data['YII_CSRF_TOKEN']);
        unset($data['queue']);

        AutoSaveTicket::saveFormData($_POST['patient_id'], $_POST['from_queue_id'], $data);
    }

    public function validateForm($ticket_id)
    {
        if (!$ticket = models\Ticket::model()->with('current_queue')->findByPk($ticket_id)) {
            throw new \CHttpException(404, 'Invalid ticket id.');
        }

        if (!$to_queue = models\Queue::model()->active()->findByPk($_POST['to_queue_id'])) {
            throw new \CHttpException(404, "Cannot find queue with id {$_POST['to_queue_id']}");
        }

        $api = Yii::app()->moduleAPI->get('PatientTicketing');
        list($data, $errs) = $api->extractQueueData($to_queue, $_POST, true);

        return $errs;
    }

    /**
     * Generate individual row for the given Ticket id.
     *
     * @param $id
     *
     * @throws \CHttpException
     */
    public function actionGetTicketTableRow($id)
    {
        if (!$ticket = models\Ticket::model()->with('current_queue')->findByPk($id)) {
            throw new \CHttpException(404, 'Invalid ticket id.');
        }

        $qs_svc = Yii::app()->service->getService(self::$QUEUESET_SERVICE);
        $queueset = $qs_svc->getQueueSetForTicket($ticket->id);

        $can_process = $qs_svc->isQueueSetPermissionedForUser($queueset, Yii::app()->user->id);

        $this->renderPartial('_ticketlist_row', array(
                    'ticket' => $ticket,
                    'can_process' => $can_process,
                ), false, false);
    }

    /**
     * Generate history rows for the given Ticket id.
     *
     * @param $id
     *
     * @throws \CHttpException
     */
    public function actionGetTicketTableRowHistory($id)
    {
        if (!$ticket = models\Ticket::model()->with(array('queue_assignments', 'queue_assignments.queue'))->findByPk($id)) {
            throw new \CHttpException(404, 'Invalid ticket id.');
        }
        $ticket->audit('ticket', 'view-history', $ticket->id);

        $this->renderPartial('_ticketlist_history', array(
                    'ticket' => $ticket,
                    'assignments' => $ticket->getPastQueueAssignments(),
                ), false, false);
    }

    /**
     * Method to take ownership of a ticket for the current user.
     *
     * @param $id
     *
     * @throws \CHttpException
     */
    public function actionTakeTicket($id)
    {
        if (!$ticket = models\Ticket::model()->with('current_queue')->findByPk($_REQUEST['id'])) {
            throw new \CHttpException(404, 'Invalid ticket id.');
        }

        $qs_svc = Yii::app()->service->getService(self::$QUEUESET_SERVICE);
        $queueset = $qs_svc->getQueueSetForTicket($ticket->id);

        if (!$this->checkQueueSetProcessAccess($queueset)) {
            throw new \CHttpException(403, 'Not authorised to take ticket');
        }

        $resp = array('status' => null);

        if ($ticket->assignee_user_id) {
            $resp['status'] = 0;
            if ($ticket->assignee_user_id != Yii::app()->user->id) {
                $resp['message'] = 'Ticket has already been taken by '.$ticket->assignee->getFullName();
            } else {
                $resp['message'] = 'Ticket was already taken by you.';
            }
        } else {
            $ticket->assignee_user_id = Yii::app()->user->id;
            $ticket->assignee_date = date('Y-m-d H:i:s');
            if ($ticket->save()) {
                $resp['status'] = 1;
                $ticket->audit('ticket', 'take-ownership', $ticket->id);
            } else {
                $resp['status'] = 0;
                $resp['message'] = 'Unable to take ticket at this time.';
                Yii::log("Couldn't save ticket to take it: ".print_r($ticket->getErrors(), true), \CLogger::LEVEL_ERROR);
            }
        }
        echo \CJSON::encode($resp);
    }

    /**
     * Release a ticket from assignment.
     *
     * @param $id
     *
     * @throws \CHttpException
     */
    public function actionReleaseTicket($id)
    {
        if (!$ticket = models\Ticket::model()->with('current_queue')->findByPk($id)) {
            throw new \CHttpException(404, 'Invalid ticket id.');
        }

        $qs_svc = Yii::app()->service->getService(self::$QUEUESET_SERVICE);
        $queueset = $qs_svc->getQueueSetForTicket($ticket->id);

        if (!$this->checkQueueSetProcessAccess($queueset)) {
            throw new \CHttpException(403, 'Not authorised to take ticket');
        }

        $resp = array('status' => null);
        if (!$ticket->assignee_user_id) {
            $resp['status'] = 0;
            $resp['message'] = 'A ticket that is not owned cannot be released.';
        } elseif ($ticket->assignee_user_id != Yii::app()->user->id) {
            $resp['status'] = 0;
            $resp['message'] = "You cannot release a ticket you don't own.";
        } else {
            $ticket->assignee_user_id = null;
            $ticket->assignee_date = null;
            if ($ticket->save()) {
                $resp['status'] = 1;
                $ticket->audit('ticket', 'release', $ticket->id);
            } else {
                $resp['status'] = 0;
                $resp['message'] = 'Unable to release ticket at this time.';
                Yii::log("Couldn't save ticket to release it: ".print_r($ticket->getErrors(), true), \CLogger::LEVEL_ERROR);
            }
        }
        echo \CJSON::encode($resp);
    }

    /**
     * Check the current user is permissioned on the given queueset.
     *
     * @param $queueset
     *
     * @return mixed
     */
    public function checkQueueSetProcessAccess($queueset)
    {
        $qs_svc = Yii::app()->service->getService(self::$QUEUESET_SERVICE);

        return $qs_svc->isQueueSetPermissionedForUser($queueset, Yii::app()->user->id);
    }

    /**
     * Abstraction of managing session tracking of expanded tickets.
     *
     * @param $ticket
     * @param $expand
     * @param bool $unique
     */
    public function setTicketState($ticket, $expand, $unique = false)
    {
        if ($unique) {
            Yii::app()->session['patientticket_ticket_ids'] = array($ticket->id);

            return;
        }
        // add to the ticket list if it's not in there for expanding
        // remove from current list for collapsing
        $curr = Yii::app()->session['patientticket_ticket_ids'];
        if (!$curr) {
            $curr = array();
        }
        if ($expand) {
            $curr[] = $ticket->id;
        } else {
            $k = array_search($ticket->id, $curr);
            if ($k !== false) {
                unset($curr[$k]);
            }
        }
        // ensure that tickets are only referenced once, and that the list is zero indexed.
        Yii::app()->session['patientticket_ticket_ids'] = array_values(array_unique($curr));
    }

    /**
     * Action to mark the given ticket as being processed by the current user
     * Redirects to the appropriate page for the user to process the patient.
     *
     * @param $ticket_id
     *
     * @throws \CHttpException
     */
    public function actionStartTicketProcess($ticket_id)
    {
        if (!$ticket = models\Ticket::model()->findByPk($ticket_id)) {
            throw new \CHttpException(404, 'Invalid ticket id.');
        }

        // check the user is permissioned to process the queueset the ticket is on
        $qs_svc = Yii::app()->service->getService(self::$QUEUESET_SERVICE);
        $qs_r = $qs_svc->getQueueSetForTicket($ticket_id);
        if (!$qs_svc->isQueueSetPermissionedForUser($qs_r, Yii::app()->user->id)) {
            throw new \CHttpException(403, "User does not have permission to manage queueset for ticket id {$ticket_id}");
        }

        // set the patient to be in processing for the current user
        $this->setTicketState($ticket, true, true);

        //set session variable to display patient ticket banner
        Yii::app()->session['patientticket_ticket_in_review'] = array('ticket_id' => $ticket_id, 'patient_id' => $ticket->patient->id);

        // redirect to the appropriate page for the ticket processing.
        $this->redirect($ticket->getSourceLink());
    }

    /**
     * Set user to session to keep ticket expanded.
     *
     * @param $ticket_id
     *
     * @throws \CHttpException
     */
    public function actionExpandTicket($ticket_id)
    {
        if (!$ticket = models\Ticket::model()->findByPk($ticket_id)) {
            throw new \CHttpException(404, 'Invalid ticket id.');
        }
        $this->setTicketState($ticket, true);
    }

    /**
     * Set user to session to keep ticket collapsed.
     *
     * @param $ticket_id
     *
     * @throws \CHttpException
     */
    public function actionCollapseTicket($ticket_id)
    {
        if (!$ticket = models\Ticket::model()->findByPk((int) $ticket_id)) {
            throw new \CHttpException(404, 'Invalid ticket id.');
        }
        $this->setTicketState($ticket, false);
    }

    /**
     * Load the Patient Alert widget for the given patient.
     *
     * @param $patient_id
     *
     * @throws \CHttpException
     */
    public function actionGetPatientAlert($patient_id)
    {
        if (!$patient = \Patient::model()->findByPk((int) $patient_id)) {
            throw new \CHttpException(404, 'Invalid patient id.');
        }
        $patient->audit('patient', 'view-alert');

        $this->renderPartial('patientalert', array('patient' => $patient));
    }

    public function actionGetFirmsForSubspecialty()
    {
        if (!$subspecialty = \Subspecialty::model()->findByPk(@$_GET['subspecialty_id'])) {
            throw new Exception('Subspecialty not found: '.@$_GET['subspecialty_id']);
        }

        echo \CHtml::dropDownList('firm-id', '', \Firm::model()->getList($subspecialty->id), array('empty' => 'All firms'));
    }

    public function actionUndoLastStep($id)
    {
        if (!$ticket = models\Ticket::model()->findByPk($id)) {
            throw new \Exception("Ticket not found: $id");
        }

        $queue_assignments = $ticket->queue_assignments;
        $last_assignment = array_pop($queue_assignments);

        if (!$last_assignment->delete()) {
            throw new \Exception('Unable to remove ticket queue assignment: '.print_r($last_assignment->errors, true));
        }

        echo '1';
    }
}
