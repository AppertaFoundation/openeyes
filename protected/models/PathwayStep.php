<?php

/**
 * This is the model class for table "pathway_step".
 *
 * The followings are the available columns in table 'pathway_step':
 * @property int $id
 * @property int $pathway_id
 * @property int $step_type_id
 * @property int $started_user_id
 * @property int $completed_user_id
 * @property int $order
 * @property string $short_name
 * @property string $long_name
 * @property string $pincode
 * @property string $state_data
 * @property string $start_time
 * @property string $end_time
 * @property string $status
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $created_user
 * @property User $started_user
 * @property User $completed_user
 * @property User $lastModifiedUser
 * @property Pathway $pathway
 * @property PathwayStepComment $comment
 * @property PathwayStepType $type
 * @property Event $associated_event
 */
class PathwayStep extends BaseActiveRecordVersioned
{
    public const STEP_REQUESTED = 0;
    public const STEP_STARTED = 1;
    public const STEP_COMPLETED = 2;
    public const STEP_DRAFT = 3;
    public const STEP_CONFIG = -1;
    public const NON_GENERIC_STEP = array(
        'onhold',
        'break',
        'drug admin',
    );
    public const NO_WAIT_TIMER_AFTER_ADD = array(
        'break'
    );
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'pathway_step';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('pathway_id, step_type_id, order, short_name, long_name', 'required'),
            array('pathway_id, step_type_id, order, status', 'numerical', 'integerOnly' => true),
            array('started_user_id, completed_user_id, last_modified_user_id, created_user_id, started_user_id, completed_user_id', 'length', 'max' => 10),
            array('short_name', 'length', 'max' => 20),
            array('long_name', 'length', 'max' => 100),
            array('pincode', 'length', 'max' => 255),
            array('start_time, end_time, last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            array(
                'id, pathway_id, step_type_id, owner_id, order, short_name, long_name, pincode, start_time, end_time, status, created_user_id, created_date, started_user_id, completed_user_id',
                'safe',
                'on' => 'search'
            ),
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
            'created_user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'owner' => array(self::BELONGS_TO, 'User', 'owner_id'),
            'started_user' => array(self::BELONGS_TO, 'User', 'started_user_id'),
            'completed_user' => array(self::BELONGS_TO, 'User', 'completed_user_id'),
            'pathway' => array(self::BELONGS_TO, 'Pathway', 'pathway_id'),
            'type' => array(self::BELONGS_TO, 'PathwayStepType', 'step_type_id'),
            'associated_event' => array(self::HAS_ONE, 'Event', 'step_id'),
            'comment' => array(self::HAS_ONE, 'PathwayStepComment', 'pathway_step_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'pathway_id' => 'Pathway',
            'step_type_id' => 'Step Type',
            'owner_id' => 'Owner',
            'order' => 'Order',
            'short_name' => 'Short Name',
            'long_name' => 'Long Name',
            'pincode' => 'Pincode',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'status' => 'Status',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('pathway_id', $this->pathway_id);
        $criteria->compare('step_type_id', $this->step_type_id);
        $criteria->compare('order', $this->order);
        $criteria->compare('short_name', $this->short_name, true);
        $criteria->compare('long_name', $this->long_name, true);
        $criteria->compare('pincode', $this->pincode, true);
        $criteria->compare('start_time', $this->start_time, true);
        $criteria->compare('end_time', $this->end_time, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider(
            $this,
            array(
                'criteria' => $criteria,
            )
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PathwayStep the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Promotes the step's status. This function will invoke the actions associated with the
     * specific before/after stage of the step.
     * @throws Exception Thrown if the step is already at the highest status.
     */
    public function nextStatus($metadata = array()): void
    {
        if (!empty($metadata)) {
            foreach ($metadata as $key => $value) {
                $this->setState($key, $value);
            }
        }
        if((int)$this->status === self::STEP_COMPLETED){
            return;
        }
        if ((int)$this->status === self::STEP_CONFIG) {
            $this->status = self::STEP_REQUESTED;
            $this->save();
        } elseif ((int)$this->status === self::STEP_REQUESTED || !$this->status) {
            $this->status = self::STEP_STARTED;
            $this->start_time = date('Y-m-d H:i:s');
            $this->started_user_id = Yii::app()->user->id;
            $this->pathway->enqueue($this);
            if (!$this->pathway->start_time) {
                $this->pathway->start_time = date('Y-m-d H:i:s');
                $this->pathway->save();
            }
        } elseif ((int)$this->status === self::STEP_STARTED) {
            if($this->getState('duration')){
                $diff = strtotime(date('Y-m-d H:i:s')) - strtotime($this->start_time);
                if($diff < (int)$this->getState('duration') * 60){
                    return;
                }
            }
            $this->status = self::STEP_COMPLETED;
            $this->end_time = date('Y-m-d H:i:s');
            $this->completed_user_id = Yii::app()->user->id;
            $this->pathway->enqueue($this);
        }
    }

    /**
     * Demotes the step's status.
     * @throws Exception Thrown if the step is already at the lowest status.
     */
    public function prevStatus(): void
    {
        if ((int)$this->status === self::STEP_COMPLETED) {
            $this->status = self::STEP_STARTED;
            $this->completed_user_id = null;
            $this->end_time = null;
            $this->pathway->enqueue($this);
        } elseif ((int)$this->status === self::STEP_STARTED) {
            $this->status = self::STEP_REQUESTED;
            $this->start_time = null;
            $this->started_user_id = null;
            // Remove the event_create_url if it exists to ensure that redirects don't take place.
            if ($this->getState('event_create_url')) {
                $this->setState('event_create_url', null);
            }
            $this->pathway->enqueue($this);
        } elseif ((int)$this->status === self::STEP_REQUESTED) {
            $this->status = self::STEP_CONFIG;
            $this->save();
        } else {
            throw new Exception('This step is in config state and cannot be reverted any further.');
        }
    }

    /**
     * Marks the step as completed regardless of the current status.
     * @throws Exception
     */
    public function markCompleted($metadata = array()): void
    {
        if (!empty($metadata)) {
            foreach ($metadata as $key => $value) {
                $this->setState($key, $value);
            }
        }
        if ((int)$this->status !== self::STEP_COMPLETED) {
            $this->status = self::STEP_COMPLETED;
            if (!$this->start_time) {
                $this->start_time = date('Y-m-d H:i:s');
            }
            $this->end_time = date('Y-m-d H:i:s');
            $this->completed_user_id = Yii::app()->user->id;
            $this->pathway->enqueue($this);
            return;
        }

        throw new Exception('This step is complete and cannot be progressed any further.');
    }

    /**
     * Gets the string representation of the step's status.
     * @return string The string representation of the step's status.
     * @throws Exception
     */
    public function getStatusString(): string
    {
        switch ((int)$this->status) {
            case self::STEP_CONFIG:
                return 'config';
            case self::STEP_REQUESTED:
                return 'todo';
            case self::STEP_STARTED:
                return 'active';
            case self::STEP_DRAFT:
                return 'draft';
            case self::STEP_COMPLETED:
                return 'done';
            default:
                throw new Exception('This step has an invalid status.');
        }
    }

    /**
     * Returns the step as a JSON-compatible key-value array.
     * @return array A representation of the step in a JSON-compatible array format.
     * @throws Exception
     */
    public function toJSON(): array
    {
        $json = array(
            'id' => $this->id,
            'patient_id' => $this->pathway->worklist_patient->patient_id,
            'status' => $this->getStatusString(),
            'type' => $this->type->type,
            'short_name' => $this->short_name,
            'start_time' => $this->start_time ? DateTime::createFromFormat('Y-m-d H:i:s', $this->start_time)->format('H:i') : null,
            'start_timestamp' => $this->start_time ? strtotime($this->start_time) : null,
            'now_timestamp' => time(),
            'end_time' => $this->end_time ? DateTime::createFromFormat('Y-m-d H:i:s', $this->end_time)->format('H:i') : null,
            'state_data' => $this->state_data
        );

        if ($this->type->large_icon) {
            $json['icon'] = $this->type->large_icon;
        }
        return $json;
    }

    /**
     * @throws JsonException
     */
    public function getState(string $key)
    {
        if ($this->state_data) {
            $state_temp = json_decode($this->state_data, true, 512, JSON_THROW_ON_ERROR);
            return $state_temp[$key] ?? null;
        }
        return null;
    }

    /**
     * @param string $key
     * @param $value
     * @throws JsonException
     */
    public function setState(string $key, $value): void
    {
        if ($this->state_data) {
            $state_temp = json_decode($this->state_data, true, 512, JSON_THROW_ON_ERROR);
        } else {
            $state_temp = array();
        }

        $state_temp[$key] = $value;
        $this->state_data = json_encode($state_temp, JSON_THROW_ON_ERROR);
    }

    protected function afterFind(){
        parent::afterFind();
        $this->syncPSDStatus();
    }

    protected function syncPSDStatus(){
        if(!$this->type || $this->type->short_name !== 'drug admin' || !$assignment_id = $this->getState('assignment_id')){
            return;
        }
        $assignment = OphDrPGDPSD_Assignment::model()->findByPk($assignment_id);
        // sync with step status
        $assignment_status = (int)$assignment->status;
        switch($assignment_status){
            case OphDrPGDPSD_Assignment::STATUS_TODO:
                $this->status = PathwayStep::STEP_REQUESTED;
                $this->start_time = null;
                $this->end_time = null;
                $this->started_user_id = null;
                $this->completed_user_id = null;
                break;
            case OphDrPGDPSD_Assignment::STATUS_PART_DONE:
                $this->status = PathwayStep::STEP_STARTED;
                if(!$this->start_time){
                    $this->start_time = $assignment->last_modified_date;
                }
                $this->end_time = null;
                if(!$this->started_user_id){
                    $this->started_user_id = $assignment->last_modified_user_id;
                }
                $this->completed_user_id = null;
                break;
            case OphDrPGDPSD_Assignment::STATUS_COMPLETE:
                $this->status = PathwayStep::STEP_COMPLETED;
                $this->end_time = $assignment->last_modified_date;
                $this->completed_user_id = $assignment->last_modified_user_id;
                break;
        }
        $this->saveOnlyIfDirty(true)->save();
    }
}
