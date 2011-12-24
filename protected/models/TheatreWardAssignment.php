<?php

/**
 * This is the model class for table "theatre_ward_assignment".
 *
 * The followings are the available columns in table 'theatre_ward_assignment':
 * @property string $id
 * @property string $theatre_id
 * @property string $ward_id
 *
 * The followings are the available model relations:
 * @property Ward $ward
 * @property Theatre $theatre
 */
class TheatreWardAssignment extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return TheatreWardAssignment the static model class
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
		return 'theatre_ward_assignment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('theatre_id, ward_id', 'required'),
			array('theatre_id, ward_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, theatre_id, ward_id', 'safe', 'on'=>'search'),
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
			'ward' => array(self::BELONGS_TO, 'Ward', 'ward_id'),
			'theatre' => array(self::BELONGS_TO, 'Theatre', 'theatre_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'theatre_id' => 'Theatre',
			'ward_id' => 'Ward',
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
		$criteria->compare('theatre_id',$this->theatre_id,true);
		$criteria->compare('ward_id',$this->ward_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
