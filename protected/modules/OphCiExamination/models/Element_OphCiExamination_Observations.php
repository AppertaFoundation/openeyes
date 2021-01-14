<?php


namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "et_ophciexamination_observations".
 *
 * The followings are the available columns in table 'et_ophciexamination_observations':
 * @property integer $id
 * @property string $event_id
 * @property string $blood_pressure_systolic
 * @property string $blood_pressure_diastolic
 * @property string $o2_sat
 * @property string $blood_glucose
 * @property string $hba1c
 * @property string $height
 * @property string $weight
 * @property string $pulse
 * @property string $temperature
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property User $createdUser
 * @property User $lastModifiedUser
 */
class Element_OphCiExamination_Observations extends \BaseEventTypeElement
{
    use traits\CustomOrdering;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_observations';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('blood_pressure_systolic, blood_pressure_diastolic, o2_sat, pulse', 'length', 'max'=>3),
            array('blood_glucose, hba1c', 'length', 'max'=>4),
            array('height, weight', 'length', 'max'=>5),
            array('last_modified_date, created_date', 'safe'),
            array('blood_pressure_systolic', 'numerical','min'=>0, 'max'=>400),
            array('blood_pressure_diastolic', 'numerical','min'=>0, 'max'=>400),
            array('o2_sat', 'numerical','min'=>0, 'max'=>100),
            array('blood_glucose', 'numerical', 'min'=>0.0, 'max'=>50.0),
            array('hba1c', 'numerical', 'min'=>0, 'max'=>1000),
            array('height', 'numerical', 'min'=>0.0, 'max'=>250.0),
            array('weight', 'numerical', 'min'=>0.0, 'max'=>250.0),
            array('pulse', 'numerical', 'min'=>0, 'max'=>200),
            array('temperature', 'numerical', 'min'=>30.0, 'max'=>45.0),
            array('blood_pressure_systolic,blood_pressure_diastolic,o2_sat,blood_glucose,hba1c,height,weight,pulse,temperature', 'default', 'setOnEmpty' => true, 'value' => null),
            array('blood_pressure_systolic,blood_pressure_diastolic,o2_sat,blood_glucose,hba1c,height,weight,pulse,temperature', \OEAtLeastOneRequiredValidator::class),
            array('id, event_id, blood_pressure_systolic, blood_pressure_diastolic, o2_sat, blood_glucose, hba1c, height, weight, pulse, temperature, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
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
            'event_id' => 'Event',
            'blood_pressure' => 'Blood pressure',
            'blood_pressure_systolic' => 'Blood p. systolic',
            'blood_pressure_diastolic' => 'Blood p. diastolic',
            'o2_sat' => 'O2 Sat (air)',
            'blood_glucose' => 'Blood Glucose',
            'hba1c' => 'HbA1c',
            'height' => 'Height',
            'weight' => 'Weight',
            'pulse' => 'Pulse',
            'temperature' => 'Temperature',
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
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('blood_pressure_systolic', $this->blood_pressure_systolic, true);
        $criteria->compare('blood_pressure_diastolic', $this->blood_pressure_diastolic, true);
        $criteria->compare('o2_sat', $this->o2_sat, true);
        $criteria->compare('blood_glucose', $this->blood_glucose, true);
        $criteria->compare('hba1c', $this->hba1c, true);
        $criteria->compare('height', $this->height, true);
        $criteria->compare('weight', $this->weight, true);
        $criteria->compare('pulse', $this->pulse, true);
        $criteria->compare('temperature', $this->temperature, true);
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
     * @return Element_OphCiExamination_Observations the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /*
     * Calculate BMI
     * @params $weight weight in kg
     * @params $height height in centimeters
     * @return float
     */
    public function bmiCalculator($weight, $height)
    {
        $height_meter = $height / 100;
        $result = $weight / ($height_meter * $height_meter);

        return number_format((float)$result, 2, '.', '');
    }
}
