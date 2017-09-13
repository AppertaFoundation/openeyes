<?php

/**
 * Created by PhpStorm.
 * User: veta
 * Date: 19/08/2017
 * Time: 23:23
 */
class OphDrPrescription_DispenseCondition extends BaseActiveRecordVersioned
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophdrprescription_dispense_condition';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, display_order, active', 'required'),
            array('display_order', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>255),
            array('created_user_id', 'length', 'max'=>10),
            array('created_date, name, display_order, active, created_user_id, last_modified_user_id, last_modified_date,', 'safe'),
            array('id, caption, active', 'safe', 'on'=>'search'),
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
            'locations' => array(self::MANY_MANY, 'OphDrPrescription_DispenseLocation', 'ophdrprescription_dispense_condition_assignment(dispense_condition_id,dispense_location_id)'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'display_order' => 'Display Order',
            'created_date' => 'Created Date',
            'created_user_id' => 'Created By',
            'active' => 'Active'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }
    
}