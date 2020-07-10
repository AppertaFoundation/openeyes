<?php

/**
 * This is the model class for table "eur_answers".
 *
 * The followings are the available columns in table 'eur_answers':
 * @property integer $id
 * @property integer $question_id
 * @property string $answer
 * @property integer $value
 *
 * The followings are the available model relations:
 * @property EURAnswerResults[] $EURAnswerResults
 * @property EURQuestions $question
 */
class EURAnswers extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'eur_answers';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('question_id, answer, value', 'required'),
            array('id, question_id, answer, value', 'safe', 'on'=>'search'),
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
            'eurAnswerResults' => array(self::HAS_MANY, 'EURAnswerResults', 'answer_id'),
            'question' => array(self::BELONGS_TO, 'EURQuestions', 'question_id'),
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
        $criteria->compare('question_id', $this->question_id);
        $criteria->compare('answer', $this->answer, true);
        $criteria->compare('value', $this->value);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EURAnswers the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
