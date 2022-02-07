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

namespace OEModule\PatientTicketing\components;

use OEModule\PatientTicketing\models\Queue;
use OEModule\PatientTicketing\models\Ticket;
use OEModule\PatientTicketing\models\TicketAssignOutcomeOption;
use Yii;

class PatientTicketing_API extends \BaseAPI
{
    public static $TICKET_SUMMARY_WIDGET = 'OEModule\PatientTicketing\widgets\TicketSummary';
    public static $QUEUE_ASSIGNMENT_WIDGET = 'OEModule\PatientTicketing\widgets\QueueAssign';
    public static $QUEUESETCATEGORY_SERVICE = 'PatientTicketing_QueueSetCategory';
    public static $TICKET_SERVICE = 'PatientTicketing_Ticket';

    /**
     * Returns the most recent followup value for a patient.
     *
     * @param $patient
     *
     * @return array|bool followup value or false if not present
     */
    public function getLatestFollowUp($patient)
    {
        $ticket_service = Yii::app()->service->getService(self::$TICKET_SERVICE);
        $tickets = $ticket_service->getTicketsForPatient($patient);

        foreach ($tickets as $ticket) {
            if ($follow_up = $this->getFollowUpFromAutoSave($patient->id, $ticket->current_queue->id)) {
                return $follow_up;
            } elseif ($follow_up = $this->getFollowUp($ticket->id)) {
                return $follow_up;
            }
        }

        return false;
    }

    public function getFollowUpFromAutoSave($patient_id, $current_queue_id)
    {
        if ($data = AutoSaveTicket::getFormData($patient_id, $current_queue_id)) {
            if (isset($data['validated']) && $data['validated']) {
                if (isset($data['patientticketing_glreview'])) {
                    return $data['patientticketing_glreview'];
                }
            }
        }
    }

    /**
     * Returns a followup value from a patient ticket if present.
     *
     * @param $ticket_id
     *
     * @return array|bool followup value or false if not present
     */
    public function getFollowUp($ticket_id)
    {
        if (!$ticket = Ticket::model()->findByPk((int) $ticket_id)) {
            return false;
        };

        if ($queue_assignments = $ticket->queue_assignments) {
            foreach ($queue_assignments as $queue_assignment) {
                $ticket_fields = json_decode($queue_assignment->details, true);
                if ($ticket_fields) {
                    foreach ($ticket_fields as $ticket_field) {
                        if (@$ticket_field['widget_name'] == 'TicketAssignOutcome') {
                            if (@isset($ticket_field['value']['outcome'])) {
                                if ($ticket_outcome_option = TicketAssignOutcomeOption::model()->findByPk((int) $ticket_field['value']['outcome'])) {
                                    if ($ticket_outcome_option->followup == 1) {
                                        $ticket_field['value']['assignment_date'] = $queue_assignment->assignment_date;
                                        return $ticket_field['value'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    public function getMenuItems($position = 1)
    {
        $result = array();

        $qsc_svc = Yii::app()->service->getService(self::$QUEUESETCATEGORY_SERVICE);
        $user = Yii::app()->user;
        foreach ($qsc_svc->getCategoriesForUser($user->id) as $qsc) {
            $result[] = array(
                    'uri' => '/PatientTicketing/default/?cat_id='.$qsc->id,
                    'title' => $qsc->name,
                    'position' => $position++,
            );
        };

        return $result;
    }

    /**
     * Simple function to standardise access to the retrieving the Queue Assignment Form.
     *
     * @return string
     */
    public function getQueueAssignmentFormURI()
    {
        return '/PatientTicketing/Default/GetQueueAssignmentForm/';
    }

    /**
     * @param $event
     *
     * @return mixed
     */
    public function getTicketForEvent($event)
    {
        if ($event->id) {
            return Ticket::model()->findByAttributes(array('event_id' => $event->id));
        }
    }

    /**
     * Filters and purifies passed array to get data relevant to a ticket queue assignment.
     *
     * @param \OEModule\PatientTicketing\models\Queue $queue
     * @param $data
     * @param bool $validate
     *
     * @return array
     */
    public function extractQueueData(Queue $queue, $data, $validate = false)
    {
        $result = array();
        $errors = array();
        $p = new \CHtmlPurifier();

        foreach ($queue->getFormFields() as $field) {
            $field_name = $field['form_name'];
            if (@$field['type'] == 'widget') {
                $class_name = 'OEModule\\PatientTicketing\\widgets\\'.$field['widget_name'];
                $widget = new $class_name();
                $widget->form_name = $field_name;
                $widget->label = $field['assignment_fields']['label'] ?? '';
                $form_name = $widget->fieldName ?? $field_name ?? null;

                if (isset($data[$form_name])) { // if widget is missing don't validate
                    $result[$form_name] = $widget->extractFormData($data[$form_name]);
                    if ($validate) {
                        $errors = array_merge($errors, $widget->validate($data[$form_name]));
                    }
                }
            } else {
                $result[$field_name] = $p->purify(@$data[$field_name]);
                if ($validate) {
                    if ($field['required'] && !@$data[$field_name]) {
                        $errors[$field_name] = $field['label'].' is required';
                    } elseif (@$field['choices'] && @$data[$field_name]) {
                        $match = false;
                        foreach ($field['choices'] as $k => $v) {
                            if ($data[$field_name] == $k) {
                                $match = true;
                                break;
                            }
                        }
                        if (!$match) {
                            $errors[$field_name] = $field['label'].': invalid choice';
                        }
                    }
                }
            }
        }

        if ($validate) {
            return array($result, $errors);
        } else {
            return $result;
        }
    }

    /**
     * @param \Event    $event
     * @param Queue     $initial_queue
     * @param \CWebUser $user
     * @param \Firm     $firm
     * @param $data
     *
     * @throws \Exception
     *
     * @return \OEModule\PatientTicketing\models\Ticket
     */
    public function createTicketForEvent(\Event $event, Queue $initial_queue, \CWebUser $user, \Firm $firm, $data)
    {

        $patient = $event->episode->patient;
        $ticket = $this->createTicketForPatient($patient, $initial_queue, $user, $firm, $data, $event);

        if (!$ticket) {
            throw new \Exception('Ticket was not created for an unknown reason');
        }

        return $ticket;
    }

    /*
     * @param Event $event
     * @param array $data
     */
    public function updateTicketForEvent(\Event $event)
    {
        if (!$ticket = $this->getTicketForEvent($event)) {
            throw new \Exception("Event has no ticket: $event->id");
        }
        $assignment = $ticket->initial_queue_assignment;

        // regenerate the report field on the ticket.
        $assignment->generateReportText();
        if (!$assignment->save()) {
            throw new \Exception('Unable to save queue assignment');
        }
    }

    /**
     * @param \Patient  $patient
     * @param Queue     $initial_queue
     * @param \CWebUser $user
     * @param \Firm     $firm
     * @param $data
     *
     * @throws \Exception
     *
     * @return \OEModule\PatientTicketing\models\Ticket
     */
    public function createTicketForPatient(\Patient $patient, Queue $initial_queue, \CWebUser $user, \Firm $firm, $data, \Event $event)
    {
        $transaction = Yii::app()->db->getCurrentTransaction() === null
                ? Yii::app()->db->beginTransaction()
                : false;

        try {
            $ticket = new Ticket();
            $ticket->patient_id = $patient->id;
            $ticket->created_user_id = $user->id;
            $ticket->last_modified_user_id = $user->id;
            $ticket->priority_id = $data['patientticketing__priority'];
            $ticket->event_id = $event->id;
            $ticket->save();
            $ticket->audit('ticket', 'create', $ticket->id);

            $initial_queue->addTicket($ticket, $user, $firm, $data);
            if ($transaction) {
                $transaction->commit();
            }

            return $ticket;
        } catch (\Exception $e) {
            if ($transaction) {
                $transaction->rollback();
            }
            throw $e;
        }
    }

    /**
     * Verifies that the provided queue id is an id for a Queue that the User can add to as the given Firm
     * At the moment, no verification takes place beyond the fact that the id is valid and active.
     *
     * @param \CWebUser $user
     * @param \Firm     $firm
     * @param int       $id
     */
    public function getQueueForUserAndFirm(\CWebUser $user, \Firm $firm, $id)
    {
        return Queue::model()->active()->findByPk($id);
    }

    /**
     * Returns the initial queues a patient ticket can be created against.
     *
     * @param \Firm $firm
     *
     * @return Queue[]
     */
    public function getInitialQueues(\Firm $firm)
    {
        $criteria = new \CDbCriteria();
        $criteria->addColumnCondition(array('is_initial' => true));

        return Queue::model()->active()->findAll($criteria);
    }

    /**
     * Returns the Queue Sets a patient ticket can be created in for the given firm.
     * (Note: firm filtering is not currently implemented).
     *
     * @param \Firm $firm
     *
     * @return mixed
     */
    public function getQueueSetList(\Firm $firm, \Patient $patient = null)
    {
        $qs_svc = Yii::app()->service->getService('PatientTicketing_QueueSet');
        $res = array();
        foreach ($qs_svc->getQueueSetsForFirm($firm) as $qs_r) {
            if ($patient && $qs_svc->canAddPatientToQueueSet($patient, $qs_r->getId())) {
                $res[$qs_r->initial_queue->getId()] = $qs_r->name;
            }
        }

        return $res;
    }

    /**
     * @param \Patient $patient
     * @param Queue    $queue
     *
     * @return mixed
     */
    public function canAddPatientToQueue(\Patient $patient, Queue $queue)
    {
        $qs_svc = Yii::app()->service->getService('PatientTicketing_QueueSet');
        $qs_r = $qs_svc->getQueueSetForQueue($queue->id);

        return $qs_svc->canAddPatientToQueueSet($patient, $qs_r->getId());
    }


    public function renderVirtualClinicSteps(Ticket $ticket)
    {
        $vc_steps = '';

        foreach ($ticket->queue_assignments as $step => $queue_assignment) {
            $is_completed = $queue_assignment->queue->id <= $ticket->current_queue->id;
            $is_current = $queue_assignment->queue->id === $ticket->current_queue->id;

            if ($is_completed) {
                $vc_steps .= "<li class='completed'<em> {$queue_assignment->assignment_user->getFullName()} </em></li>";
            }
            $li_class = $is_current ? 'selected' : ($is_completed ? 'completed' : '');
            $display_step = $step + 1;
            $vc_steps .= "<li class='{$li_class}'>{$display_step}. {$queue_assignment->queue->name}</li>";
        }

        $index = count($ticket->queue_assignments) + 1;

        foreach ($ticket->getFutureSteps() as $case => $futureSteps) {
            foreach ($futureSteps as $futureStep) {
                $step = $case === '?' ? $case : $index;
                $vc_steps .= "<li>{$step}. {$futureStep->name}<li>";
            }
        }

        return $vc_steps;
    }
}
