<?php
/**
 * This is the model class for table "ophdrpgdpsd_pgdpsdmeds".
 *
 * The followings are the available columns in table 'ophdrpgdpsd_pgdpsdmeds':
 * @property integer $id
 * @property integer $medication_id
 * @property integer $dose
 * @property string $dose_unit_term
 * @property integer $route_id
 * @property integer $frequency_id
 * @property integer $duration_id
 * @property integer $dispense_condition_id
 * @property integer $dispense_location_id
 * @property integer $pgdpsd_id
 *
 * The followings are the available model relations:
 * @property Medication $medication
 * @property MedicationRoute $route
 * @property MedicationFrequency $frequency
 * @property MedicationDuration $duration
 * @property OphDrPrescription_DispenseCondition $dispense_condition
 * @property OphDrPrescription_DispenseLocation $dispense_location
 * @property OphDrPGDPSD_PGDPSD $pgdpsd
 */
class OphDrPGDPSD_PGDPSDMeds extends BaseActiveRecordVersioned
{
    public $laterality = null;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophdrpgdpsd_pgdpsd_meds';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        $common_rules =  array(
            array('medication_id, pgdpsd_id, dose, dose_unit_term, route_id', 'required'),
            array('medication_id, pgdpsd_id', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            array('pgdpsd_id, medication_id, dose, dose_unit_term, route_id, frequency_id, duration_id, dispense_condition_id, dispense_location_id, comments', 'safe'),
            array('frequency_id, duration_id, dispense_condition_id, dispense_location_id', 'validatePGDFields'), // required for PGD
            array('id, pgdpsd_id, medication_id, dose, dose_unit_term, route_id, frequency_id, duration_id, dispense_condition_id, dispense_location_id', 'safe', 'on'=>'search'),
        );

        return $common_rules;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'medication' => array(self::BELONGS_TO, 'Medication', 'medication_id'),
            'pgdpsd' => array(self::BELONGS_TO, 'OphDrPGDPSD_PGDPSD', 'pgdpsd_id'),
            'route' => array(self::BELONGS_TO, 'MedicationRoute', 'route_id'),
            'frequency' => array(self::BELONGS_TO, 'MedicationFrequency', 'frequency_id'),
            'duration' => array(self::BELONGS_TO, 'MedicationDuration', 'duration_id'),
            'dispense_condition' => array(self::BELONGS_TO, 'OphDrPrescription_DispenseCondition', 'dispense_condition_id'),
            'dispense_location' => array(self::BELONGS_TO, 'OphDrPrescription_DispenseLocation', 'dispense_location_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'medication_id' => 'Medication',
            'dose' => 'Dose',
            'dose_unit_term' => 'Unit Term',
            'route_id' => 'Route',
            'frequency_id' => 'Frequency',
            'duration_id' => 'Duration',
            'dispense_condition_id' => 'Dispense Condition',
            'dispense_location_id' => 'Dispense Location',
            'pgdpsd_id' => 'PGD/PSD',
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
        $criteria->compare('medication_id', $this->medication_id, true);
        $criteria->compare('dose', $this->dose, true);
        $criteria->compare('dose_unit_term', $this->dose_unit_term, true);
        $criteria->compare('route_id', $this->route_id, true);
        $criteria->compare('frequency_id', $this->frequency_id, true);
        $criteria->compare('duration_id', $this->duration_id, true);
        $criteria->compare('dispense_condition_id', $this->dispense_condition_id, true);
        $criteria->compare('dispense_location_id', $this->dispense_location_id, true);
        $criteria->compare('pgdpsd_id', $this->pgdpsd_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PsdDrugSetItem the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function validatePGDFields($attribute_name)
    {
        if (strtolower($this->pgdpsd->type) === 'pgd' && !$this->$attribute_name) {
            $attr_label = $this->getAttributeLabel($attribute_name);
            $this->addError($attribute_name, "{$attr_label} cannot be blank");
            return false;
        }
        return true;
    }
}
