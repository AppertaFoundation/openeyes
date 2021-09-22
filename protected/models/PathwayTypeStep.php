<?php

/**
 * This is the model class for table "pathway_step".
 *
 * The followings are the available columns in table 'pathway_step':
 * @property int $id
 * @property int $pathway_type_id
 * @property int $step_type_id
 * @property int $order
 * @property string $short_name
 * @property string $long_name
 * @property string $default_state_data
 * @property string $status
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $lastModifiedUser
 * @property PathwayType $pathway_type
 * @property PathwayStepType $step_type
 */
class PathwayTypeStep extends BaseActiveRecordVersioned
{
    public const STEP_REQUESTED = 0;
    public const STEP_STARTED = 1;
    public const STEP_COMPLETED = 2;
    public const STEP_DRAFT = 3;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'pathway_type_step';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('pathway_type_id, step_type_id, order, short_name, long_name', 'required'),
            array('pathway_type_id, step_type_id, order, status', 'numerical', 'integerOnly' => true),
            array('last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('short_name', 'length', 'max' => 20),
            array('long_name', 'length', 'max' => 100),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            array(
                'id, pathway_type_id, step_type_id, order, short_name, long_name, status, created_user_id, created_date',
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
            'pathway_type' => array(self::BELONGS_TO, 'PathwayType', 'pathway_type_id'),
            'step_type' => array(self::BELONGS_TO, 'PathwayStepType', 'step_type_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'pathway_type_id' => 'Pathway Type',
            'step_type_id' => 'Step Type',
            'order' => 'Order',
            'short_name' => 'Short Name',
            'long_name' => 'Long Name',
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
        $criteria->compare('pathway_type_id', $this->pathway_type_id);
        $criteria->compare('step_type_id', $this->step_type_id);
        $criteria->compare('order', $this->order);
        $criteria->compare('short_name', $this->short_name, true);
        $criteria->compare('long_name', $this->long_name, true);
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
     * @return PathwayTypeStep the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Gets the string representation of the step's status.
     * @return string The string representation of the step's status.
     * @throws Exception
     */
    public function getStatusString(): string
    {
        switch ((int)$this->status) {
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
            'status' => $this->getStatusString(),
            'step_type' => $this->step_type->type,
            'short_name' => $this->short_name,
            'default_state_data' => $this->default_state_data
        );

        if ($this->step_type->large_icon) {
            $json['icon'] = $this->step_type->large_icon;
        }
        return $json;
    }

    /**
     * @throws JsonException
     */
    public function getState(string $key)
    {
        if ($this->default_state_data) {
            $state_temp = json_decode($this->default_state_data, true, 512, JSON_THROW_ON_ERROR);
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
        if ($this->default_state_data) {
            $state_temp = json_decode($this->default_state_data, true, 512, JSON_THROW_ON_ERROR);
        } else {
            $state_temp = array();
        }

        $state_temp[$key] = $value;
        $this->default_state_data = json_encode($state_temp, JSON_THROW_ON_ERROR);
    }

    /**
     * @param int $pathway_id
     * @param array $initial_state_data
     * @param bool $raise_event
     * @param int $queue_position
     * @return false|PathwayStep
     * @throws JsonException
     * @throws Exception
     */
    public function cloneStepForPathway(
        int $pathway_id,
        array $initial_state_data,
        bool $raise_event = true,
        int $queue_position = 0
    ) {
        $pathway = Pathway::model()->findByPk($pathway_id);

        if ($pathway) {
            $step = new PathwayStep();
            $step->pathway_id = $pathway_id;
            $step->step_type_id = $this->step_type_id;
            $step->short_name = array_key_exists('short_name', $initial_state_data)
                ? $initial_state_data['short_name']
                : $this->short_name;
            $step->long_name = array_key_exists('long_name', $initial_state_data)
                ? $initial_state_data['long_name']
                : $this->long_name;
            $step->status = $this->status;
            if (!empty($initial_state_data)) {
                if ($this->default_state_data) {
                    $template = json_decode($this->default_state_data, true, 512, JSON_THROW_ON_ERROR);
                    $state_data = array_merge($template, $initial_state_data);
                } else {
                    $state_data = $initial_state_data;
                }
                $step->state_data = json_encode($state_data, JSON_THROW_ON_ERROR);
            } else {
                $step->state_data = $this->default_state_data;
            }

            // Enqueueing the step saves the step and also sets the order.
            if ($queue_position !== 0 && !$pathway->enqueueAtPosition($step, $queue_position)) {
                return false;
            }

            if ($queue_position === 0 && !$pathway->enqueue($step)) {
                return false;
            }

            $step->refresh();

            if ($raise_event) {
                Yii::app()->event->dispatch('step_created', ['step' => $step]);
            }

            return $step;
        }
        return false;
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function cloneStepForPathwayType(int $pathway_id)
    {
        $pathway = PathwayType::model()->findByPk($pathway_id);

        if ($pathway) {
            $step = new PathwayTypeStep();
            $step->pathway_type_id = $pathway_id;
            $step->step_type_id = $this->step_type_id;
            $step->short_name = $this->short_name;
            $step->long_name = $this->long_name;
            $step->status = $this->status;
            $step->default_state_data = $this->default_state_data;

            // Enqueueing the step saves the step and also sets the order.
            if (!$pathway->enqueue($step)) {
                return false;
            }

            $step->refresh();

            return $step;
        }
        return false;
    }
}
