<?php

/**
 * This is the model class for table "theatre".
 *
 * The followings are the available columns in table 'theatre':
 * @property string $id
 * @property string $name
 * @property string $site_id
 *
 * The followings are the available model relations:
 * @property Sequence[] $sequences
 * @property Site $site
 */
class Theatre extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Theatre the static model class
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
		return 'theatre';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('site_id', 'required'),
			array('name', 'length', 'max'=>255),
			array('site_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, site_id', 'safe', 'on'=>'search'),
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
			'sequences' => array(self::HAS_MANY, 'Sequence', 'theatre_id'),
			'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'site_id' => 'Site',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('site_id',$this->site_id,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	public static function getDateFilterOptions()
	{
		return array(
			'today' => 'Today',
			'week' => 'This week',
			'month' => 'This month',
			'custom' => 'or from'
		);
	}
}