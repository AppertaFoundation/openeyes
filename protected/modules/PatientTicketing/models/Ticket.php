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

namespace OEModule\PatientTicketing\models;

use Yii;

/**
 * This is the model class for table "patientticketing_ticket".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $patient_id
 * @property int $priority_id
 * @property string $report
 * @property int $assignee_user_id
 * @property datetime $assignee_date
 * @property int $created_user_id
 * @property datetime $created_date
 * @property int $last_modified_user_id
 * @property datetime $last_modified_date
 * @property int $event_id
 * @property \Event $event
 * @property \Patient $patient
 * @property Priority $priority
 * @property \User $assignee
 * @property \User $user
 * @property \User $usermodified
 * @property TicketQueueAssignment[] queue_assignments
 * @property TicketQueueAssignment[] reverse_queue_assignments
 * @property TicketQueueAssignment initial_queue_assignment
 * @property TicketQueueAssignment current_queue_assignment
 * @property Queue current_queue
 */
class Ticket extends \BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return Ticket the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'patientticketing_ticket';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('patient_id', 'required'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'assignee' => array(self::BELONGS_TO, 'User', 'assignee_user_id'),
            'priority' => array(self::BELONGS_TO, 'OEModule\PatientTicketing\models\Priority', 'priority_id'),
            'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'queue_assignments' => array(
                self::HAS_MANY,
                'OEModule\PatientTicketing\models\TicketQueueAssignment',
                'ticket_id',
                'order' => 'queue_assignments.assignment_date asc',
            ),
            'reversed_queue_assignments' => array(
                self::HAS_MANY,
                'OEModule\PatientTicketing\models\TicketQueueAssignment',
                'ticket_id',
                'order' => 'reversed_queue_assignments.assignment_date desc',
            ),
            'initial_queue_assignment' => array(
                self::HAS_ONE,
                'OEModule\PatientTicketing\models\TicketQueueAssignment',
                'ticket_id',
                'order' => 'initial_queue_assignment.assignment_date',
            ),
            'current_queue_assignment' => array(
                self::HAS_ONE,
                'OEModule\PatientTicketing\models\TicketQueueAssignment',
                'ticket_id',
                'order' => 'current_queue_assignment.assignment_date desc',
            ),
            'initial_queue' => array(
                self::HAS_ONE,
                'OEModule\PatientTicketing\models\Queue',
                'queue_id',
                'through' => 'queue_assignments',
                'order' => 'queue_assignments.assignment_date asc',
            ),
            'current_queue' => array(
                self::HAS_ONE,
                'OEModule\PatientTicketing\models\Queue',
                'queue_id',
                'through' => 'reversed_queue_assignments',
                'order' => 'reversed_queue_assignments.assignment_date desc',
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'event_id' => 'Source Event',
            'priority_id' => 'Priority',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Get the URL to link to the source of the ticket.
     *
     * @return mixed
     */
    public function getSourceLink()
    {
        if ($this->event) {
            $qs = $this->initial_queue->queueset;
            if ($qs->summary_link) {
                return Yii::app()->createURL("/patient/summary/", array("id" => $this->patient_id));
            }

            return Yii::app()->createURL(
                '/' . $this->event->eventType->class_name . '/default/view/',
                array('id' => $this->event_id)
            );
        }

        return Yii::app()->createURL('/patient/view/', array('id' => $this->patient_id));
    }

    /**
     * Get the text to describe the source of this ticket.
     *
     * @return string
     */
    public function getSourceLabel()
    {
        if ($this->event) {
            if ($this->initial_queue_assignment->queue->summary_link) {
                return $this->initial_queue_assignment->assignment_firm->getSubspecialtyText() . ' Episode';
            }

            return $this->event->eventType->name;
        } else {
            return 'Patient';
        }
    }

    /**
     * Gets the firm that was being used when this ticket was created.
     *
     * @return string
     */
    public function getTicketFirm()
    {
        $ass = $this->initial_queue_assignment;

        return $ass->assignment_firm->name;
    }

    /**
     * Returns true if this ticket was previously in a different queue. False otherwise.
     *
     * @return bool
     */
    public function hasHistory()
    {
        return count($this->queue_assignments) > 1;
    }

    /*
     * returns true if ticket has any recorded queue assignments
     *
     * */
    public function hasRecordedQueueAssignments()
    {
        return !empty($this->queue_assignments);
    }

    /**
     * Get the past Queue Assignments for the ticket.
     *
     * @return TicketQueueAssignment[]
     */
    public function getPastQueueAssignments()
    {
        if ($ass_size = count($this->queue_assignments)) {
            return array_slice($this->queue_assignments, 0, $ass_size - 1);
        }

        return array();
    }

    /**
     * Gets an associative array of queues before and after the current queue for this ticket
     * Returned in the format $step => $queue where $step is the step number of the queue item
     *
     * @param int $stepsBeforeAndAfter If >= 0 then the number of queue items is restricted to only those within
     *                                 that many steps before and after this tickets "step"
     * @return Queue[]
     */
    public function getNearestQueuesInStepOrder($stepsBeforeAndAfter = -1)
    {
        // Get all queues this ticket has been assigned to ...
        /* @var \OEModule\PatientTicketing\models\Queue[] $queues */
        $queues = array_map(function (TicketQueueAssignment $qa) {
            return $qa->queue;
        }, $this->queue_assignments);

        /* @var \OEModule\PatientTicketing\models\Queue[] $stepToQueueMap */
        $stepToQueueMap = [];

        $stepNumber = 1;
        $pastCurrentQueue = false;
        $currentQueueStep = null;
        while (count($queues) > 0) {
            $queue = array_shift($queues);
            if (!$pastCurrentQueue && $queue->id === $this->current_queue->id) {
                $currentQueueStep = $stepNumber;
                $pastCurrentQueue = true;
            }

            // ... if we reach the end of the queues, then follow the outcomes until the end
            // If a queue has multiple outcomes, this will follow them breadth first, and show the output as a flat list
            if ($pastCurrentQueue) {
                foreach ($queue->outcomes as $outcome) {
                    $queues[] = $outcome->outcome_queue;
                }
            }
            $stepToQueueMap[$stepNumber] = $queue;

            $stepNumber++;
        }

        // Restrict to only queue items that are within $stepsBeforeAndAfter steps of the current step
        if ($stepsBeforeAndAfter >= 0) {
            $allowedKeys = array_filter(
                array_keys($stepToQueueMap),
                function ($key) use ($currentQueueStep, $stepsBeforeAndAfter) {
                    return abs($key - $currentQueueStep) <= $stepsBeforeAndAfter;
                }
            );

            return array_intersect_key($stepToQueueMap, array_flip($allowedKeys));
        }

        return $stepToQueueMap;
    }

    /**
     * Get a data structure containing information about this ticket.
     *
     * @param bool $json
     *
     * @return array|string
     */
    public function getInfoData($json = true)
    {
        $res = array(
            'id' => $this->id,
            'patient_name' => $this->patient->getFullName(),
            'current_queue_name' => $this->current_queue->name,
            'current_queue_id' => $this->current_queue->id,
            'patient_id' => $this->patient->id,
        );
        if ($json) {
            return \CJSON::encode($res);
        }

        return $res;
    }

    /**
     * Checks if the ticket is complete or not.
     *
     * @return bool
     */
    public function is_complete()
    {
        return count($this->current_queue->outcomes) == 0;
    }

    /**
     * Convenience function to access ticket notes.
     *
     * @return mixed
     */
    public function getNotes()
    {
        return $this->current_queue_assignment->notes;
    }

    /**
     * Convenience function to accese ticket report field.
     *
     * @return mixed
     */
    public function getReport($report_type = 'report')
    {
        foreach ($this->reversed_queue_assignments as $assignment) {
            if ($assignment->report) {
                return $assignment->$report_type;
            }
        }

        return '';
    }

    public function getFormattedReport()
    {
        return $this->getReport('formattedReport');
    }

    /**
     * @return Queue
     */
    public function getDisplayQueue()
    {
        $current_queue = $this->current_queue;

        if (!$service = Yii::app()->service->getService('PatientTicketing_QueueSet')) {
            throw new Exception('Service not found: PatientTicketing_QueueSet');
        }

        $queueset = $service->getQueueSetForQueue($current_queue->id);

        if ($queueset->default_queue) {
            foreach ($this->queue_assignments as $assignment) {
                if ($assignment->queue_id == $queueset->default_queue->getId()) {
                    return $queueset->default_queue;
                }
            }
        }

        return $current_queue;
    }

    /**
     * @return TicketQueueAssignment
     */
    public function getDisplayQueueAssignment()
    {
        $current_queue = $this->current_queue;

        if (!$service = Yii::app()->service->getService('PatientTicketing_QueueSet')) {
            throw new Exception('Service not found: PatientTicketing_QueueSet');
        }

        $queueset = $service->getQueueSetForQueue($current_queue->id);

        if ($queueset->default_queue) {
            foreach ($this->queue_assignments as $assignment) {
                if ($assignment->queue_id == $queueset->default_queue->getId()) {
                    return $assignment;
                }
            }
        }

        return $this->current_queue_assignment;
    }

    public function getFutureSteps()
    {
        $ticket_future_steps = [];
        $outcomes = $this->current_queue->outcomes;

        while (!empty($outcomes)) {
            if (count($outcomes) > 1) {
                foreach ($outcomes as $outcome) {
                    $ticket_future_steps['?'][] = $outcome->outcome_queue;
                }
                $outcomes = null;
            } elseif (count($outcomes) === 1) {
                $outcome_queue = $outcomes[0]->outcome_queue;
                $ticket_future_steps[] = [$outcome_queue];
                $outcomes = $outcome_queue->outcomes;
            }
        }

        return $ticket_future_steps;
    }

    public function afterDelete()
    {
        \FollowupAnalysisAggregate::updateForPatientTickets($this->patient_id, $this->id);

        return parent::afterDelete();
    }
}
