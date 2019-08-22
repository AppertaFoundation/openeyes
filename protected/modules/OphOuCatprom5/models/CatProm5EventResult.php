<?php

/**
 * This is the model class for table "cat_prom5_event_result".
 *
 * The followings are the available columns in table 'cat_prom5_event_result':
 * @property integer $id
 * @property integer $total_raw_score
 * @property string $total_rasch_measure
 * @property string $event_id
 *
 * The followings are the available model relations:
 * @property CatProm5AnswerResults[] $catProm5AnswerResults
 */
class CatProm5EventResult extends \BaseEventTypeElement
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'cat_prom5_event_result';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('total_raw_score', 'numerical', 'integerOnly'=>true),
            array('total_rasch_measure', 'length', 'max'=>5),
            array('event_id', 'length', 'max'=>10),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            array('id, total_raw_score, total_rasch_measure, event_id', 'safe', 'on'=>'search'),
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
            'catProm5AnswerResults' => array(self::HAS_MANY, 'CatProm5AnswerResults', 'event_result_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'total_raw_score' => 'Total Raw Score',
            'total_rasch_measure' => 'Total Rasch Measure',
            'event_id' => 'Event',
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

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('total_raw_score',$this->total_raw_score);
        $criteria->compare('total_rasch_measure',$this->total_rasch_measure,true);
        $criteria->compare('event_id',$this->event_id,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CatProm5EventResult the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
