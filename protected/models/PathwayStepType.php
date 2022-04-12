<?php

/**
 * This is the model class for table "pathway_step_type".
 *
 * The followings are the available columns in table 'pathway_step_type':
 * @property int $id
 * @property int $action_id
 * @property int $default_state
 * @property string $state_data_template
 * @property string $short_name
 * @property string $long_name
 * @property string $small_icon
 * @property string $large_icon
 * @property string $widget_view
 * @property string $type
 * @property bool $user_can_create
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property PathwayStep[] $pathway_steps
 * @property User $createdUser
 * @property User $lastModifiedUser
 */
class PathwayStepType extends BaseActiveRecordVersioned
{
    use MappedReferenceData;

    public function getSupportedLevels()
    {
        return ReferenceData::LEVEL_INSTITUTION;
    }

    public function mappingColumn()
    {
        return 'pathway_step_type_id';
    }

    public const START_AFTER_ADD = array(
        'break'
    );

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'pathway_step_type';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('short_name, long_name, type', 'required'),
            array('action_id, default_state', 'numerical', 'integerOnly' => true),
            array('short_name, long_name', 'length', 'max' => 20),
            array('last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('last_modified_date, created_date, active', 'safe'),
            // The following rule is used by search().
            array(
                'id, default_state, short_name, long_name',
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
            'pathway_steps' => array(self::HAS_MANY, 'PathwayStep', 'step_type_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'custom_pathway_step_type' => array(self::HAS_ONE, 'PathwayStepTypePresetAssignment', 'id'),
            'standard_pathway_step_type' => array(self::HAS_ONE, 'PathwayStepTypePresetAssignment', 'id'),
            'institutions' => array(self::MANY_MANY, 'Institution', 'pathway_step_type_institution(pathway_step_type_id, institution_id)'),
            'pathway_step_type_institutions' => array(self::HAS_MANY, 'PathwayStepType_Institution', 'pathway_step_type_id'),
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
            'action_id' => 'Action',
            'state' => 'State',
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
        $criteria->compare('default_state', $this->default_state);
        $criteria->compare('short_name', $this->short_name, true);
        $criteria->compare('long_name', $this->long_name, true);

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
     * @return PathwayStepType the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    private static function basePathTypeCriteria(){
        $criteria = new CDbCriteria();
        $criteria->addCondition('user_can_create = 1');
        return $criteria;
    }
    /**
     * @return array|CActiveRecord|mixed|PathwayStepType[]|null
     */
    public static function getPathTypes()
    {
        $criteria = self::basePathTypeCriteria();
        $criteria->addCondition('`group` = \'path\'');
        return self::model()->findAll($criteria);
    }

    /**
     * @return array|CActiveRecord|mixed|PathwayStepType[]|null
     */
    public static function getStandardTypes()
    {
        $criteria = self::basePathTypeCriteria();
        $criteria->addCondition('`group` = \'standard\'');
        return self::model()->findAll($criteria);
    }

    /**
     * @return array|CActiveRecord|mixed|PathwayStepType[]|null
     */
    public static function getCustomTypes($is_admin = false)
    {
        $criteria = self::basePathTypeCriteria();
        $criteria->addCondition('`group` IS NULL');
        if (!$is_admin) {
            $criteria->with = ['institutions'];
            $criteria->addCondition('institutions_institutions.institution_id = :institution_id');
            $criteria->params[':institution_id'] = Yii::app()->session['selected_institution_id'];
        }

        return self::model()->findAll($criteria);
    }

    /**
     * @return PathwayTypeStep|false
     * @throws JsonException
     * @throws Exception
     */
    public function createNewStepForPathwayType(
        int $pathway_type_id,
        array $initial_state_data,
        int $queue_position = 0
    ) {
        $pathway_type = PathwayType::model()->findByPk($pathway_type_id);

        if ($pathway_type) {
            $step = new PathwayTypeStep();
            $step->pathway_type_id = $pathway_type_id;
            $step->step_type_id = $this->id;
            $step->short_name = array_key_exists('short_name', $initial_state_data)
                ? $initial_state_data['short_name']
                : $this->short_name;
            $step->long_name = array_key_exists('long_name', $initial_state_data)
                ? $initial_state_data['long_name']
                : $this->long_name;
            $step->status = $this->default_state;
            if ($this->state_data_template) {
                $template = json_decode($this->state_data_template, true, 512, JSON_THROW_ON_ERROR);
                $state_data = array_merge($template, $initial_state_data);
            } else {
                $state_data = $initial_state_data;
            }

            $step->default_state_data = json_encode($state_data, JSON_THROW_ON_ERROR);

            // Enqueueing the step saves the step and also sets the order.
            if ($queue_position !== 0 && !$pathway_type->enqueueAtPosition($step, $queue_position)) {
                return false;
            }

            if ($queue_position === 0 && !$pathway_type->enqueue($step)) {
                return false;
            }

            $step->refresh();

            return $step;
        }
        return false;
    }

    /**
     * @param int $pathway_id
     * @param array $initial_state_data
     * @param bool $raise_event
     * @param int $queue_position
     * @return PathwayStep|false
     * @throws JsonException
     */
    public function createNewStepForPathway(
        int $pathway_id,
        array $initial_state_data,
        bool $raise_event = true,
        int $queue_position = 0
    ) {
        $pathway = Pathway::model()->findByPk($pathway_id);

        if ($pathway) {
            $step = new PathwayStep();
            $step->pathway_id = $pathway_id;
            $step->step_type_id = $this->id;
            $step->short_name = array_key_exists('short_name', $initial_state_data)
                ? $initial_state_data['short_name']
                : $this->short_name;
            $step->long_name = array_key_exists('long_name', $initial_state_data)
                ? $initial_state_data['long_name']
                : $this->long_name;
            $step->status = $this->default_state;

            if (in_array($this->short_name, self::START_AFTER_ADD)) {
                $step->status = PathwayStep::STEP_STARTED;
                $step->start_time = date('Y-m-d H:i:s');
            }

            if (!empty($initial_state_data)) {
                // Only override the default state data template if overrides have been set.
                if ($this->state_data_template) {
                    $template = json_decode($this->state_data_template, true, 512, JSON_THROW_ON_ERROR);
                    $state_data = array_merge($template, $initial_state_data);
                } else {
                    $state_data = $initial_state_data;
                }
                $step->state_data = json_encode($state_data, JSON_THROW_ON_ERROR);
            } else {
                $step->state_data = $this->state_data_template;
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
     */
    public function getState(string $key)
    {
        if ($this->state_data_template) {
            $state_temp = json_decode($this->state_data_template, true, 512, JSON_THROW_ON_ERROR);
            return $state_temp[$key] ?? null;
        }
        return null;
    }
}
