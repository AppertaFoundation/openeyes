<?php

/**
 * This is the model class for table "eur_questions".
 *
 * The followings are the available columns in table 'eur_questions':
 * @property integer $id
 * @property string $question
 * @property integer $mandatory
 * @property integer $display_order
 *
 * The followings are the available model relations:
 * @property EURAnswers[] $EURAnswers
 */
class EURQuestions extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'eur_questions';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('question, eye_num, display_order', 'required'),
            array('id, question, eye_num, display_order', 'safe', 'on'=>'search'),
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
            'answers' => array(self::HAS_MANY, 'EURAnswers', 'question_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'question' => 'Question',
            'eye_num' => 'Eye Number',
            'display_order' => 'Display Order',
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
        $criteria = new CDbCriteria;
        $criteria->compare('id', $this->id, true);
        $criteria->compare('question', $this->question, true);
        $criteria->compare('eye_num', $this->eye_num);
        $criteria->compare('display_order', $this->display_order);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EURQuestions the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
