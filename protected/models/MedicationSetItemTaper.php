<?php

/**
 * This is the model class for table "medication_set_item_taper".
 *
 * The followings are the available columns in table 'medication_set_item_taper':
 * @property integer $id
 * @property integer $medication_set_id
 * @property double $dose
 * @property integer $frequency_id
 * @property string $duration_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property MedicationDuration $duration
 * @property MedicationFrequency $frequency
 * @property MedicationSetItem $medicationSetItem
 * @property User $createdUser
 * @property User $lastModifiedUser
 */
class MedicationSetItemTaper extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'medication_set_item_taper';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('medication_set_item_id, frequency_id, duration_id', 'required'),
            array('medication_set_item_id, frequency_id', 'numerical', 'integerOnly'=>true),
            array('dose', 'numerical'),
            array('duration_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, medication_set_item_id, dose, frequency_id, duration_id, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'duration' => array(self::BELONGS_TO, MedicationDuration::class, 'duration_id'),
            'frequency' => array(self::BELONGS_TO, MedicationFrequency::class, 'frequency_id'),
            'medicationSetItem' => array(self::BELONGS_TO, MedicationSetItem::class, 'medication_set_item_id'),
            'createdUser' => array(self::BELONGS_TO, User::class, 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, User::class, 'last_modified_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'medication_set_item_id' => 'Medication Set Item',
            'dose' => 'Dose',
            'frequency_id' => 'Frequency',
            'duration_id' => 'Duration',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
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

        $criteria->compare('id',$this->id);
        $criteria->compare('medication_set_item_id',$this->medication_set_item_id);
        $criteria->compare('dose',$this->dose);
        $criteria->compare('frequency_id',$this->frequency_id);
        $criteria->compare('duration_id',$this->duration_id,true);
        $criteria->compare('last_modified_user_id',$this->last_modified_user_id,true);
        $criteria->compare('last_modified_date',$this->last_modified_date,true);
        $criteria->compare('created_user_id',$this->created_user_id,true);
        $criteria->compare('created_date',$this->created_date,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return MedicationSetItemTaper the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
