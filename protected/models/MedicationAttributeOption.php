<?php

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "medication_attribute_option".
 *
 * The followings are the available columns in table 'medication_attribute_option':
 * @property integer $id
 * @property integer $medication_attribute_id
 * @property string $value
 * @property string $description
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property MedicationAttributeAssignment[] $medicationAttributeAssignments
 * @property MedicationAttribute $medicationAttribute
 * @property User $createdUser
 * @property User $lastModifiedUser
 */
class MedicationAttributeOption extends BaseActiveRecordVersioned
{
    use HasFactory;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'medication_attribute_option';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('medication_attribute_id', 'required'),
            array('medication_attribute_id', 'numerical', 'integerOnly'=>true),
            array('value', 'length', 'max'=>64),
            array('description', 'length', 'max'=>256),
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('last_modified_date, created_date, medication_attribute_id', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, medication_attribute_id, value, description, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'medicationAttributeAssignments' => array(self::HAS_MANY, 'MedicationAttributeAssignment', 'medication_attribute_option_id'),
            'medicationAttribute' => array(self::BELONGS_TO, 'MedicationAttribute', 'medication_attribute_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
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
            'medication_attribute_id' => 'Medication Attribute',
            'value' => 'Value',
            'description' => 'Description',
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

        $criteria->compare('id', $this->id);
        $criteria->compare('medication_attribute_id', $this->medication_attribute_id);
        $criteria->compare('value', $this->value, true);
        $criteria->compare('description', $this->description, true);
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
     * @return MedicationAttributeOption the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
