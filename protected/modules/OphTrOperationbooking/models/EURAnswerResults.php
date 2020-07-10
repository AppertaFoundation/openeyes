<?php

/**
 * This is the model class for table "eur_answer_results".
 *
 * The followings are the available columns in table 'eur_answer_results':
 * @property integer $id
 * @property integer $res_id
 * @property string $answer_id
 * @property integer $eye_no
 * @property integer $eye_side
 *
 * The followings are the available model relations:
 * @property EURAnswers $answer
 * @property EUREventResult $eventResult
 */
class EURAnswerResults extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'eur_answer_results';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('question_id, answer_id, res_id, eye_num', 'required'),
            array('eye_side', 'safe'),
            // The following rule is used by search().
            array('id, res_id, answer_id, question_id, eye_num, eye_side', 'safe', 'on'=>'search'),
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
            'question' => array(self::BELONGS_TO, 'EURQuestions', 'question_id'),
            'answer' => array(self::BELONGS_TO, 'EURAnswers', 'answer_id'),
            'element' => array(self::BELONGS_TO, 'EUREventResult', 'res_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_side'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'res_id' => 'Event',
            'answer_id' => 'Answer',
            'question_id' => 'Question',
            'eye_num' => 'Eye Number',
            'eye_side' => 'Eye Side',
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
        $criteria->compare('res_id', $this->res_id);
        $criteria->compare('question_id', $this->question_id);
        $criteria->compare('answer_id', $this->answer_id);
        $criteria->compare('eye_num', $this->eye_num, true);
        $criteria->compare('eye_side', $this->eye_side, true);

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
