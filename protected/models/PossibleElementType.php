<?php

/**
 * This is the model class for table "possible_element_type".
 *
 * The followings are the available columns in table 'possible_element_type':
 * @property string $id
 * @property string $event_type_id
 * @property string $element_type_id
 * @property string $view_number
 * @property integer $display_order
 *
 * The followings are the available model relations:
 * @property DefaultElementType[] $defaultElementTypes
 * @property EventType $eventType
 * @property ElementType $elementType
 * @property PossibleElementTypeSpecialtyAssignment[] $PossibleElementTypeSpecialtyAssignments
 */
class PossibleElementType extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return PossibleElementType the static model class
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
		return 'possible_element_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('event_type_id, element_type_id, view_number, display_order', 'required'),
			array('display_order', 'numerical', 'integerOnly'=>true),
			array('event_type_id, element_type_id, view_number', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, event_type_id, element_type_id, view_number, display_order', 'safe', 'on'=>'search'),
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
			'defaultElementTypes' => array(self::HAS_MANY, 'DefaultElementType', 'possible_element_type_id'),
			'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
			'elementType' => array(self::BELONGS_TO, 'ElementType', 'element_type_id'),
			'PossibleElementTypeSpecialtyAssignments' => array(self::HAS_MANY, 'PossibleElementTypeSpecialtyAssignment', 'possible_element_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'event_type_id' => 'Event Type',
			'element_type_id' => 'Element Type',
			'view_number' => 'View Number',
			'display_order' => 'Display order',
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
		$criteria->compare('event_type_id',$this->event_type_id,true);
		$criteria->compare('element_type_id',$this->element_type_id,true);
		$criteria->compare('view_number',$this->view_number,true);
		$criteria->compare('display_order',$this->display_order);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}
