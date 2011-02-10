<?php

/**
 * This is the model class for table "firm".
 *
 * The followings are the available columns in table 'firm':
 * @property string $id
 * @property string $service_id
 * @property string $specialty_id
 * @property string $pas_code
 * @property string $name
 * @property string $contact_id
 *
 * The followings are the available model relations:
 * @property Service $service
 * @property Specialty $specialty
 * @property Contact $contact
 */
class Firm extends CActiveRecord
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
			array('service_id, specialty_id, name, contact_id', 'required'),
			array('service_id, specialty_id, contact_id', 'length', 'max'=>10),
			array('pas_code', 'length', 'max'=>4),
			array('name', 'length', 'max'=>40),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, service_id, specialty_id, pas_code, name, contact_id', 'safe', 'on'=>'search'),
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
			'service' => array(self::BELONGS_TO, 'Service', 'service_id'),
			'specialty' => array(self::BELONGS_TO, 'Specialty', 'specialty_id'),
			'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'service_id' => 'Service',
			'specialty_id' => 'Specialty',
			'pas_code' => 'Pas Code',
			'name' => 'Name',
			'contact_id' => 'Contact',
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
		$criteria->compare('service_id',$this->service_id,true);
		$criteria->compare('specialty_id',$this->specialty_id,true);
		$criteria->compare('pas_code',$this->pas_code,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('contact_id',$this->contact_id,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	public function getContactOptions()
	{
		return CHtml::listData(Contact::Model()->findAll(), 'id', 'nick_name');
	}

	public function getSpecialtyOptions()
	{
		return CHtml::listData(Specialty::Model()->findAll(), 'id', 'name');
	}

	public function getServiceOptions()
	{
		return CHtml::listData(Service::Model()->findAll(), 'id', 'name');
	}
}