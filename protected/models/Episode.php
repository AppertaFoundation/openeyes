<?php

/**
 * This is the model class for table "episode".
 *
 * The followings are the available columns in table 'episode':
 * @property string $id
 * @property string $patient_id
 * @property string $firm_id
 * @property string $startdate
 * @property string $enddate
 *
 * The followings are the available model relations:
 * @property Patient $patient
 * @property Firm $firm
 * @property Event[] $events
 */
class Episode extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Episode the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'episode';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('patient_id', 'required'),
			array('patient_id, firm_id', 'length', 'max'=>10),
			array('enddate', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, patient_id, firm_id, startdate, enddate', 'safe', 'on'=>'search'),
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
			'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
			'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
			'events' => array(self::HAS_MANY, 'Event', 'episode_id'),
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
			'firm_id' => 'Firm',
			'startdate' => 'Startdate',
			'enddate' => 'Enddate',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('patient_id',$this->patient_id,true);
		$criteria->compare('firm_id',$this->firm_id,true);
		$criteria->compare('startdate',$this->startdate,true);
		$criteria->compare('enddate',$this->enddate,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the episode for a patient and specialty if there is one.
	 */
	public static function modelBySpecialtyIdAndPatientId($specialtyId, $patientId)
	{
		$sql = 'SELECT
					episode.id AS id
				FROM
					episode,
					firm,
					service_specialty_assignment
				WHERE
					patient_id = :patient_id
				AND
					firm_id = firm.id
				AND
					service_specialty_assignment_id = service_specialty_assignment.id
				AND
					specialty_id = :specialty_id
				AND
					enddate IS NULL
				';

		$connection = Yii::app()->db;
		$command = $connection->createCommand($sql);
		$command->bindParam(':patient_id', $patientId);
		$command->bindParam(':specialty_id', $specialtyId);

		$results = $command->queryAll();

		if (count($results)) {
			return Episode::model()->findByPk($results[0]['id']);
		}

		return false;
	}
}