<?php

/**
 * Class StudyParticipationStatus
 */
class StudyParticipationStatus  extends BaseActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'study_participation_status';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array();
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array();
    }
}
