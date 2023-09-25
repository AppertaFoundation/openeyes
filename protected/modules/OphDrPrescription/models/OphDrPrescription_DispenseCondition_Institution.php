<?php

use OE\factories\models\traits\HasFactory;

/**
 * Class OphDrPrescription_DispenseCondition_Institution
 * @property OphDrPrescription_DispenseLocation_Institution[] $dispense_location_institutions
 */
class OphDrPrescription_DispenseCondition_Institution extends BaseActiveRecordVersioned
{
    use HasFactory;

    protected $auto_update_relations = true;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophdrprescription_dispense_condition_institution';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('dispense_condition_id', 'numerical', 'integerOnly' => true),
            array('institution_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('last_modified_date, created_date, dispense_location_institutions', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, dispense_condition_id, institution_id, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'dispense_condition' => array(self::BELONGS_TO, 'OphDrPrescription_DispenseCondition', 'dispense_condition_id'),
            'dispense_location_institutions' => array(self::MANY_MANY, 'OphDrPrescription_DispenseLocation_Institution', 'ophdrprescription_dispense_condition_assignment(dispense_condition_institution_id, dispense_location_institution_id)'),
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'dispense_condition_id' => 'Dispense Condition',
            'institution_id' => 'Institution',
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

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('dispense_condition_id', $this->dispense_condition_id);
        $criteria->compare('institution_id', $this->institution_id, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OphDrPrescription_DispenseCondition_Institution the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
