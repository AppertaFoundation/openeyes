<?php

/**
 * OpenEyes.
 *
 * Copyright OpenEyes Foundation, 2021
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
use OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Risk_Status;
use OEModule\OphCiExamination\models\OphCiExamination_Triage_Priority;

class WorklistFilterQuery
{
    public const ALL_WORKLISTS = 'all';
    public const ALL_CONTEXTS = 'all';

    private const SORT_BY_TIME = 0;
    private const SORT_BY_NAME_A_Z = 1;
    private const SORT_BY_NAME_Z_A = 2;
    private const SORT_BY_WAIT_LONGEST = 3;
    private const SORT_BY_WAIT_SHORTEST = 4;
    private const SORT_BY_PRIORITY = 5;
    private const SORT_BY_DURATION = 6;

    private const AGE_UNDER_16 = 0;
    private const AGE_16_OR_OVER = 1;

    private const RED_FLAGS_SOME = 0;
    private const RED_FLAGS_NONE = 1;

    private const PATHWAY_STATE_SCHEDULED = 0;
    private const PATHWAY_STATE_ARRIVED = 1;
    private const PATHWAY_STATE_COMPLETED = 2;

    private const DURATION_RANGES = [
        0, 1, 3, 4, 6, 7
    ];

    private const PATHWAY_STEP_WAIT_TIME_VALUE = 'IF(pathway.end_time IS NULL AND (NVL(pswt.started_count, 0) = 0), NVL(pswt.step_wait, NOW() - pathway.start_time), NULL)';
    private const EARLIEST_UNCOMPLETED_STEP_QUERY = '(SELECT pathway_id, MIN(todo_order) AS first FROM pathway_step WHERE status IS NULL OR status < 1 GROUP BY pathway_id) AS earlier';

    private $site;
    private $context;
    private $firm;

    private $worklists;
    private $from;
    private $to;

    public $sortBy;
    public $optional;
    public $combined;

    private $quick;

    private $wait_time_query;

    private $priority_query;
    private $priority_values;

    private $risk_query;
    private $risk_values;

    // Preserve any supplied date period with a name (e.g. today, next week)
    // for later use in getJSONRepresentation so that it does not lossily return
    // absolute dates after the conversion in the constructor below.
    private $relative_period_name = null;

    public function __construct($filter = null, $quick = null)
    {
        if ($filter) {
            $filter = json_decode($filter);

            $this->site = $filter->site;
            $this->context = $filter->context;

            $this->worklists = $filter->worklists;

            if (isset($filter->period)) {
                if (getType($filter->period) === 'string') {
                    $this->relative_period_name = $filter->period;

                    $range = self::convertPeriodToDateRange($filter->period);

                    $this->from = $range['from'];
                    $this->to = $range['to'];
                } else {
                    $this->from = $filter->period->from;
                    $this->to = $filter->period->to;
                }
            } else {
                $this->from = null;
                $this->to = null;
            }

            $this->sortBy = $filter->sortBy;
            $this->optional = $filter->optional;

            $this->combined = $filter->combined;

            if (isset($filter->quick)) {
                $this->quick = $filter->quick;
            } else {
                $this->quick = (getType($quick) === 'string') ? json_decode($quick) : $quick;
            }
        } else {
            $this->site = Yii::app()->session['selected_site_id'];
            $this->context = self::ALL_CONTEXTS;

            $this->worklists = self::ALL_WORKLISTS;
            $this->relative_period_name = 'today'; // Default
            $this->from = null;
            $this->to = null;

            $this->sortBy = self::SORT_BY_TIME;
            $this->optional = [];

            $this->combined = false;

            $this->quick = (getType($quick) === 'string') ? json_decode($quick) : $quick;
        }

        if ($this->context === self::ALL_CONTEXTS) {
            $this->firm = Yii::app()->session->getSelectedFirm();
        } else {
            $this->firm = Firm::model()->with('serviceSubspecialtyAssignment.subspecialty')->findByPk($this->context);
        }

        if (!empty($this->quick) && !empty($this->quick->sortBy)) {
            $this->sortBy = $this->quick->sortBy;
        }

        // Cache a table of priority values for indexing later
        $this->priority_values = array_map(static function ($priority) {
            return $priority->id;
        }, OphCiExamination_Triage_Priority::model()->findAll());

        // Cache a table of risk values for indexing later
        $risk_criteria = new CDbCriteria();
        $risk_criteria->addInCondition('name', ['High', 'Medium', 'Low']);

        $this->risk_values = array_map(static function ($risk) {
            return $risk->id;
        },
        OphCiExamination_ClinicOutcome_Risk_Status::model()->findAll($risk_criteria));

        // Cache subqueries for wait time sorting and risk filtering
        $wait_time_command = Yii::app()->db->createCommand();
        $wait_time_command->select = 'pathway_id, SUM(status = ' . PathwayStep::STEP_STARTED .') AS started_count, MIN(NOW() - end_time) AS step_wait';
        $wait_time_command->from = 'pathway_step';
        $wait_time_command->group = 'pathway_id';

        $this->wait_time_query = '(' . $wait_time_command->text . ') AS pswt';

        $priority_command = Yii::app()->db->createCommand();
        $priority_command->select = 'patient_id, MAX(e.event_date) AS date';
        $priority_command->from = 'et_ophciexamination_triage tr';

        $priority_command->join('event e', 'tr.event_id = e.id');
        $priority_command->join('episode ep', 'episode_id = ep.id');

        $priority_command->group = 'patient_id';

        $this->priority_query = '(' . $priority_command->text . ') AS priority_event_dates';

        $discharge_status = \EpisodeStatus::model()->find('`key` = :key', array(':key' => 'discharged'));

        $risk_command = Yii::app()->db->createCommand();
        $risk_command->select = 'patient_id, MAX(e.event_date) AS date';
        $risk_command->from = 'et_ophciexamination_clinicoutcome oc';
        $risk_command->join('event e', 'oc.event_id = e.id');

        if ($discharge_status) {
            $risk_command->join('episode ep', 'episode_id = ep.id AND ep.episode_status_id != ' . $discharge_status->id);
        } else {
            $risk_command->join('episode ep', 'episode_id = ep.id');
        }

        $risk_command->join('ophciexamination_clinicoutcome_entry oce', 'oce.element_id = oc.id AND oce.risk_status_id IS NOT NULL');
        $risk_command->group = 'patient_id';

        $this->risk_query = '(' . $risk_command->text . ') AS risk_event_dates';
    }

    public function hasQuickFilter()
    {
        return $this->quick !== null && $this->quick->filter !== 'all';
    }

    public function getQuickFilterTypeName()
    {
        if ($this->quick === null) {
            return '';
        } elseif (getType($this->quick->filter) === 'string') {
            switch ($this->quick->filter) {
                case 'all':
                    return 'All';
                case 'clinic':
                    return 'Arrived';
                case 'issues':
                    return 'Issues';
                case 'discharged':
                    return 'Departed';
                case 'done':
                    return 'Completed';
            }
        } else {
            switch ($this->quick->filter->type) {
                case 'waitingFor':
                    return 'Waiting for ' . $this->quick->filter->value;
                case 'assignedTo':
                    return 'Assigned to ' . User::model()->findByPk($this->quick->filter->value)->getFullName();
            }
        }
    }

    public function getSiteId()
    {
        return $this->site;
    }

    public function priorityIsUsed()
    {
        return $this->firm->getSubspecialty()->getTreeName() === 'AE';
    }

    public function coversAllContexts()
    {
        return $this->context == self::ALL_CONTEXTS;
    }

    public function getContextId()
    {
        return $this->context;
    }

    public function coversAllWorklists()
    {
        return $this->worklists === self::ALL_WORKLISTS;
    }

    public function getWorklists()
    {
        return $this->worklists;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function getCombineWorklistsStatus()
    {
        return $this->combined;
    }

    public function getWorklistPatientsProvider($page_size, $worklist)
    {
        $command = Yii::app()->db->createCommand();

        $command->from('worklist_patient wp');
        $command->leftJoin('pathway', 'pathway.worklist_patient_id = wp.id');

        $conditions = array('and');
        $params = [];

        if (is_array($worklist)) {
            $worklists = array_map(static function ($worklist) {
                return $worklist->id;
            }, $worklist);

            $conditions[] = ['in', 'wp.worklist_id', $worklists];
        } else {
            $conditions[] = 'wp.worklist_id = :worklist_id';
            $params[':worklist_id'] = $worklist->id;
        }

        $this->getOptionalCriteria($command, $conditions, $params);

        if ($this->quick) {
            $this->getQuickFilterCriteria($command, $conditions, $params);
        }

        $command->where($conditions);
        $command->params = $params;

        // Total count needed for pagination
        $command->select('COUNT(DISTINCT wp.id)');

        $count = $command->queryScalar();

        // The main query
        $command->text = null;

        $sortBy = $this->getSortByCriteria($command);

        $command->select('wp.id');
        $command->order = $sortBy;

        $query = $command->text;

        return new CSqlDataProvider($query, array(
            'params' => $params,
            'sort' => array('attributes' => $sortBy),
            'totalItemCount' => $count,
            'pagination' => array('pageSize' => $page_size),
        ));
    }

    public function getPatientStatusCountsQuery($worklists)
    {
        $command = Yii::app()->db->createCommand();

        $command->from('worklist_patient wp');
        $command->select('pathway.status, COUNT(wp.id) AS count');
        $command->leftJoin('pathway', 'pathway.worklist_patient_id = wp.id');

        $conditions = array('and');
        $params = [];

        $worklists = array_map(static function ($worklist) {
            return $worklist->id;
        }, $worklists);

        $conditions[] = ['in', 'wp.worklist_id', $worklists];

        $this->getOptionalCriteria($command, $conditions, $params);

        $command->where($conditions);
        $command->params = $params;

        $command->group = 'pathway.status';

        return $command;
    }

    public function getWaitingForListQuery($worklists)
    {
        $command = Yii::app()->db->createCommand();

        $command->from('pathway_step ps');
        $command->select('ps.long_name, COUNT(earlier.first) AS count');

        $worklists = array_map(static function ($worklist) {
            return $worklist->id;
        }, $worklists);

        $worklist_patient_conditions = [
            'and',
            'wp.id = pathway.worklist_patient_id',
            ['in', 'wp.worklist_id', $worklists]
        ];

        $command->join('pathway', 'pathway.id = ps.pathway_id');
        $command->join('worklist_patient wp', $worklist_patient_conditions);

        $command->leftJoin(self::EARLIEST_UNCOMPLETED_STEP_QUERY, 'earlier.pathway_id = ps.pathway_id AND earlier.first = ps.todo_order');

        $conditions = array('and');
        $params = [];

        $this->getOptionalCriteria($command, $conditions, $params);

        $command->where($conditions);
        $command->params = $params;

        $command->group = 'ps.long_name';

        return $command;
    }

    public function getAssignedToListQuery($worklists)
    {
        $command = Yii::app()->db->createCommand();

        $command->from('user u');
        $command->select('u.id, first_name, last_name, COUNT(pathway.id) AS count');

        $worklists = array_map(static function ($worklist) {
            return $worklist->id;
        }, $worklists);

        $worklist_patient_conditions = [
            'and',
            'wp.id = pathway.worklist_patient_id',
            ['in', 'wp.worklist_id', $worklists]
        ];

        $command->join('pathway', 'pathway.owner_id = u.id');
        $command->join('worklist_patient wp', $worklist_patient_conditions);

        $conditions = array('and');
        $params = [];

        $this->getOptionalCriteria($command, $conditions, $params);

        $command->where($conditions);
        $command->params = $params;

        $command->group = 'u.id';

        return $command;
    }

    public function getJSONRepresentation()
    {
        $data = [
            'site' => $this->site,
            'context' => $this->context,
            'worklists' => $this->worklists,
            'sortBy' => $this->sortBy,
            'optional' => $this->optional,
            'combined' => $this->combined,
        ];

        $period = [];

        if ($this->relative_period_name) {
            $period = $this->relative_period_name;
        } elseif ($this->from) {
            $period['from'] = $this->from;
            $period['to'] = $this->to ?? '';
        } elseif ($this->to) {
            $period['from'] = '';
            $period['to'] = $this->to;
        }

        if (!empty($period)) {
            $data['period'] = $period;
        }

        return json_encode($data);
    }

    private function getSortByCriteria(&$command)
    {
        switch ($this->sortBy) {
            case self::SORT_BY_TIME:
                return 'wp.when';

            case self::SORT_BY_NAME_A_Z:
                $command->join('patient', 'patient.id = wp.patient_id');
                $command->join('contact', 'contact.id = patient.contact_id');
                return 'LOWER(contact.last_name), LOWER(contact.first_name)';

            case self::SORT_BY_NAME_Z_A:
                $command->join('patient', 'patient.id = wp.patient_id');
                $command->join('contact', 'contact.id = patient.contact_id');
                return 'LOWER(contact.last_name) DESC, LOWER(contact.first_name) DESC';

            case self::SORT_BY_WAIT_LONGEST:
                $command->join('patient', 'patient.id = wp.patient_id');
                $command->join('contact', 'contact.id = patient.contact_id');
                $command->leftJoin($this->wait_time_query, 'pswt.pathway_id = pathway.id');

                return self::PATHWAY_STEP_WAIT_TIME_VALUE . ' DESC, LOWER(contact.last_name), LOWER(contact.first_name)';

            case self::SORT_BY_WAIT_SHORTEST:
                $command->join('patient', 'patient.id = wp.patient_id');
                $command->join('contact', 'contact.id = patient.contact_id');
                $command->leftJoin($this->wait_time_query, 'pswt.pathway_id = pathway.id');

                return self::PATHWAY_STEP_WAIT_TIME_VALUE . ' ASC, LOWER(contact.last_name), LOWER(contact.first_name)';

            case self::SORT_BY_PRIORITY:
                $command->join('patient', 'patient.id = wp.patient_id');
                $command->join('contact', 'contact.id = patient.contact_id');

                if ($this->priorityIsUsed()) {
                    $command->leftJoin($this->priority_query . '_sort', 'wp.patient_id = priority_event_dates_sort.patient_id');
                    $command->leftJoin('event re_s', 're_s.event_date = priority_event_dates_sort.date');
                    $command->leftJoin('et_ophciexamination_triage etr_s', 'etr_s.event_id = re_s.id');
                    $command->leftJoin('ophciexamination_triage tr_s', 'tr_s.element_id = etr_s.id');

                    return 'tr_s.priority_id DESC, LOWER(contact.last_name), LOWER(contact.first_name)';
                } else {
                    $command->leftJoin($this->risk_query . '_sort', 'wp.patient_id = risk_event_dates_sort.patient_id');
                    $command->leftJoin('event re_s', 're_s.event_date = risk_event_dates_sort.date');
                    $command->leftJoin('et_ophciexamination_clinicoutcome oc_s', 'oc_s.event_id = re_s.id');
                    $command->leftJoin('ophciexamination_clinicoutcome_entry oce_s', 'oce_s.element_id = oc_s.id');

                    return 'oce_s.risk_status_id DESC, LOWER(contact.last_name), LOWER(contact.first_name)';
                }

            case self::SORT_BY_DURATION:
                $command->join('patient', 'patient.id = wp.patient_id');
                $command->join('contact', 'contact.id = patient.contact_id');
                return '(IFNULL(pathway.end_time, NOW()) - wp.when), LOWER(contact.last_name), LOWER(contact.first_name)';
        };
    }

    private function getOptionalCriteria(&$command, &$conditions, &$params)
    {
        foreach ($this->optional as $optional) {
            switch ($optional[0]) {
                case 'assignedTo':
                    $conditions[] = 'pathway.owner_id = :owner_id';
                    $params[':owner_id'] = $optional[1];
                    break;

                case 'steps':
                    $steps_conditions = ['and',
                                     'pathway_step.pathway_id = pathway.id',
                                     ['in', 'pathway_step.step_type_id', $optional[1]]];

                    $command->join('pathway_step', $steps_conditions);
                    break;

                case 'todo':
                    $todo_conditions = ['and',
                                    'pathway_step.pathway_id = pathway.id',
                                    ['or', 'pathway_step.status IS NULL', 'pathway_step.status = :pathway_step_requested'],
                                    ['in', 'pathway_step.step_type_id', $optional[1]]];

                    $params[':pathway_step_requested'] = PathwayStep::STEP_REQUESTED;
                    $command->join('pathway_step', $todo_conditions);
                    break;

                case 'ageRanges':
                    $command->join('patient arp', 'arp.id = wp.patient_id');

                    if ($optional[1] === self::AGE_UNDER_16) {
                        $conditions[] = 'TIMESTAMPDIFF(YEAR, arp.dob, IFNULL(arp.date_of_death, CURDATE())) < 16';
                    } else {
                        $conditions[] = 'TIMESTAMPDIFF(YEAR, arp.dob, IFNULL(arp.date_of_death, CURDATE())) >= 16';
                    }
                    break;

                case 'redFlags':
                    if ($optional[1] === self::RED_FLAGS_SOME) {
                        $command->join('event rfe', 'rfe.worklist_patient_id = wp.id');
                        $command->join('et_ophciexamination_ae_red_flags ae_rf', 'ae_rf.event_id = rfe.id');
                    } else {
                        $command->leftJoin('event rfe', 'rfe.worklist_patient_id = wp.id');
                        $command->leftJoin('et_ophciexamination_ae_red_flags ae_rf', 'ae_rf.event_id = rfe.id');
                        $conditions[] = ['and', 'event.id IS NULL', 'ae_rf.id IS NULL'];
                    }
                    break;

                case 'priorityOrRisk':
                    if ($this->priorityIsUsed()) {
                        $selected = array_map(function ($choice) {
                            return $this->priority_values[$choice];
                        }, $optional[1]);

                        $command->join($this->priority_query, 'wp.patient_id = priority_event_dates.patient_id');
                        $command->join('event re', 're.event_date = priority_event_dates.date');
                        $command->join('et_ophciexamination_triage etr', 'etr.event_id = re.id');
                        $command->join('ophciexamination_triage tr', 'tr.element_id = etr.id');

                        $conditions[] = ['in', 'tr.priority_id', $selected];
                    } else {
                        $selected = array_map(function ($choice) {
                            switch ($choice) {
                                case 0:
                                case 1:
                                    return $this->risk_values[0];

                                case 2:
                                    return $this->risk_values[1];

                                case 3:
                                case 4:
                                    return $this->risk_values[2];
                            }
                        }, $optional[1]);

                        $command->join($this->risk_query, 'wp.patient_id = risk_event_dates.patient_id');
                        $command->join('event re', 're.event_date = risk_event_dates.date');
                        $command->join('et_ophciexamination_clinicoutcome oc', 'oc.event_id = re.id');
                        $command->join('ophciexamination_clinicoutcome_entry oce', 'oce.element_id = oc.id');

                        $conditions[] = ['in', 'oce.risk_status_id', $selected];
                    }
                    break;

                case 'pathwayStates':
                    $statusValues = [];

                    foreach ($optional[1] as $state) {
                        switch ($state) {
                            case self::PATHWAY_STATE_SCHEDULED: // Later
                                $statusValues[] = Pathway::STATUS_LATER;
                                break;

                            case self::PATHWAY_STATE_ARRIVED: // Everything else (see Pathway::inProgressStatuses)
                                $statusValues = array_merge($statusValues, Pathway::inProgressStatuses());
                                break;

                            case self::PATHWAY_STATE_COMPLETED: // Discharged/Done
                                $statusValues[] = Pathway::STATUS_DISCHARGED;
                                $statusValues[] = Pathway::STATUS_DONE;
                                break;
                        }
                    }

                    if (count($statusValues) > 1) {
                        $conditions[] = ['in', 'pathway.status', $statusValues];
                    } else {
                        $conditions[] = 'pathway.status = ' . $statusValues[0];
                    }
                    break;

                case 'durations':
                    $duration_conditions = ['or'];

                    foreach ($optional[1] as $index) {
                        $range = self::DURATION_RANGES[$index];

                        $duration_conditions[] = 'HOUR(IFNULL(pathway.end_time, NOW()) - wp.when) = ' . $range;
                    }

                    $conditions[] = $duration_conditions;
                    break;
            }
        }
    }

    private function getQuickFilterCriteria(&$command, &$conditions, &$params)
    {
        if ($this->quick->filter !== 'all') {
            switch ($this->quick->filter) {
                case 'clinic':
                    $conditions[] = 'pathway.status <> ' . Pathway::STATUS_LATER;
                    break;

                case 'issues':
                    $conditions[] = ['in', 'pathway.status', [
                        Pathway::STATUS_STUCK,
                        Pathway::STATUS_WAITING,
                        Pathway::STATUS_DELAYED,
                        Pathway::STATUS_BREAK
                    ]];
                    break;

                case 'discharged':
                    $conditions[] = 'pathway.status = ' . Pathway::STATUS_DISCHARGED;
                    break;

                case 'done':
                    $conditions[] = 'pathway.status = ' . Pathway::STATUS_DONE;
                    break;

                default:
                    switch ($this->quick->filter->type) {
                        case 'waitingFor':
                            $params[':quick_step_long_name'] = $this->quick->filter->value;

                            $command->join('pathway_step',
                                   'pathway_step.pathway_id = pathway.id'.
                                   ' AND pathway_step.long_name = :quick_step_long_name');

                            $command->join(self::EARLIEST_UNCOMPLETED_STEP_QUERY,
                                           'earlier.pathway_id = pathway.id'.
                                           ' AND earlier.first = pathway_step.todo_order');
                            break;

                        case 'assignedTo':
                            $conditions[] = 'pathway.owner_id = :quick_owner_id';
                            $params[':quick_owner_id'] = $this->quick->filter->value;
                            break;
                    }
                    break;
            }
        }

        if (!empty($this->quick->patientName)) {
            $likeExpr = '%' . $this->quick->patientName . '%';

            $command->join('patient qp', 'qp.id = wp.patient_id');
            $command->join('contact qc', 'qc.id = qp.contact_id');

            $conditions[] = ['or', ['like', 'qc.first_name', $likeExpr], ['like', 'qc.last_name', $likeExpr]];
        }
    }

    private static function convertPeriodToDateRange($period)
    {
        $from = null;
        $to = null;

        switch ($period) {
            case 'yesterday':
                $from = new DateTime('today -1 days');
                break;

            case 'tomorrow':
                $from = new DateTime('today +1 days');
                break;

            case 'this-week':
                $from = new DateTime('today');
                $to = new DateTime('today +6 days');
                break;

            case 'next-week':
                $from = new DateTime('today +7 days');
                $to = new DateTime('today +13 days');
                break;

            case 'next-7-days':
                $from = new DateTime('today');
                $to = new DateTime('today +7 days');
                break;

            case 'today':
            default:
                $from = new DateTime('today');
                break;
        }

        return array(
            'from' => $from ? $from->format('Y-m-d') : '',
            'to' => $to ? $to->format('Y-m-d') : '');
    }

    public static function getLastUsedFilterFromSession()
    {
        $filter = null;
        $quick = !empty(Yii::app()->session['current_worklist_filter_quick']) ? Yii::app()->session['current_worklist_filter_quick'] : null;
        $type = 'New';
        $id = null;

        if (!empty(Yii::app()->session['current_worklist_filter_type']) && !empty(Yii::app()->session['current_worklist_filter_id'])) {
            $type = Yii::app()->session['current_worklist_filter_type'];
            $id = Yii::app()->session['current_worklist_filter_id'];

            if ($type === 'Saved') {
                $filter = \WorklistFilter::model()->findByPk($id);
            } elseif ($type === 'Recent') {
                $filter = \WorklistRecentFilter::model()->findByPk($id);
            }
        }

        $filter = $filter ? $filter->filter : null;

        return array('filter' => new WorklistFilterQuery($filter, $quick), 'quick' => $quick, 'type' => $type, 'id' => $id);
    }

    public static function setLatestUsedFilterForSession($type, $value)
    {
        if ($value !== null) {
            if ($type === 'Saved' || $type === 'Recent') {
                Yii::app()->session['current_worklist_filter_type'] = $type;
                Yii::app()->session['current_worklist_filter_id'] = (int)$value;
            } elseif ($type === 'Quick') {
                Yii::app()->session['current_worklist_filter_quick'] = $value;
            }
        }
    }
}
