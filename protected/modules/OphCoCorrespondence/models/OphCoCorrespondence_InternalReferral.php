<?php

/**
 * This is the model class for table "ophcocorrespondence_letter_internal_referral".
 *
 * The followings are the available columns in table 'ophcocorrespondence_letter_internal_referral':
 * @property string $id
 * @property string $element_id
 * @property integer $service_id
 * @property integer $user_id
 * @property integer $is_urgent
 * @property integer $is_same_condition
 *
 * The followings are the available model relations:
 * @property EtOphcocorrespondenceLetter $element
 */
class OphCoCorrespondence_InternalReferral extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophcocorrespondence_letter_internal_referral';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('element_id, service_id', 'required'),
            array('service_id, user_id, is_urgent, is_same_condition', 'numerical', 'integerOnly'=>true),
            array('element_id', 'length', 'max'=>10),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, element_id, service_id, user_id, is_urgent, is_same_condition', 'safe', 'on'=>'search'),
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
            'element' => array(self::BELONGS_TO, 'EtOphcocorrespondenceLetter', 'element_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'element_id' => 'Element',
            'service_id' => 'Service',
            'user_id' => 'User',
            'is_urgent' => 'Is Urgent',
            'is_same_condition' => 'Is Same Condition',
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
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id,true);
        $criteria->compare('element_id',$this->element_id,true);
        $criteria->compare('service_id',$this->service_id);
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('is_urgent',$this->is_urgent);
        $criteria->compare('is_same_condition',$this->is_same_condition);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OphcocorrespondenceLetterInternalReferral the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}