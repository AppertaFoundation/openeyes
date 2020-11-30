<?php

/**
 * This is the model class for table "patient_statistic".
 *
 * The followings are the available columns in table 'patient_statistic':
 * @property string $patient_id
 * @property string $stat_type_mnem
 * @property int $eye_id
 * @property bool $process_datapoints
 * @property float $min_adjusted
 * @property float $max_adjusted
 * @property float $gradient
 * @property float $y_intercept
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $created_user
 * @property Eye $eye
 * @property User $last_modified_user
 * @property Patient $patient
 * @property PatientStatisticType $stat_type
 * @property PatientStatisticDatapoint[] $datapoints
 */
class PatientStatistic extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'patient_statistic';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('patient_id, stat_type_mnem, eye_id', 'required'),
            array('process_datapoints', 'boolean'),
            array('min_adjusted, max_adjusted, gradient, y_intercept', 'numerical'),
            array('patient_id, eye_id', 'length', 'max' => 11),
            array('stat_type_mnem, last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            array(
                'patient_id, stat_type_mnem, eye_id, process_datapoints, min_adjusted, max_adjusted, gradient, y_intercept, last_modified_user_id, last_modified_date, created_user_id, created_date',
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
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'last_modified_user' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
            'stat_type' => array(self::BELONGS_TO, 'PatientStatisticType', 'stat_type_mnem'),
            'datapoints' => array(
                self::HAS_MANY,
                'PatientStatisticDatapoint',
                array('patient_id', 'stat_type_mnem', 'eye_id')
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'patient_id' => 'Patient',
            'stat_type_mnem' => 'Statistic Type',
            'eye_id' => 'Eye',
            'process_datapoints' => 'Process Datapoints?',
            'min_adjusted' => 'Min Adjusted',
            'max_adjusted' => 'Max Adjusted',
            'gradient' => 'Gradient',
            'y_intercept' => 'Y Intercept',
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

        $criteria->compare('patient_id', $this->patient_id, true);
        $criteria->compare('stat_type_mnem', $this->stat_type_mnem, true);
        $criteria->compare('eye_id', $this->eye_id, true);
        $criteria->compare('process_datapoints', $this->process_datapoints);
        $criteria->compare('min_adjusted', $this->min_adjusted);
        $criteria->compare('max_adjusted', $this->max_adjusted);
        $criteria->compare('gradient', $this->gradient);
        $criteria->compare('y_intercept', $this->y_intercept);
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
     * Gets the datapoint with the highest X value for the statistic for the specified date range (if any).
     * @param $from_date
     * @param $to_date
     *
     * @return PatientStatisticDatapoint|CActiveRecord|null
     */
    public function getMaxDatapoint($from_date = null, $to_date = null)
    {
        $max_id = null;
        $largest_x = -999;

        foreach ($this->datapoints as $datapoint) {
            if ($datapoint->x_value > $largest_x) {
                if ((!$from_date || $datapoint->event->event_date > $from_date) && (!$to_date || $datapoint->event->event_date < $to_date)) {
                    $largest_x = $datapoint->x_value;
                    $max_id = $datapoint->id;
                }
            }
        }
        return PatientStatisticDatapoint::model()->findByPk($max_id);
    }

    /**
     * Gets the datapoint with the lowest X value for the statistic for the specified date range (if any).
     * @param $from_date
     * @param $to_date
     *
     * @return PatientStatisticDatapoint|CActiveRecord|null
     */
    public function getMinDatapoint($from_date = null, $to_date = null)
    {
        $min_id = null;
        $smallest_x = 999;

        foreach ($this->datapoints as $datapoint) {
            if ($datapoint->x_value < $smallest_x) {
                if ((!$from_date || $datapoint->event->event_date > $from_date) && (!$to_date || $datapoint->event->event_date < $to_date)) {
                    $smallest_x = $datapoint->x_value;
                    $min_id = $datapoint->id;
                }
            }
        }
        return PatientStatisticDatapoint::model()->findByPk($min_id);
    }

    /**
     * Gets the x-value for the modelled Y value using a linear formula (x = (y - b)/m).
     * @param $y float
     * @return float
     */
    public function getXForLinearY(float $y)
    {
        $gradient = $this->gradient;
        $y_intercept = $this->y_intercept;

        return ($y - $y_intercept) / $gradient;
    }

    /**
     * Gets the modelled y-value for the specified X-value using a linear formula (y = mx + b).
     * @param $x float
     * @return float
     */
    public function getLinearYForX(float $x)
    {
        $gradient = $this->gradient;
        $y_intercept = $this->y_intercept;

        return $gradient * $x + $y_intercept;
    }
}
