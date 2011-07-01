<?php

/**
 * This is the model class for table "diagnosis".
 *
 * The followings are the available columns in table 'diagnosis':
 * @property string $id
 * @property string $patient_id
 * @property string $user_id
 * @property string $disorder_id
 * @property string $created_on
 * @property integer $location
 *
 * The followings are the available model relations:
 * @property Patient $patient
 * @property User $user
 * @property Disorder $disorder
 */
class Diagnosis extends BaseActiveRecord
{
	public $common_ophthalmic_disorder_id;
	public $common_systemic_disorder_id;

	const LOCATION_LEFT = 0;
	const LOCATION_RIGHT = 1;
	const LOCATION_BILATERAL = 2;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Diagnosis the static model class
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
		return 'diagnosis';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('patient_id, user_id, disorder_id, created_on', 'required'),
			array('location', 'numerical', 'integerOnly'=>true),
			array('patient_id, user_id, disorder_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, patient_id, user_id, disorder_id, created_on, location', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'disorder' => array(self::BELONGS_TO, 'Disorder', 'disorder_id'),
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
			'user_id' => 'User',
			'disorder_id' => 'Disorder',
			'created_on' => 'created_on',
			'ophthalmic' => 'Ophthalmic',
			'systemic' => 'Systemic',
			'location' => 'Location',
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
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('disorder_id',$this->disorder_id,true);
		$criteria->compare('created_on',$this->created_on,true);
		$criteria->compare('location',$this->location);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	public function getLocationOptions()
	{
		return array(
			self::LOCATION_LEFT => 'Left',
			self::LOCATION_RIGHT => 'Right',
			self::LOCATION_BILATERAL => 'Bilateral',
		);
	}

	public function getLocationText()
	{
		$locationOptions = $this->getLocationOptions();

		return $locationOptions[$this->location];
	}

	public function getCommonOphthalmicDisorderOptions($firm)
	{
		$options = Yii::app()->db->createCommand()
			->select('t.id AS did, t.term')
			->from('disorder t')
			->join('common_ophthalmic_disorder', 't.id = common_ophthalmic_disorder.disorder_id')
			->where('common_ophthalmic_disorder.specialty_id = :specialty_id',
				array(':specialty_id' => $firm->serviceSpecialtyAssignment->specialty_id))
			->queryAll();

		$result = array();
		foreach ($options as $value) {
			$result[$value['did']] = $value['term'];
		}

		return $result;
	}

	public function getCommonSystemicDisorderOptions()
	{
		$options = Yii::app()->db->createCommand()
			->select('t.id AS did, t.term')
			->from('disorder t')
			->join('common_systemic_disorder', 't.id = common_systemic_disorder.disorder_id')
			->queryAll();

		$result = array();
		foreach ($options as $value) {
			$result[$value['did']] = $value['term'];
		}

		return $result;
	}

	public function getDisorderTerm()
	{
		return $this->disorder->term;
	}
}