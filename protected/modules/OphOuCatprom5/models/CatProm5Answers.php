<?php

/**
 * This is the model class for table "cat_prom5_answers".
 *
 * The followings are the available columns in table 'cat_prom5_answers':
 * @property string $id
 * @property string $question_id
 * @property string $answer
 * @property string $score
 *
 * The followings are the available model relations:
 * @property CatProm5AnswerResults[] $catProm5AnswerResults
 * @property CatProm5Questions $question
 */
class CatProm5Answers extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'cat_prom5_answers';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('question_id, answer', 'required'),
            array('question_id, score', 'length', 'max'=>10),
            // The following rule is used by search().
            array('id, question_id, answer, score', 'safe', 'on'=>'search'),
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
            'catProm5AnswerResults' => array(self::HAS_MANY, 'CatProm5AnswerResults', 'answer_id'),
            'question' => array(self::BELONGS_TO, 'CatProm5Questions', 'question_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'question_id' => 'Question',
            'answer' => 'Answer',
            'score' => 'Score',
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

        $criteria->compare('id',$this->id,true);
        $criteria->compare('question_id',$this->question_id,true);
        $criteria->compare('answer',$this->answer,true);
        $criteria->compare('score',$this->score,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CatProm5Answers the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
