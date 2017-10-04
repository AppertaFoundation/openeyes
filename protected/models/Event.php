<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "event".
 *
 * The followings are the available columns in table 'event':
 *
 * @property string $id
 * @property string $episode_id
 * @property string $user_id
 * @property string $event_type_id
 * @property string $info
 * @property boolean $deleted
 * @property string $delete_reason
 * @property boolean $is_automated
 * @property array $automated_source - json structure
 * @property string $event_date
 * @property string $created_date
 * @property string $last_modified_date
 *
 * The followings are the available model relations:
 * @property Episode   $episode
 * @property User      $user
 * @property EventType $eventType
 */
class Event extends BaseActiveRecordVersioned
{
    private $defaultScopeDisabled = false;

    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className
     *
     * @return Event the static model class
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
        return 'event';
    }

    /**
     * Sets default scope for events such that we never pull back any rows that have deleted set to 1.
     *
     * @return array of mandatory conditions
     */
    public function defaultScope()
    {
        if ($this->defaultScopeDisabled) {
            return array();
        }

        $table_alias = $this->getTableAlias(false, false);

        return array(
            'condition' => $table_alias . '.deleted = 0',
        );
    }

    public function disableDefaultScope()
    {
        $this->defaultScopeDisabled = true;

        return $this;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_type_id, event_date', 'required'),
            array('parent_id', 'safe'),
            array('episode_id, event_type_id', 'length', 'max' => 10),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, episode_id, event_type_id, created_date, event_date, parent_id', 'safe', 'on' => 'search'),
            array('event_date', 'OEDateValidatorNotFuture'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'episode' => array(self::BELONGS_TO, 'Episode', 'episode_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'issues' => array(self::HAS_MANY, 'EventIssue', 'event_id'),
            'parent' => array(self::BELONGS_TO, 'Event', 'parent_id'),
            'children' => array(self::HAS_MANY, 'Event', 'parent_id'),
        );
    }

    /**
     * Make sure event date is set.
     */
    protected function afterConstruct()
    {
        $this->event_date = date('Y-m-d H:i:s');
        parent::afterConstruct();
    }

    protected function afterFind()
    {
        parent::afterFind();

        if ($this->is_automated) {
            $this->automated_source = json_decode($this->automated_source);
        }
    }

    protected function beforeSave()
    {
        if ($this->is_automated && !is_string($this->automated_source)) {
            $this->automated_source = json_encode($this->automated_source);
        }

        return parent::beforeSave();
    }

    /**
     * @return BaseAPI|null
     */
    public function getApi()
    {
        if ($this->eventType) {
            return Yii::app()->moduleAPI->get($this->eventType->class_name);
        }
    }

    public function moduleAllowsEditing()
    {
        if ($api = $this->getApi()) {
            if (method_exists($api, 'canUpdate')) {
                return $api->canUpdate($this->id);
            }
        }

        return;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'episode_id' => 'Episode',
            'created_user_id' => 'User',
            'event_type_id' => 'Event Type',
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
        $criteria->compare('episode_id', $this->episode_id, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('event_type_id', $this->event_type_id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Does this event have some kind of issue that the user should know about.
     *
     * @param string $type
     * @return bool
     */
    public function hasIssue($type = null)
    {
        if ($type === null) {
            return (boolean)$this->issues;
        }
        foreach ($this->issues as $event_issue) {
            if (strtolower($event_issue->issue->name) === strtolower($type)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the text for any issues on this event.
     *
     * @return string
     */
    public function getIssueText()
    {
        $text = '';

        foreach ($this->issues as $issue) {
            $text .= $this->expandIssueText($issue->text) . "\n";
        }

        return $text;
    }

    /**
     * parse out placeholders in the given text.
     *
     * @param $text
     *
     * @return mixed
     */
    public function expandIssueText($text)
    {
        if (preg_match_all('/\{(.*?)\}/', $text, $m)) {
            foreach ($m[0] as $i => $match) {
                if (preg_match('/^\{(.*?)\+([0-9]+)H:(.*)\}/', $match, $n)) {
                    $field = $n[1];
                    $hours = $n[2];
                    $dateformat = $n[3];

                    $text = str_replace($match, date($dateformat, strtotime($this->{$field}) + 3600 * $hours), $text);
                } elseif ($this->hasProperty($m[1][$i])) {
                    $text = str_replace($match, $this->{$m[1][$i]}, $text);
                }
            }
        }

        return $text;
    }

    /**
     * Add an issue to this event.
     *
     * @param $text
     *
     * @return bool
     */
    public function addIssue($text)
    {
        if (!$issue = Issue::model()->find('name=?', array($text))) {
            $issue = new Issue();
            $issue->name = $text;
            if (!$issue->save()) {
                return false;
            }
        }

        if (!EventIssue::model()->find('event_id=? and issue_id=?', array($this->id, $issue->id))) {
            $ei = new EventIssue();
            $ei->event_id = $this->id;
            $ei->issue_id = $issue->id;

            if (!$ei->save()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Remove an issue assignment for this event.
     *
     * @param $name
     *
     * @return bool
     */
    public function deleteIssue($name)
    {
        if (!$issue = Issue::model()->find('name=?', array($name))) {
            return false;
        }

        foreach (EventIssue::model()->findAll('event_id=? and issue_id = ?',
            array($this->id, $issue->id)) as $event_issue) {
            $event_issue->delete();
        }

        return true;
    }

    /**
     * Remove all issues assigned to this event.
     */
    public function deleteIssues()
    {
        foreach (EventIssue::model()->findAll('event_id=?', array($this->id)) as $event_issue) {
            $event_issue->delete();
        }
    }

    public function showDeleteIcon()
    {
        if ($api = $this->getApi()) {
            if (method_exists($api, 'showDeleteIcon')) {
                return $api->showDeleteIcon($this->id);
            }
        }

        return;
    }

    /**
     * Marks an event as deleted and processes any softDelete methods that exist on the elements attached to it.
     *
     * @throws Exception
     */
    public function softDelete($reason = false)
    {
        // perform this process in a transaction if one has not been created
        $transaction = Yii::app()->db->getCurrentTransaction() === null
            ? Yii::app()->db->beginTransaction()
            : false;

        try {
            $this->deleted = 1;
            $this->delete_pending = 0;

            if ($reason) {
                $this->delete_reason = $reason;
            }

            foreach ($this->getElements() as $element) {
                $element->softDelete();
            }
            if (!$this->save()) {
                throw new Exception('Unable to mark event deleted: ' . print_r($this->event->getErrors(), true));
            }
            if ($transaction) {
                $transaction->commit();
            }
        } catch (Exception $e) {
            if ($transaction) {
                $transaction->rollback();
            }
            throw $e;
        }
    }

    /**
     * Deletes issues for this event before calling the parent delete method
     * Does not handle the removal of elements and will therefore fail if this has not been handled before being called.
     *
     * @return bool
     *
     * @see parent::delete()
     */
    public function delete()
    {
        // Delete related
        EventIssue::model()->deleteAll('event_id = ?', array($this->id));

        return parent::delete();
    }

    /**
     * returns the latest event of this type in the event episode.
     *
     * @returns Event
     */
    public function getLatestOfTypeInEpisode()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'episode_id = :e_id AND event_type_id = :et_id';
        $criteria->limit = 1;
        $criteria->order = ' event_date DESC, created_date DESC';
        $criteria->params = array(':e_id' => $this->episode_id, ':et_id' => $this->event_type_id);

        return self::model()->find($criteria);
    }

    public function getPreviousInEpisode($eventTypeId = 0)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('episode_id = :e_id');
        $criteria->addCondition('event_date <= :event_date');
        $criteria->limit = 1;
        $criteria->order = ' event_date DESC, created_date DESC';
        $criteria->params = array(':e_id' => $this->episode_id, 'event_date' => $this->event_date);

        if ($eventTypeId) {
            $criteria->addCondition('event_type_id = :et_id');
            $criteria->params['et_id'] = $eventTypeId;
        }

        return self::model()->find($criteria);
    }

    /**
     * if this event is the most recent of its type in its episode, returns true. false otherwise.
     *
     * @returns boolean
     */
    public function isLatestOfTypeInEpisode()
    {
        $latest = $this->getLatestOfTypeInEpisode();

        return ($latest->id == $this->id) ? true : false;
    }

    /**
     * @param Event $event
     * @return bool
     */
    public function isAfterEvent(Event $event)
    {
        if ($this->event_date === $event->event_date) {
            return $this->created_date > $event->created_date;
        }
        return $this->event_date > $event->event_date;
    }

    /**
     * Sets various default properties for audit calls on this event.
     *
     * @param       $target
     * @param       $action
     * @param null  $data
     * @param bool  $log
     * @param array $properties
     */
    public function audit($target, $action, $data = null, $log = false, $properties = array())
    {
        $properties['event_id'] = $this->id;
        $properties['episode_id'] = $this->episode_id;
        $properties['patient_id'] = $this->episode->patient_id;

        parent::audit($target, $action, $data, $log, $properties);
    }

    /**
     * returns the saved elements that belong to the event if it has any.
     *
     * @return BaseEventTypeElement[]
     */
    public function getElements()
    {
        $elements = array();
        if ($this->id) {
            foreach ($this->eventType->getAllElementTypes() as $element_type) {
                $element_class = $element_type->class_name;

                foreach ($element_class::model()->findAll('event_id = ?', array($this->id)) as $element) {
                    $elements[] = $element;
                }
            }
        }

        return $elements;
    }

    public function requestDeletion($reason)
    {
        $this->delete_reason = $reason;
        $this->delete_pending = 1;

        if (!$this->save()) {
            throw new Exception('Unable to mark event as delete pending: ' . print_r($this->getErrors(), true));
        }

        $this->audit('event', 'delete-request', serialize(array(
            'requested_user_id' => $this->last_modified_user_id,
            'requested_datetime' => $this->last_modified_date,
        )));
    }

    public function isLocked()
    {
        return $this->delete_pending;
    }

    /**
     * Fetch a single element of the specified class for this event.
     *
     * @param $element_class
     */
    public function getElementByClass($element_class)
    {
        return $element_class::model()->find(
            array(
                'condition' => 'event_id = :event_id',
                'params' => array('event_id' => $this->id),
                'limit' => 1,
            )
        );
    }

    public function isEventDateDifferentFromCreated()
    {
        $evDate = new DateTime($this->event_date);
        $creDate = new DateTime($this->created_date);
        if ($creDate->format('Y-m-d') != $evDate->format('Y-m-d')) {
            return true;
        }

        return false;
    }

    public function getImageDirectory()
    {
        return Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'events' . DIRECTORY_SEPARATOR . "event_{$this->id}_" . strtotime($this->last_modified_date);
    }

    public function hasEventImage($name)
    {
        return file_exists($this->getImagePath($name));
    }

    public function getImagePath($name)
    {
        return $this->getImageDirectory() . DIRECTORY_SEPARATOR . "$name.png";
    }

    public function getPDF($pdf_print_suffix = null)
    {
        return $pdf_print_suffix ? "$this->imageDirectory" . DIRECTORY_SEPARATOR . "event_$pdf_print_suffix.pdf" : "$this->imageDirectory" . DIRECTORY_SEPARATOR . 'event.pdf';
    }

    public function hasPDF($pdf_print_suffix = null)
    {
        // Temporary fix related to OEM-281
        return false;
        $pdf = $this->getPDF($pdf_print_suffix);

        return file_exists($pdf) && filesize($pdf) > 0;
    }

    protected function getLockKey()
    {
        return "openeyes.event:$this->id";
    }

    public function lock()
    {
        $cmd = $this->dbConnection->createCommand('SELECT GET_LOCK(?, 1)');

        while (!$cmd->queryScalar(array($this->lockKey))) {
            ;
        }
    }

    public function unlock()
    {
        $this->dbConnection->createCommand('SELECT RELEASE_LOCK(?)')->execute(array($this->lockKey));
    }

    public function getBarCodeHTML()
    {
        $barcode = new TCPDFBarcode("E:$this->id", 'C128');

        return $barcode->getBarcodeHTML(1, 8);
    }

    public function getDocref()
    {
        return "E:$this->id/" . strtoupper(base_convert(time() . sprintf('%04d', Yii::app()->user->getId()), 10,
            32)) . '/{{PAGE}}';
    }

    /**
     * Returns the automated source text
     *
     * @return string
     */
    public function automatedText()
    {
        $result = '';
        if ($this->is_automated && $this->automated_source) {
            // TODO: this really should be in the module API with some kind of default text here
            if (property_exists($this->automated_source, 'goc_number')) {
                $result .= ' - Community optometric examination by ' . $this->automated_source->name . ' (' . $this->automated_source->goc_number . ')'. "<br>";

            }
            if(property_exists($this->automated_source, 'address')){
                $result .= 'Optometrist Address: '.$this->automated_source->address;
            }

            return $result;
        }
    }

    /**
     * @param EventType $event_type
     * @param Patient   $patient
     *
     * @return Event[]
     */
    public function getEventsOfTypeForPatient(EventType $event_type, Patient $patient)
    {
        $criteria = new CDbCriteria;
        $criteria->compare('patient_id', $patient->id);
        $criteria->compare('event_type_id', $event_type->id);
        $criteria->order = 'event_date asc';

        return Event::model()->with('episode')->findAll($criteria);
    }

    /**
     * @param string $type
     * @return string
     */
    public function getEventIcon($type = 'small')
    {
        if ($api = $this->getApi()) {
            if (method_exists($api, 'getEventIcon')) {
                return $api->getEventIcon($type, $this);
            }
        }

        if ($this->eventType) {
            return $this->eventType->getEventIcon($type, $this);
        }

        // TODO: add default images that can be returned
        return '';
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        if ($api = $this->getApi()) {
            if (method_exists($api, 'getEventName')) {
                return $api->getEventName($this);
            }
        }
        return $this->eventType ? $this->eventType->name : 'Event';
    }

    /**
     * Convenience function to retrieve the patient for event.
     *
     * @return Patient
     */
    public function getPatient()
    {
        if ($this->episode) {
            return $this->episode->patient;
        }
    }
}
