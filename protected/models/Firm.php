<?php

/**
 * This is the model class for table "firm".
 *
 * The followings are the available columns in table 'firm':
 * @property string $id
 * @property string $service_specialty_assignment_id
 * @property string $pas_code
 * @property string $name
 *
 * The followings are the available model relations:
 * @property ServiceSpecialtyAssignment $serviceSpecialtyAssignment
 * @property FirmUserAssignment[] $firmUserAssignments
 * @property LetterPhrase[] $letterPhrases
 */
class Firm extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Firm the static model class
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
		return 'firm';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('service_specialty_assignment_id, name', 'required'),
			array('service_specialty_assignment_id', 'length', 'max'=>10),
			array('pas_code', 'length', 'max'=>4),
			array('name', 'length', 'max'=>40),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, service_specialty_assignment_id, pas_code, name', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		// @todo - how to express the link to the firm's one consultant
		return array(
			'serviceSpecialtyAssignment' => array(self::BELONGS_TO, 'ServiceSpecialtyAssignment', 'service_specialty_assignment_id'),
			'firmUserAssignments' => array(self::HAS_MANY, 'FirmUserAssignment', 'firm_id'),
			'letterPhrases' => array(self::HAS_MANY, 'LetterPhrase', 'firm_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'service_specialty_assignment_id' => 'Service Specialty Assignment',
			'pas_code' => 'Pas Code',
			'name' => 'Name',
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
		$criteria->compare('service_specialty_assignment_id',$this->service_specialty_assignment_id,true);
		$criteria->compare('pas_code',$this->pas_code,true);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns an array of the service_specialty names - the service name plus the specialty name.
	 */
	public function getServiceSpecialtyOptions()
	{
		$sql = 'SELECT
					service_specialty_assignment.id,
					service.name AS service_name,
					specialty.name AS specialty_name
				FROM
					service,
					specialty,
					service_specialty_assignment
				WHERE
					service.id = service_specialty_assignment.service_id
				AND
					specialty.id = service_specialty_assignment.specialty_id
				ORDER BY
					service.name,
					specialty.name
				';

		$connection = Yii::app()->db;
		$command = $connection->createCommand($sql);
		$results = $command->queryAll();

		$select = array();

		foreach ($results as $result) {
			$select[$result['id']] = $result['service_name'] . ' - ' . $result['specialty_name'];
		}

		return $select;
	}

	public function getServiceText()
	{
		return $this->serviceSpecialtyAssignment->service->name;
	}

	public function getSpecialtyText()
	{
		return $this->serviceSpecialtyAssignment->specialty->name;
	}
	
	/**
	 * Fetch an array of service IDs and names
	 * @return array
	 */
	public function getList()
	{
		$list = Firm::model()->findAll();
		$result = array();
		
		foreach ($list as $firm) {
			$result[$firm->id] = $firm->name;
		}
		
		return $result;
	}
}
