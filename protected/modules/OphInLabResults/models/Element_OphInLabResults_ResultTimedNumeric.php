<?php

/**
 * Class Element_OphInLabResults_ResultTimedNumeric.
 */
class Element_OphInLabResults_ResultTimedNumeric extends BaseLabResultElement
{
    /**
     * @return string
     */
    public function tableName()
    {
        return 'et_ophinlabresults_result_timed_numeric';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('time, result, type', 'required'),
            array('result', 'labResultIsValid'),
            array('time', 'type', 'type' => 'time', 'timeFormat' => 'hh:mm'),
            array('event_id, time, result, comment, type, unit', 'safe'),
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
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'resultType' => array(self::BELONGS_TO, 'OphInLabResults_Type', 'type')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'time' => 'Time of Recording',
            'type' => 'Type'
        );
    }

    public function init()
    {
        parent::init();
        $this->time =  date_create('now')->format('H:i');
    }

    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        parent::afterFind();
        //We aren't really interested in the microseconds and it breaks the validation on edit
        $this->time = date_create_from_format('H:i:s', $this->time)->format('H:i');
    }

    public function setDefaultUnit()
    {
        if (!$this->unit) {
            $this->unit = $this->resultType->default_units;
        }
    }

    public function labResultIsValid($attribute, $params)
    {
        if ($this->resultType->fieldType->name === "Numeric Field") {
            if ($this->resultType->min_range > $this->$attribute || $this->resultType->max_range < $this->$attribute) {
                $this->addError(
                    $attribute, 'Value should be between ' . $this->resultType->min_range . ' and ' .
                    $this->resultType->max_range
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findPatientResultByType($patientId, $type)
    {
        $criteria = new CDbCriteria();
        $criteria->join = ' LEFT JOIN event on t.event_id = event.id ';
        $criteria->join .= 'LEFT JOIN episode on event.episode_id = episode.id ';
        $criteria->join .= ' LEFT JOIN et_ophinlabresults_details on et_ophinlabresults_details.event_id = event.id ';
        $criteria->addCondition('et_ophinlabresults_details.result_type_id = :type');
        $criteria->addCondition('episode.patient_id = :patientId');
        $criteria->addCondition('event.deleted = 0');
        $criteria->order = 'event.event_date DESC, t.time DESC, event.created_date DESC';
        $criteria->limit = 1;
        $criteria->params = array(
            'type' => $type,
            'patientId' => $patientId,
        );

        return $this->find($criteria);
    }

    public function getPrint_view()
    {
        return 'print_' . $this->getDefaultView();
    }
}
