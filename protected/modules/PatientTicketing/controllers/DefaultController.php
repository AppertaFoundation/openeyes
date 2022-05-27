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

use CJavaScript;
use OEModule\PatientTicketing\components\AutoSaveTicket;
use OEModule\PatientTicketing\models;
use OEModule\PatientTicketing\services;
use Patient;
use Yii;

class DefaultController extends \BaseModuleController
{
    public $layout = '//layouts/main';
    public $renderPatientPanel = false;
    public bool $fixedHotlist = false;
    protected $page_size = 20;
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
                'actions' => array('index', 'getTicketTableRow', 'getTicketTableRowHistory', 'patientSearch'),
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

    protected function buildTicketFilterCriteria($filter_options, services\PatientTicketing_QueueSet $queueset, $sort_by = null, $sort_by_order = null)
    {
        $patient_filter = null;
        // build criteria
        $criteria = new \CDbCriteria();
        $params = array();
        $qs_svc = Yii::app()->service->getService(self::$QUEUESET_SERVICE);
        $criteria->with = ['event', 'patient.contact', 'priority'];
        $criteria->together = true;

        // TODO: we probably don't want to have such a gnarly approach to this, we might want to denormalise so that we are able to do eager loading
        // That being said, we might get away with setting together false on the with to do this filtering (multiple query eager loading).
        $criteria->join = 'JOIN ' . models\TicketQueueAssignment::model()->tableName() . ' cqa ON cqa.ticket_id = t.id and cqa.id = (SELECT id from ' . models\TicketQueueAssignment::model()->tableName() . ' qa2 WHERE qa2.ticket_id = t.id order by qa2.created_date desc limit 1)';

        // build queue id list
        $queue_ids = array();
        if (@$filter_options['queue-ids']) {
            $queue_ids = $filter_options['queue-ids'];
            if (@$filter_options['closed-tickets']) {
                // get all closed tickets from the queueset regardless of whether queue is active or not
                $rows = $qs_svc->getQueueSetClosingQueues($queueset);

                foreach ($rows as $row) {
                    $queue_ids[] = $row->id;
                }
            }
        } else {
            if ($qs_svc->isQueueSetPermissionedForUser($queueset, Yii::app()->user->id)) {
                foreach (
                    $qs_svc->getQueueSetQueues(
                        $queueset,
                        @$filter_options['closed-tickets'] ? true : false
                    ) as $queue
                ) {
                    $queue_ids[] = $queue->id;
                }
            }
        }

        if (@$filter_options['my-tickets']) {
            $criteria->addColumnCondition(array('assignee_user_id' => Yii::app()->user->id));
        }
        if (@$filter_options['priority-ids']) {
            $key = array_search("0", $filter_options['priority-ids']);
            if ($key !== false) {
                unset($filter_options['priority-ids'][$key]);
            }

            if (count($filter_options['priority-ids']) != 0) {
                $criteria->addInCondition('priority_id', $filter_options['priority-ids']);
            }
        }
        if (count($queue_ids)) {
            $criteria->addInCondition('cqa.queue_id', $queue_ids);
        }
        if (@$filter_options['firm-id']) {
            $criteria->addColumnCondition(array('cqa.assignment_firm_id' => $filter_options['firm-id']));
        } elseif (@$filter_options['subspecialty-id']) {
            $criteria->join .= 'JOIN ' . \Firm::model()->tableName() . ' f ON f.id = cqa.assignment_firm_id JOIN ' . \ServiceSubspecialtyAssignment::model()->tableName() . ' ssa ON ssa.id = f.service_subspecialty_assignment_id';
            $criteria->addColumnCondition(array('ssa.subspecialty_id' => $filter_options['subspecialty-id']));
        }
        if (isset($filter_options['patient-ids'])) {
            $criteria->addInCondition('patient_id', $filter_options['patient-ids']);
        }
        if (isset($filter_options['date-from']) && $filter_options['date-from']) {
            $date_from = new \DateTime($filter_options['date-from']);
            $date_from_timestamp = $date_from->getTimestamp();
            $criteria->addCondition('UNIX_TIMESTAMP(DATE(t.created_date)) >= :date_from_timestamp');
            $params[':date_from_timestamp'] = $date_from_timestamp;
        }
        if (isset($filter_options['date-to']) && $filter_options['date-to']) {
            $date_to = new \DateTime($filter_options['date-to']);
            $date_to_timestamp = $date_to->getTimestamp();
            $criteria->addCondition('UNIX_TIMESTAMP(DATE(t.created_date)) <= :date_to_timestamp');
            $params[':date_to_timestamp'] = $date_to_timestamp;
        }

        if (isset($filter_options['site-id']) && is_numeric($filter_options['site-id'])) {
            $criteria->addCondition('event.site_id = :site_id');
            $params[':site_id'] = $filter_options['site-id'];
        }

        $sort_by_order = (strtolower($sort_by_order) === 'desc') ? 'DESC' : '';

        switch ($sort_by) {
            case 'list':
                // I wasn't able to get this done using relations and conditions...
                // ->order = "current_queue.name ASC/DESC" does not bring the required(latest) queue_id from the assignment table
                $join = <<<SQLJOIN
                        JOIN patientticketing_ticketqueue_assignment ptta ON ptta.id = 
                        (
                            SELECT id 
                            FROM patientticketing_ticketqueue_assignment
                            WHERE patientticketing_ticketqueue_assignment.ticket_id = t.id
                            ORDER BY patientticketing_ticketqueue_assignment.assignment_date DESC
                            LIMIT 1
                        )
                        
                        JOIN patientticketing_queue q ON q.id = ptta.ticket_id
                SQLJOIN;

                $criteria->join .= new \CDbExpression($join);
                $criteria->order = "q.name $sort_by_order";

                break;
            case 'patient':
                $criteria->order = "contact.last_name {$sort_by_order}";
                break;
            case 'priority':
                $criteria->order = "priority.display_order {$sort_by_order}";
                break;
            case 'date':
            default:
                $criteria->order = "t.created_date {$sort_by_order}";
        }

        if (count($params)) {
            $criteria->params = array_merge($params, $criteria->params);
        }

        return $criteria;
    }

    public function actionPatientSearch($term)
    {
        $closed_tickets = \Yii::app()->request->getParam('closedTickets', '0');

        $queue_ids = [];
        if ($closed_tickets === '0') {
            $rows = Yii::app()->db->createCommand()
                ->select('patientticketing_queue.id, COUNT(oc.id) oc_ct')
                ->from('patientticketing_queue')
                ->leftJoin('patientticketing_queueoutcome oc', 'patientticketing_queue.id = oc.queue_id')
                ->group('patientticketing_queue.id')
                ->having('oc_ct = 0')
                ->queryAll();

            foreach ($rows as $row) {
                $queue_ids[] = $row['id'];
            }
        }

        $search = new \PatientSearch(false, true);
        $search_terms = $search->prepareSearch($term);

        $patient = new Patient();
        $patient->use_pas = false;

        $data_provider = $patient->search($search_terms);
        $criteria = $data_provider->getCriteria();

        $search_terms['terms_with_types'] = $search_terms['terms_with_types'] ?? [];

        if (!$search_terms['terms_with_types'] && !$search_terms['first_name'] && !$search_terms['last_name']) {
            // no name search and no types found to search in
            // we return no result
            $criteria->addCondition("1=0");
        }

        $criteria->distinct = true;

        $criteria->join .= ' JOIN patientticketing_ticket ticket ON ticket.patient_id = t.id';
        $criteria->join .= ' JOIN patientticketing_ticketqueue_assignment cqa ON cqa.ticket_id = ticket.id AND cqa.id = 
                                                            (	SELECT id 
                                                                FROM patientticketing_ticketqueue_assignment qa2
                                                                WHERE qa2.ticket_id = ticket.id 
                                                                ORDER BY qa2.created_date DESC LIMIT 1
                                                            )';

        if (count($queue_ids)) {
            $criteria->addNotInCondition('cqa.queue_id', $queue_ids);
        }

        $criteria->addCondition('t.is_deceased = 0');
        $data_provider->setCriteria($criteria);

        $result = [];
        foreach ($data_provider->getData(true) as $patient) {
            $pi = [];
            foreach ($patient->identifiers as $identifier) {
                $pi[] = [
                    'title' => $identifier->patientIdentifierType->long_title ?? $identifier->patientIdentifierType->short_title,
                    'value' => $identifier->value
                ];
            }

            $primary_identifier = \PatientIdentifierHelper::getIdentifierForPatient(
                SettingMetadata::model()->getSetting('display_primary_number_usage_code'),
                $patient->id,
                \Institution::model()->getCurrent()->id,
                Yii::app()->session['selected_site_id']
            );

            $result[] = array(
                'id' => $patient->id,
                'first_name' => $patient->first_name,
                'last_name' => $patient->last_name,
                'age' => ($patient->isDeceased() ? 'Deceased' : $patient->getAge()),
                'gender' => $patient->getGenderString(),
                'genderletter' => $patient->gender,
                'dob' => ($patient->dob) ? $patient->NHSDate('dob') : 'Unknown',
                'is_deceased' => $patient->is_deceased,
                'patient_identifiers' => $pi,
                'primary_patient_identifiers' => [
                    'title' => \PatientIdentifierHelper::getIdentifierPrompt($primary_identifier),
                    'value' => \PatientIdentifierHelper::getIdentifierValue($primary_identifier)
                ]
            );
        }

        echo CJavaScript::jsonEncode($result);
        Yii::app()->end();
    }

    /**
     * Generate a list of current tickets.
     */
    public function actionIndex()
    {
        unset(Yii::app()->session['patientticket_ticket_in_review']);
        AutoSaveTicket::clear();

        $cat_id = htmlspecialchars(Yii::app()->request->getParam('cat_id'));
        $qs_id = htmlspecialchars(Yii::app()->request->getParam('queueset_id'));
        $reset_filters = htmlspecialchars(Yii::app()->request->getParam('reset_filters', false));
        if ($reset_filters) {
            Yii::app()->session['patientticket_filter'] = [];
            unset($_GET['reset_filters']);
        }

        $unset_patientticketing = htmlspecialchars(Yii::app()->request->getParam('unset_patientticketing'));
        $patient_ids = Yii::app()->request->getParam('patient-ids', []);

        if ($unset_patientticketing === "true") {
            unset(Yii::app()->session['patientticket_ticket_ids']);
        }

        if (!$cat_id) {
            throw new \CHttpException(404, 'Category ID required');
        }

        $qsc_svc = Yii::app()->service->getService(self::$QUEUESETCATEGORY_SERVICE);

        if (!$category = $qsc_svc->readActive((int)$cat_id)) {
            throw new \CHttpException(404, 'Invalid category id');
        }

        $queueset = null;
        $tickets = null;
        $pagination = null;
        $patient_filter = null;

        $queuesets = $qsc_svc->getCategoryQueueSetsForUser($category, Yii::app()->user->id);
        if ($queuesets) {
            // default to the single queueset if that is all that is available to the user
            if (count($queuesets) > 1) {
                if ($qs_id) {
                    foreach ($queuesets as $qs) {
                        if ($qs->getID() == $qs_id) {
                            $queueset = $qs;
                            break;
                        }
                    }
                } else {
                    //multiple queueset, but none of them selected, let's load the first one
                    $queueset = $queuesets[0];
                }
            } else {
                $queueset = $queuesets[0];
            }

            if ($queueset) {
                // build the filter
                $filter_keys = array('queue-ids', 'priority-ids', 'subspecialty-id', 'firm-id', 'my-tickets', 'closed-tickets', 'patient-ids', 'date-from', 'date-to', 'site-id');
                $filter_options = array();

                foreach ($filter_keys as $k) {
                    if (!is_null($param = \Yii::app()->request->getParam($k, null))) {
                        $filter_options[$k] = $param;
                    }
                }

                if (empty($filter_options) && !empty(Yii::app()->session['patientticket_filter']) && !$reset_filters) {
                    $filter_options = Yii::app()->session['patientticket_filter'];
                    // Filter out queue-ids which do not belong to the queue set
                    if (isset($filter_options['queue-ids']) && $filter_options['queue-ids']) {
                        $qs_svc = Yii::app()->service->getService(self::$QUEUESET_SERVICE);
                        $queueset_queues = \CHtml::listData($qs_svc->getQueueSetQueues($queueset, false), 'id', 'name');
                        $cleaned_items = array_filter($filter_options['queue-ids'], function ($key) use ($queueset_queues) {
                            return array_key_exists($key, $queueset_queues);
                        });
                        if (empty($cleaned_items)) {
                            unset($filter_options['queue-ids']);
                        } else {
                            $filter_options['queue-ids'] = $cleaned_items;
                        }
                    }

                    $redir = array_merge(
                        ['/PatientTicketing/default'],
                        $filter_options,
                        ['cat_id' => $category->getID(), 'queueset_id' => $qs_id]
                    );
                    $this->redirect($redir);
                }

                Yii::app()->session['patientticket_filter'] = $filter_options;

                $filter_options['category-id'] = $category->getID();

                $sort_by = Yii::app()->getRequest()->getParam('sort_by');
                $sort_by_order = Yii::app()->getRequest()->getParam('sort_by_order', '');
                $criteria = $this->buildTicketFilterCriteria($filter_options, $queueset, $sort_by, $sort_by_order);

                $count = models\Ticket::model()->count($criteria);
                $pagination = new \CPagination($count);
                $pagination->pageSize = $this->page_size;
                $pagination->applyLimit($criteria);

                // get tickets that match criteria
                $tickets = models\Ticket::model()->findAll($criteria);
                \Audit::add('queueset', 'view', $queueset->getId());
            }
        }

        // render
        $this->pageTitle = 'Virtual Clinic';
        $this->render('index', array(
            'category' => $category,
            'queueset' => $queueset,
            'tickets' => $tickets,
            'patient_filter' => $patient_filter,
            'pagination' => $pagination,
            'cat_id' => $cat_id,
            'patients' => $patient_ids ? \Patient::model()->findAllByPk($patient_ids) : [],
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

        //anyone can process
        /*if (!$this->checkQueueSetProcessAccess($queueset)) {
            throw new \CHttpException(403, 'Not authorised to take ticket');
        }*/

        $template_vars = array('queue_id' => $id, 'patient_id' => null);
        $p = new \CHtmlPurifier();

        foreach (array('label_width' => 2, 'data_width' => 8) as $id => $default) {
            $template_vars[$id] = @$_GET[$id] ? $p->purify($_GET[$id]) : $default;
        }

        // if this is for a ticket, then we pass the patient id through for any event creation links
        $ticket_id =  \Yii::app()->request->getParam('ticket_id', null);
        if ($ticket_id) {
            if (!$ticket = models\Ticket::model()->findByPk((int)$ticket_id)) {
                throw new \CHttpException(404, 'Invalid ticket id.');
            };
            $template_vars['patient_id'] = $ticket->patient_id;
            AutoSaveTicket::saveFormData($ticket->patient_id, $ticket->current_queue->id, ['to_queue_id' => $q->id]);
        }

        $template_vars['episode_id'] = \Yii::app()->request->getParam('episode_id');


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
            $this->renderJSON(array('errors' => array_values($errs)));
            Yii::app()->end();
        }

        $transaction = Yii::app()->db->beginTransaction();

        try {
            if (isset($this->event)) {
                $ticket->event = $this->event;
            }
            if ($to_queue->addTicket($ticket, Yii::app()->user->id, $this->firm, $data)) {
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
                    Yii::app()->user->setFlash($flsh_id, $t_svc->getCategoryForTicket($ticket)->name . ' - ' . $ticket->patient->getHSCICName() . ' moved to ' . $to_queue->name);
                } else {
                    Yii::app()->user->setFlash($flsh_id, $t_svc->getCategoryForTicket($ticket)->name . ' - ' . $ticket->patient->getHSCICName() . ' completed');
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
        $this->renderJSON(array('redirectURL' => "/PatientTicketing/default/?queueset_id=$queueset_id&cat_id=$queueset_category_id"));
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
        /* @var models\Ticket $ticket */
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
                $resp['message'] = 'Ticket has already been taken by ' . $ticket->assignee->getFullName();
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
                Yii::log("Couldn't save ticket to take it: " . print_r($ticket->getErrors(), true), \CLogger::LEVEL_ERROR);
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
                Yii::log("Couldn't save ticket to release it: " . print_r($ticket->getErrors(), true), \CLogger::LEVEL_ERROR);
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
        if (!$ticket = models\Ticket::model()->findByPk((int)$ticket_id)) {
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
        if (!$patient = \Patient::model()->findByPk((int)$patient_id)) {
            throw new \CHttpException(404, 'Invalid patient id.');
        }
        $patient->audit('patient', 'view-alert');

        $this->renderPartial('patientalert', array('patient' => $patient));
    }

    public function actionGetFirmsForSubspecialty()
    {
        if (!$subspecialty = \Subspecialty::model()->findByPk(@$_GET['subspecialty_id'])) {
            throw new Exception('Subspecialty not found: ' . @$_GET['subspecialty_id']);
        }

        echo \CHtml::dropDownList(
            'firm-id',
            '',
            \Firm::model()->getList(Yii::app()->session['selected_institution_id'], $subspecialty->id),
            ['class' => 'cols-full', 'empty' => 'All ' . \Firm::contextLabel() . 's']
        );
    }

    public function actionUndoLastStep($id)
    {
        if (!$ticket = models\Ticket::model()->findByPk($id)) {
            throw new \Exception("Ticket not found: $id");
        }

        $queue_assignments = $ticket->queue_assignments;
        $last_assignment = array_pop($queue_assignments);

        if (!$last_assignment->delete()) {
            throw new \Exception('Unable to remove ticket queue assignment: ' . print_r($last_assignment->errors, true));
        }

        $this->renderJSON(['success' => true]);
    }
}
