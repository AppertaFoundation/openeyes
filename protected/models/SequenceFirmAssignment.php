<?php

/**
 * This is the model class for table "sequence_firm_assignment".
 *
 * The followings are the available columns in table 'sequence_firm_assignment':
 * @property string $id
 * @property string $sequence_id
 * @property string $firm_id
 *
 * The followings are the available model relations:
 * @property Sequence $sequence
 * @property Firm $firm
 */
class SequenceFirmAssignment extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return SequenceFirmAssignment the static model class
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
		return 'sequence_firm_assignment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sequence_id, firm_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sequence_id, firm_id', 'safe', 'on'=>'search'),
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
			'sequence' => array(self::BELONGS_TO, 'Sequence', 'sequence_id'),
			'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'sequence_id' => 'Sequence',
			'firm_id' => 'Firm',
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
		$criteria->compare('sequence_id',$this->sequence_id,true);
		$criteria->compare('firm_id',$this->firm_id,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	public function getFirmOptions()
	{
		$options = Yii::app()->db->createCommand()
			->select('t.id, t.name, s.name AS sname')
			->from('firm t')
			->join('service_specialty_assignment ssa', 'ssa.id = t.service_specialty_assignment_id')
			->join('service s', 's.id = ssa.service_id')
			->order('t.name')
			->queryAll();

		$result = array();
		foreach ($options as $value) {
			$result[$value['id']] = $value['name'] . ' (' . $value['sname'] . ')';
		}

		return $result;

	}
}