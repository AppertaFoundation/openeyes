<?php

/**
 * This is the model class for table "ophtroperationbooking_whiteboard_settings_data".
 *
 * The followings are the available columns in table 'ophtroperationbooking_whiteboard_settings_data':
 * @property string $id
 * @property string $element_type_id
 * @property string $key
 * @property string $value
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 */
class OphTrOperationbooking_Whiteboard_Settings_Data extends \CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophtroperationbooking_whiteboard_settings_data';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('key', 'required'),
            array('element_type_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('key', 'length', 'max'=>64),
            array('value', 'length', 'max'=>255),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, element_type_id, key, value, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'element_type' => array(self::BELONGS_TO, 'ElementType', 'element_type_id'),
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'element_type_id' => 'Element Type',
            'key' => 'Key',
            'value' => 'Value',
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

        $criteria->compare('id', $this->id, true);
        $criteria->compare('element_type_id', $this->element_type_id, true);
        $criteria->compare('key', $this->key, true);
        $criteria->compare('value', $this->value, true);
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
     * @return OphTrOperationbooking_Whiteboard_Settings_Data the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
