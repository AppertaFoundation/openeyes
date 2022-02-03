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
 * @property string $worklist_patient_id
 * @property int $firm_id
 *
 * The followings are the available model relations:
 * @property Episode $episode
 * @property User $user
 * @property EventType $eventType
 * @property Institution $institution
 */
class Event extends BaseActiveRecordVersioned
{
    private $defaultScopeDisabled = false;
    protected $event_view_path = '/default/view';

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

    public function behaviors()
    {
        return array(
            'DisplayDeletedEventsBehavior' => 'DisplayDeletedEventsBehavior',
        );
    }

    protected function instantiate($attributes)
    {
        $deleted = !empty($attributes) ? intval($attributes['deleted']) : 0;
        if ($deleted) {
            $class = 'DeletedEvent';
        } else {
            $class = get_class($this);
        }
        $model = new $class(null);
        return $model;
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
        $this->displayDeletedEvents();
        if ($this->getDefaultScopeDisabled()) {
            return [];
        }

        $table_alias = $this->getTableAlias(false, false);

        return array(
            'condition' => $table_alias . '.deleted = 0',
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_type_id, event_date, institution_id', 'required'),
            array('parent_id, worklist_patient_id, institution_id, site_id', 'safe'),
            array('episode_id, event_type_id', 'length', 'max' => 10),
            array('worklist_patient_id', 'length', 'max' => 40),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, episode_id, event_type_id, created_date, event_date, parent_id, worklist_patient_id', 'safe', 'on' => 'search'),
            array('event_date', 'OEDateValidatorNotFuture', 'except' => 'allowFutureEvent'),
            array('event_date', 'eventDateValidator'),
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
            'previewImages' => array(self::HAS_MANY, 'EventImage', 'event_id'),
            'parent' => array(self::BELONGS_TO, 'Event', 'parent_id'),
            'children' => array(self::HAS_MANY, 'Event', 'parent_id'),
            'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
            'eventSubtypeItems' => array(self::HAS_MANY, 'EventSubTypeItem', 'event_id'),
            'firstEventSubtypeItem' => [self::HAS_ONE, 'EventSubTypeItem', 'event_id', 'order' => 'display_order'],
            'eventAttachmentGroups' => [self::HAS_MANY, 'EventAttachmentGroup', 'event_id'],
            'institution' => [self::BELONGS_TO, 'Institution', 'institution_id'],
            'site' => [self::BELONGS_TO, 'Site', 'site_id'],
        );
    }

    /**
     * Make sure event date is set.
     */
    protected function afterConstruct()
    {
        $this->event_date = date('Y-m-d H:i:s');

        // set default values here so we can use site/institution right after the object is ready
        // specially useful in event create page
        if ($this->isNewRecord && $this->scenario !== "automatic") {
            $selected_institution_id = Yii::app()->session->get('selected_institution_id');
            if (isset($selected_institution_id)) {
                $this->institution_id = $selected_institution_id;
                $this->site_id = Yii::app()->session->get('selected_site_id');
            }
        }

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
            'worklist_patient_id' => 'Worklist Patient Id',
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

        foreach (EventIssue::model()->findAll(
            'event_id=? and issue_id = ?',
            array($this->id, $issue->id)
        ) as $event_issue) {
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
     * @param bool $reason
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

            $this->onAfterSoftDelete(new CEvent($this));
        } catch (Exception $e) {
            if ($transaction) {
                $transaction->rollback();
            }
            throw $e;
        }
    }

    /**
     * Raising the afterSoftDelete event
     * @param $yii_event
     * @throws CException
     */
    public function onAfterSoftDelete($yii_event)
    {
        $this->raiseEvent('onAfterSoftDelete', $yii_event);
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
     * @param null $data
     * @param bool $log
     * @param array $properties
     */
    public function audit($target, $action, $data = null, $log = false, $properties = array())
    {
        $properties['event_id'] = $this->id;
        $properties['episode_id'] = $this->episode_id;
        $properties['patient_id'] = $this->episode->patient_id;
        $data = is_null($data)? 'Event Info: ' . $this->info : $data;
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
            /*
             * The following kludge exists to get around issues with class_exists and missing class files.
             * Yii promotes the warnings caused by include() on missing files to errors, such that calling
             * class_exists with autoload enabled can fail instead of merely returning false.
             *
             * Although class_exists can be passed a second parameter of false to disable autoload, this
             * will make it return false even for element classes that do exist because they are not yet
             * loaded in this context.
             *
             * While it is possible to use the @ suppression operator, this may cause other errors that
             * people want to see to be suppressed too.
             *
             * Since there is no get_error_handler, we instead have to use the return value of
             * set_error_handler, hence the strange setup for the closure.
             */

            // Set these up for use in the error handler closure
            $yii_err_handler = null;
            $element_class = null;

            // Temporarily install an error handler to deal with missing files.
            // Plese make sure the restore_error_handler below the loop is present while this exists.
            $yii_err_handler = set_error_handler(function($errno, $errstr, $errfile, $errline, $errcontext) use (&$yii_err_handler, &$element_class) {
                /*
                 * More kludging - we just want to look for errors where include failed to open the class file,
                 * for missing element classes.
                 *
                 * Everything else is passed on to the Yii error handler to prevent them from being lost.
                 */
                if (strpos($errstr, 'include') !== false && strpos($errstr, 'open') !== false) {
                    if (preg_match('/include\\((.+)\\):/', $errstr, $matches) === 1) {
                        Yii::log("Failed to find files for class $element_class in getElements: {$matches[1]}", 'Error');
                    }

                    return false;
                } else {
                    return call_user_func($yii_err_handler, $errno, $errstr, $errfile, $errline, $errcontext);
                }
            }, error_reporting());

            foreach ($this->eventType->getAllElementTypes() as $element_type) {
                $element_class = $element_type->class_name;

                if (class_exists($element_class)) {
                    foreach ($element_class::model()->findAll('event_id = ?', array($this->id)) as $element) {
                        $elements[] = $element;
                    }
                } else {
                    Yii::log("Failed to find class $element_class in getElements", 'Error');
                }
            }

            // This is needed to restore the Yii error handler and is the last line of the kludge.
            restore_error_handler();
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

    public function getImagePath($name, $extension = '.png')
    {
        return $this->getImageDirectory() . DIRECTORY_SEPARATOR . $name . $extension;
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

    public function getBarCodeSVG()
    {
        $barcode = new TCPDFBarcode("E:$this->id", 'C128');

        return $barcode->getBarcodeSVGCode(1, 8);
    }

    public function getDocref()
    {
        return "E:$this->id/" . strtoupper(
            base_convert(
                time() . sprintf('%04d', Yii::app()->user->getId()),
                10,
                32
            )
        ) . '/<span class="pageNumber"></span>';
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
                $result .= 'Community optometric examination by ' . $this->automated_source->name . ' (' . $this->automated_source->goc_number . ')' . "<br>";
            }
            if (property_exists($this->automated_source, 'address')) {
                $result .= 'Optometrist Address: ' . $this->automated_source->address;
            }

            return $result;
        }
    }

    /**
     * @param EventType $event_type
     * @param Patient $patient
     *
     * @return Event[]
     */
    public function getEventsOfTypeForPatient(EventType $event_type, Patient $patient)
    {
        $criteria = new CDbCriteria;
        $criteria->compare('patient_id', $patient->id);
        $criteria->compare('event_type_id', $event_type->id);
        $criteria->addCondition('t.deleted = 0');
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

        if ($this->firstEventSubtypeItem) {
            return $this->firstEventSubtypeItem->eventSubtype->display_name;
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

    /**
     * Validate the event date.
     */
    public function eventDateValidator($attribute, $param)
    {
        $event_date = Helper::mysqlDate2JsTimestamp($this->event_date);
        if (isset($this->episode)) {
            $episode = $this->episode;
            if (isset($episode->patient)) {
                $patient = $episode->patient;
                $event_date_limitation = Helper::mysqlDate2JsTimestamp($patient->dob) - (Helper::EPOCHMONTH * 9);
                if ($event_date < $event_date_limitation) {
                    $this->addError($attribute, 'The event date cannot be earlier than ' . date('Y-m-d', ($event_date_limitation / 1000)) . '.');
                }
            }
        }
    }

    /**
     * Return event path
     *
     * @return string
     */
    public function getEventViewPath()
    {
        return Yii::app()->createUrl($this->eventType->class_name . $this->event_view_path) . '/' . $this->id ;
    }

    /**
     * Return processed event date
     *
     * @return string
     */
    public function getEventDate()
    {
        return $this->event_date ? $this->NHSDateAsHTML('event_date') : $this->NHSDateAsHTML('created_date');
    }

    /**
     * Return a list of event li css
     *
     * @return array
     */
    public function getEventLiCss()
    {
        return array('event');
    }

    /**
     * Return event details
     *
     * @return array
     */
    public function getEventListDetails()
    {
        $criteria_event_image = new CDbCriteria();
        $criteria_event_image->with = ['status'];
        $criteria_event_image->compare('t.event_id', $this->id);
        $criteria_event_image->compare('status.name', 'CREATED');
        $criteria_event_image->order = 't.last_modified_date desc';
        return array(
            'event_path' => $this->getEventViewPath(),
            'event_name' => $this->getEventName(),
            'event_image' => EventImage::model()->find($criteria_event_image),
            'event_date' => $this->getEventDate(),
            'event_li_css' => $this->getEventLiCss(),
        );
    }

    /**
     * Setup special event icon, issue text and class
     * (Moved from _single_episode_sidebar view)
     *
     * @return array
     */
    public function getDetailedIssueText($event_icon_class, $event_issue_text, $event_issue_class)
    {
        $event_type = $this->eventType->class_name;
        $text = $event_issue_text;
        $class = $event_issue_class;
        switch ($event_type) {
            case 'OphTrOperationbooking':
                $operation_status_to_css_class = [
                    'Requires scheduling' => 'alert',
                    'Scheduled' => 'scheduled',
                    'Requires rescheduling' => 'alert',
                    'Rescheduled' => 'scheduled ',
                    'Cancelled' => 'cancelled',
                    'Completed' => 'done',
                    'On-Hold' => 'pause'
                    // extend this list with new statuses, e.g.:
                    // 'Reserved ... ' => 'flag', for OE-7194
                ];
                $operation = $this->getElementByClass('Element_OphTrOperationbooking_Operation');
                if ($operation) {
                    $status_name = $operation->status->name;
                    $css_class = $operation_status_to_css_class[$status_name];
                    $event_icon_class .= ' ' . $css_class;
                    if (!$this->hasIssue('Operation requires scheduling')) {
                        // this needs to be checked to avoid issue duplication, because the issue
                        // 'Operation requires scheduling' is saved to the database
                        // as an event issue, while the others are not
                        $class .= ' ' . $css_class;
                        $text .= 'Operation ' . $status_name . "\n";
                    }
                }
                break;
            case 'OphCoCorrespondence':
                $correspondence_email_status_to_css_class = [
                    'Sending' => 'scheduled',
                    'Complete' => 'done',
                    'Failed' => 'cancelled',
                    'Pending' => 'scheduled',
                ];
                $eventStatus = null;
                $emails = ElementLetter::model()->find(
                    'event_id = ?',
                    array($this->id)
                )->getOutputByType(['Email', 'Email (Delayed)']);
                // If there is a document output that has one of the two email delivery methods, only then proceed.
                if (count($emails) > 0) {
                    foreach ($emails as $email) {
                        if ($email->output_status === 'SENDING' || $email->output_status === 'PENDING') {
                            $eventStatus = "Pending";
                            continue;
                        }
                        if ($email->output_status === 'FAILED') {
                            $eventStatus = "Failed";
                            continue;
                        }
                    }
                    if (!isset($eventStatus)) {
                        $eventStatus = "Complete";
                    }
                    $css_class = $correspondence_email_status_to_css_class[$eventStatus];
                    $event_icon_class .= ' ' . $css_class;

                    $event_issue_class .= ' ' . $correspondence_email_status_to_css_class[$eventStatus];
                    $event_issue_text = $eventStatus;
                }
                break;
            case 'OphCoMessaging':
                $message_type_to_css_class = [
                    '0' => '',
                    '1' => 'urgent',
                ];
                $message = $this->getElementByClass('OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message');
                if ($message) {
                    $urgent_status = $message->urgent;
                    $css_class = $message_type_to_css_class[$urgent_status];
                    $event_icon_class .= ' ' . $css_class;
                    if ($urgent_status) {
                        $event_issue_class .= ' ' . $css_class;
                        $event_issue_text .= $message->message_type->name . "\n";
                    }
                }
                break;
        }
        return array(
            'event_icon_class' => $event_icon_class,
            'event_issue_class' => $class,
            'event_issue_text' => $text,
        );
    }
}
