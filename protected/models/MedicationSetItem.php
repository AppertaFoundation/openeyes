<?php

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "medication_set_item".
 *
 * The followings are the available columns in table 'medication_set_item':
 * @property integer $id
 * @property integer $medication_id
 * @property integer $medication_set_id
 * @property integer $default_form_id
 * @property double $default_dose
 * @property integer $default_route_id
 * @property integer $default_dispense_location_id
 * @property integer $default_dispense_condition_id
 * @property integer $default_frequency_id
 * @property string $default_dose_unit_term
 * @property integer $default_duration_id
 * @property string $deleted_date
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property MedicationFrequency $defaultFrequency
 * @property MedicationForm $defaultForm
 * @property MedicationRoute $defaultRoute
 * @property Medication $medication
 * @property MedicationSet $medicationSet
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property MedicationSetItemTaper[] $tapers
 * @property MedicationDuration $defaultDuration
 */
class MedicationSetItem extends BaseActiveRecordVersioned
{
    use HasFactory;

    public $auto_update_relations = true;
    public $auto_validate_relations = true;
    private $delete_with_tapers = false;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'medication_set_item';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('medication_id, medication_set_id', 'required'),
            array('medication_id, medication_set_id, default_form_id, default_route_id, default_frequency_id, default_duration_id', 'numerical', 'integerOnly'=>true),
            array('default_dose', 'numerical'),
            array('default_dose_unit_term', 'length', 'max'=>45),
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('deleted_date, last_modified_date, created_date, tapers', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, medication_id, medication_set_id, default_form_id, default_dose, default_route_id, default_frequency_id, default_dose_unit_term, deleted_date, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'defaultFrequency' => array(self::BELONGS_TO, 'MedicationFrequency', 'default_frequency_id'),
            'defaultForm' => array(self::BELONGS_TO, 'MedicationForm', 'default_form_id'),
            'defaultRoute' => array(self::BELONGS_TO, 'MedicationRoute', 'default_route_id'),
            'medication' => array(self::BELONGS_TO, Medication::class, 'medication_id'),
            'medicationSet' => array(self::BELONGS_TO, MedicationSet::class, 'medication_set_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'tapers' => array(self::HAS_MANY, MedicationSetItemTaper::class, 'medication_set_item_id'),
            'defaultDuration' => array(self::BELONGS_TO, MedicationDuration::class, 'default_duration_id'),
            'defaultDispenseCondition' => array(self::BELONGS_TO, 'OphDrPrescription_DispenseCondition', 'default_dispense_condition_id'),
            'defaultDispenseLocation' => array(self::BELONGS_TO, 'OphDrPrescription_DispenseLocation', 'default_dispense_location_id')
        );
    }

    public function deleteWithTapers()
    {
        $this->delete_with_tapers = true;
        return $this;
    }

    public function beforeDelete()
    {
        if ($this->delete_with_tapers === true) {
            MedicationSetItemTaper::model()->deleteAllByAttributes(['medication_set_item_id' => $this->id]);
        }

        return parent::beforeDelete();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'medication_id' => 'Medication',
            'medication_set_id' => 'Medication Set',
            'default_form_id' => 'Default Form',
            'default_dose' => 'Default Dose',
            'default_route_id' => 'Default Route',
            'default_frequency_id' => 'Default Frequency',
            'default_dose_unit_term' => 'Default Dose Unit Term',
            'deleted_date' => 'Deleted Date',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
            'tapers' => 'Tapers'
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
        $criteria->compare('medication_id', $this->medication_id);
        $criteria->compare('medication_set_id', $this->medication_set_id);
        $criteria->compare('default_form_id', $this->default_form_id);
        $criteria->compare('default_dose', $this->default_dose);
        $criteria->compare('default_route_id', $this->default_route_id);
        $criteria->compare('default_frequency_id', $this->default_frequency_id);
        $criteria->compare('default_dose_unit_term', $this->default_dose_unit_term, true);
        $criteria->compare('deleted_date', $this->deleted_date, true);
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
     * @return MedicationSetItem the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
