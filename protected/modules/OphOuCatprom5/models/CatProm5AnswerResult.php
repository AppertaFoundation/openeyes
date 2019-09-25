<?php

/**
 * This is the model class for table "cat_prom5_answer_results".
 *
 * The followings are the available columns in table 'cat_prom5_answer_results':
 * @property integer $id
 * @property integer $element_id
 * @property string $answer_id
 *
 * The followings are the available model relations:
 * @property CatProm5Answers $answer
 * @property CatProm5EventResult $eventResult
 */
class CatProm5AnswerResult extends \BaseEventTypeElement
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'cat_prom5_answer_results';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('id, question_id, answer_id', 'safe'),
            array('element_id', 'required'),
            array('element_id', 'numerical', 'integerOnly'=>true),
            array('question_id, answer_id', 'length', 'max'=>10),
            // The following rule is used by search().
            array('id, element_id, answer_id, question_id', 'safe', 'on'=>'search'),
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
            'question' => array(self::BELONGS_TO, 'CatProm5Questions', 'question_id'),
            'answer' => array(self::BELONGS_TO, 'CatProm5Answers', 'answer_id'),
            'element' => array(self::BELONGS_TO, 'CatProm5EventResult', 'element_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'element_id' => 'Event',
            'answer_id' => 'Answer',
            'question_id' => 'Question',
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

        $criteria->compare('id', $this->id);
        $criteria->compare('element_id', $this->element_id);
        $criteria->compare('question_id', $this->question_id);
        $criteria->compare('answer_id', $this->answer_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CatProm5AnswerResult the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
