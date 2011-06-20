<?php

/**
 * This is the model class for table "booking".
 *
 * The followings are the available columns in table 'booking':
 * @property string $id
 * @property string $element_operation_id
 * @property string $session_id
 * @property integer $display_order
 *
 * The followings are the available model relations:
 * @property ElementOperation $elementOperation
 * @property Session $session
 */
class Booking extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Booking the static model class
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
		return 'booking';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('element_operation_id, session_id', 'required'),
			array('display_order', 'numerical', 'integerOnly'=>true),
			array('element_operation_id, session_id', 'length', 'max'=>10),
			array('element_operation_id, session_id, display_order', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, element_operation_id, session_id, display_order', 'safe', 'on'=>'search'),
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
			'elementOperation' => array(self::BELONGS_TO, 'ElementOperation', 'element_operation_id'),
			'session' => array(self::BELONGS_TO, 'Session', 'session_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'element_operation_id' => 'Element Operation',
			'session_id' => 'Session',
			'display_order' => 'Display Order',
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
		$criteria->compare('element_operation_id',$this->element_operation_id,true);
		$criteria->compare('session_id',$this->session_id,true);
		$criteria->compare('display_order',$this->display_order);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}