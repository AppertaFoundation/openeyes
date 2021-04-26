<?php

/**
 * This is the model class for table "patient_statistic_datapoint".
 *
 * The followings are the available columns in table 'patient_statistic_datapoint':
 * @property int $id
 * @property string $patient_id
 * @property string $stat_type_mnem
 * @property string $eye_id
 * @property float $x_value
 * @property float $y_value
 * @property int $event_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $created_user
 * @property User $last_modified_user
 * @property PatientStatistic $statistic
 * @property Event $event
 */
class PatientStatisticDatapoint extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'patient_statistic_datapoint';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('stat_type_mnem, x_value, y_value', 'required'),
            array('x_value, y_value', 'numerical'),
            array('patient_id, eye_id', 'length', 'max' => 11),
            array('stat_type_mnem, last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            array(
                'id, patient_id, stat_type_mnem, eye_id, x_value, y_value, last_modified_user_id, last_modified_date, created_user_id, created_date',
                'safe',
                'on' => 'search'
            ),
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
            'created_user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'last_modified_user' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'statistic' => array(self::BELONGS_TO, 'PatientStatistic', array('patient_id', 'stat_type_mnem', 'eye_id')),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'patient_id' => 'Patient',
            'stat_type_mnem' => 'Statistic Type',
            'eye_id' => 'Eye',
            'x_value' => 'X',
            'y_value' => 'Y',
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
        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('patient_id', $this->patient_id, true);
        $criteria->compare('stat_type_mnem', $this->stat_type_mnem, true);
        $criteria->compare('eye_id', $this->eye_id, true);
        $criteria->compare('x_value', $this->x_value);
        $criteria->compare('y_value', $this->y_value);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array('criteria' => $criteria));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BaseActiveRecord
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Gets the linear-modelled Y-value for the current datapoint based on the statistic's overall gradient and y-intercept.
     * @return float
     */
    public function getLinearY()
    {
        $gradient = $this->statistic->gradient;
        $y_intercept = $this->statistic->y_intercept;

        return $gradient * $this->x_value + $y_intercept;
    }
}
