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

/**
 * This is the model class for table "patientticketing_queue".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property bool $active
 * @property string $report_definition
 * @property bool $is_initial
 * @property bool $summary_link - if true, tickets should link to the source event episode summary, rather than the event itself.
 * @property string assignment_fields
 * @property int $created_user_id
 * @property datetime $created_date
 * @property int $last_modified_user_id
 * @property datetime $last_modified_date
 * @property string $action_label
 *
 * The followings are the available model relations:
 * @property \User $user
 * @property \User $usermodified
 * @property \OEModule\PatientTicketing\models\QueueOutcome[] $outcomes
 * @property \OEModule\PatientTicketing\models\Queue[] $outcome_queues
 * @property \OEModule\PatientTicketing\models\QueueEventType[] $event_type_assignments
 * @property \EventType[] $event_types
 */
class Queue extends \BaseActiveRecordVersioned
{
    // used to prevent form field name conflicts
    public static $FIELD_PREFIX = 'patientticketing_';

    protected $auto_update_relations = true;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphTrOperationnote_GlaucomaTube_PlatePosition the static model class
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
        return 'patientticketing_queue';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name', 'required'),
            array('assignment_fields', 'validJSON'),
            array('name, description, active, action_label, summary_link, assignment_fields, report_definition, event_types', 'safe'),
            array('id, name', 'search'),
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
            'outcomes' => array(self::HAS_MANY, 'OEModule\PatientTicketing\models\QueueOutcome', 'queue_id'),
            'all_outcome_queues' => array(self::HAS_MANY, 'OEModule\PatientTicketing\models\Queue', 'outcome_queue_id', 'through' => 'outcomes'),
            'outcome_queues' => array(self::HAS_MANY, 'OEModule\PatientTicketing\models\Queue', 'outcome_queue_id', 'through' => 'outcomes', 'on' => 'outcome_queues.active = 1'),
            'event_type_assignments' => array(self::HAS_MANY, 'OEModule\PatientTicketing\models\QueueEventType', 'queue_id', 'order' => 'display_order asc'),
            'event_types' => array(self::HAS_MANY, 'EventType', 'event_type_id', 'through' => 'event_type_assignments', 'order' => 'display_order asc'),
        );
    }

    public function behaviors()
    {
        return array(
                'LookupTable' => 'LookupTable',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    /**
     * A scope to only return queues that have active outcomes.
     * NOTE: didn't seem to be able to do this with relation definitions.
     *
     * @return $this
     */
    public function notClosing()
    {
        $criteria = new \CDbCriteria();
        $criteria->select = $this->getTableAlias(true).'.*, count(oc.id)';
        $criteria->join = 'left outer join patientticketing_queueoutcome oc on '.$this->getTableAlias(true).
                '.id = oc.queue_id left join patientticketing_queue oq on oc.outcome_queue_id = oq.id';
        $criteria->condition = 'oq.active = 1';
        $criteria->group = $this->getTableAlias(true).'.id';

        $this->getDbCriteria()->mergeWith($criteria);

        return $this;
    }

    /**
     * A scope to return queues that don't have outcomes.
     *
     * @return $this
     */
    public function closing()
    {
        $criteria = new \CDbCriteria();
        $criteria->select = $this->getTableAlias(true).'.*, count(oc.id) oc_ct';
        $criteria->join = 'left outer join patientticketing_queueoutcome oc on '.$this->getTableAlias(true).
                '.id = oc.queue_id';
        $criteria->group = $this->getTableAlias(true).'.id';
        $criteria->having = 'oc_ct = 0';
        $this->getDbCriteria()->mergeWith($criteria);

        return $this;
    }

    /**
     * Checks the given attribute is valid JSON.
     *
     * @param $attribute
     */
    public function validJSON($attribute)
    {
        if ($this->$attribute) {
            $array = \CJSON::decode($this->$attribute);
            if (!$array) {
                $this->addError($attribute, $this->getAttributeLabel($attribute).' must be valid JSON');
            } else {
                // valid JSON
                $ids = array_column($array, 'id');
                $duplicates = array_diff_key($ids, array_unique(array_map('strtolower', $ids)));

                if ($duplicates) {
                    $this->addError($attribute, '"id" must be unique');
                }
            }
        }
    }

    /**
     * assignment field validation.
     */
    public function afterValidate()
    {
        // validate any widget fields in the assignment_fields attribute
        foreach ($this->getAssignmentFieldDefinitions() as $i => $fld) {
            if (!$id = @$fld['id']) {
                $this->addError('assignment_fields', 'ID required for assignment field '.($i + 1));
                continue;
            }
            if (@$fld['type'] == 'widget') {
                if (!@$fld['widget_name']) {
                    $this->addError('assignment_fields', 'Widget Name missing for '.$id);
                } elseif (!is_file(\Yii::getPathOfAlias('application.modules.PatientTicketing.widgets.'.$fld['widget_name']).'.php')) {
                    $this->addError('assignment_fields', 'Widget with name '.$fld['widget_name'].' for '.$id.' not defined');
                }
            }
        }

        parent::afterValidate();
    }

    /**
     * Add the given ticket to the Queue for the user and firm.
     *
     * @param Ticket    $ticket
     * @param int       $user_id
     * @param \Firm     $firm
     * @param $data
     */
    public function addTicket(Ticket $ticket, $user_id, \Firm $firm, $data, $automatically_created = false)
    {
        $assignment = new TicketQueueAssignment();
        $assignment->queue_id = $this->id;
        $assignment->ticket_id = $ticket->id;
        $assignment->assignment_user_id = $user_id;
        $assignment->assignment_firm_id = $firm->id;
        $assignment->assignment_date = date('Y-m-d H:i:s');
        $assignment->notes = $data[self::$FIELD_PREFIX.'_notes'] ?? null;

        // store the assignment field values to the assignment object.
        if ($assignment_fields = $this->getAssignmentFieldDefinitions()) {
            $details = array();
            foreach ($assignment_fields as $assignment_field) {
                $store = array('id' => $assignment_field['id']);
                if (@$assignment_field['type'] == 'widget') {
                    // store the widget for later data manipulation
                    $store['widget_name'] = $assignment_field['widget_name'];
                    // post processing handling
                    $class_name = 'OEModule\\PatientTicketing\\widgets\\'.$assignment_field['widget_name'];
                    $widget = new $class_name();
                    $widget->form_name = $assignment_field['form_name'];
                    $widget->assignment_field = $assignment_field;
                    $widget->ticket = $ticket;
                    $widget->queue = $this;

                    $field_name = $widget->fieldName ?? $assignment_field['form_name'] ?? null;
                    $val = $data[$field_name] ?? null;
                    if ($val) {
                        $widget->processAssignmentData($ticket, $val);
                    }
                } elseif (@$assignment_field['choices']) {
                    if ($val = @$data[$assignment_field['form_name']]) {
                        foreach ($assignment_field['choices'] as $k => $v) {
                            if ($k == $val) {
                                $val = $v;
                                break;
                            }
                        }
                    }
                }
                $store['value'] = $val;
                $details[] = $store;
            }
            $assignment->details = json_encode($details);
        }

        //If ticket is created from a console command  we cannot generate report text as it requires rendering HTML
        // which is only possible if we have a controller instantiated
        if(!$automatically_created) {
            // generate the report field on the ticket.
            $assignment->generateReportText();
        } else {
            $assignment->notes = "Report unavailable as ticket has been created automatically.";
        }

        if (!$assignment->save()) {
            throw new \Exception('Unable to save queue assignment' . print_r($assignment->getErrors(), true));
        }

        \FollowupAnalysisAggregate::updateForPatientTickets($ticket->patient_id, $ticket->id);

        return true;
    }

    /**
     * Get simple data structure of possible outcomes for this Queue.
     *
     * @param bool $json
     *
     * @return array|string
     */
    public function getOutcomeData($json = true)
    {
        $res = array();
        foreach ($this->outcome_queues as $q) {
            $res[] = array('id' => $q->id, 'name' => $q->name);
        }
        if ($json) {
            return \CJSON::encode($res);
        }

        return $res;
    }

    public function getRelatedEventTypes($json = true)
    {
        $event_types = array();

        foreach ($this->outcome_queues as $queue) {
            $event_types[$queue->id] = array();

            foreach ($queue->event_types as $event_type) {
                $event_types[$queue->id][] = array(
                    'name' => $event_type->name,
                    'class_name' => $event_type->class_name,
                );
            }
        }

        if ($json) {
            return \CJSON::encode($event_types);
        }

        return $event_types;
    }

    /**
     * Returns the fields that have been defined for this Queue when a ticket is assigned to it.
     *
     * @return array
     */
    protected function getAssignmentFieldDefinitions()
    {
        $flds = array();
        if ($ass_fields = \CJSON::decode($this->assignment_fields)) {
            foreach ($ass_fields as $ass_fld) {
                $flds[] = array(
                        'id' => @$ass_fld['id'],
                        'form_name' => self::$FIELD_PREFIX.@$ass_fld['id'],
                        'required' => @$ass_fld['required'],
                        'type' => @$ass_fld['type'],
                        'widget_name' => @$ass_fld['widget_name'],
                        'label' => @$ass_fld['label'],
                        'choices' => @$ass_fld['choices'],
                        'assignment_fields' => $ass_fld
                );
            }
        }

        return $flds;
    }

    /**
     * Function to return a list of the fields that we are expecting an assignment form to contain for this queue.
     *
     * @return array(array('id' => string, 'required' => boolean, 'choices' => array(), 'label' => string, 'type' => string))
     */
    public function getFormFields()
    {
        $flds = array();

        // priority and notes are reserved fields and so get additional _ prefix for the field name
        if ($this->is_initial) {
            $flds[] = array(
                'id' => '_priority',
                'form_name' => self::$FIELD_PREFIX.'_priority',
                'required' => $this->getQueueSet()->allow_null_priority ? false : true,
                'choices' => \CHtml::listData(Priority::model()->findAll(), 'id', 'name'),
                'label' => 'Priority',
            );
        }
        $flds[] = array(
            'id' => '_notes',
            'form_name' => self::$FIELD_PREFIX.'_notes',
            'required' => false,
            'type' => 'textarea',
            'label' => 'Notes', );

        return array_merge($flds, $this->getAssignmentFieldDefinitions());
    }

    /**
     * Helper method for handling the construction of the root node set.
     *
     * @param $root
     * @param $candidates
     *
     * @return array
     */
    private function mergeRootQueues($root, $candidates)
    {
        if (!is_array($candidates)) {
            $candidates = array($candidates);
        }
        foreach ($candidates as $c) {
            $seen = false;
            foreach ($root as $r) {
                if ($r->id == $c->id) {
                    $seen = true;
                    break;
                }
            }
            if (!$seen) {
                $root[] = $c;
            }
        }

        return $root;
    }

    /**
     * Get the root queue for this particular queue
     * (Note: this assumes that all queues are only in one path, if that model changes this will not work).
     *
     * @return Queue[]|Queue
     */
    public function getRootQueue()
    {
        if ($this->is_initial) {
            return $this;
        }
        $root = array();
        foreach (QueueOutcome::model()->findAllByAttributes(array('outcome_queue_id' => $this->id)) as $qo) {
            $q = $qo->queue->getRootQueue();
            $root = $this->mergeRootQueues($root, $q);
        }

        if (count($root) == 1) {
            return $root[0];
        }

        return $root;
    }

    /**
     * Get the number of tickets that are currently assigned to this queue.
     *
     * @return array
     */
    public function getCurrentTicketCount()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('queue_assignments.id = (select id from '.TicketQueueAssignment::model()->tableName().' cass where cass.ticket_id = t.id order by cass.assignment_date desc limit 1) and queue_assignments.queue_id = :qid');
        $criteria->params = array(':qid' => $this->id);

        return Ticket::model()->with('queue_assignments')->count($criteria);
    }

    /**
     * Get the ids of queues that depend solely on this queue for tickets to be assigned to them.
     *
     * @param array $dependent_ids
     *
     * @return array
     */
    public function getDependentQueueIds($dependent_ids = array())
    {
        $dependents = array();
        foreach ($this->outcome_queues as $oc) {
            $criteria = new \CDbCriteria();
            $criteria->addNotInCondition('queue_id', array_unique(array_merge($dependent_ids, array($this->id))));
            $criteria->addColumnCondition(array('outcome_queue_id' => $oc->id));

            $ct = QueueOutcome::model()->count($criteria);
            if ($ct == 0) {
                $dependents[] = $oc;
                $dependent_ids[] = $oc->id;
            }
        }
        foreach ($dependents as $dependent) {
            $dependent_ids = array_unique(array_merge($dependent_ids, $dependent->getDependentQueueIds($dependent_ids)));
        }

        return $dependent_ids;
    }

    /**
     * Get the QueueSet this Queue belongs to.
     *
     * @return QueueSet
     *
     * @throws \Exception
     */
    public function getQueueSet()
    {
        $root = $this->getRootQueue();
        if (is_array($root)) {
            throw new \Exception('Unexpected configuration of multiple root queues'.print_r($root, true));
            $rid = $root[0]->id;
        } else {
            $rid = $root->id;
        }

        return QueueSet::model()->findByAttributes(array('initial_queue_id' => $rid));
    }
}
