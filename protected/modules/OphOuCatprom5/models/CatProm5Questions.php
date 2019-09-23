<?php

/**
 * This is the model class for table "cat_prom5_questions".
 *
 * The followings are the available columns in table 'cat_prom5_questions':
 * @property string $id
 * @property string $question
 * @property integer $mandatory
 * @property string $display_order
 *
 * The followings are the available model relations:
 * @property CatProm5Answers[] $catProm5Answers
 * @property User $createdUser
 * @property User $lastModifiedUser
 */
class CatProm5Questions extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'cat_prom5_questions';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('question, display_order', 'required'),
            array('mandatory', 'numerical', 'integerOnly'=>true),
            array('display_order', 'length', 'max'=>10),
            // The following rule is used by search().
            array('id, question, mandatory, display_order', 'safe', 'on'=>'search'),
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
            'answers' => array(self::HAS_MANY, 'CatProm5Answers', 'question_id'),
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
            'mandatory' => 'Mandatory',
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
        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('question', $this->question, true);
        $criteria->compare('mandatory', $this->mandatory);
        $criteria->compare('display_order', $this->display_order, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CatProm5Questions the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
