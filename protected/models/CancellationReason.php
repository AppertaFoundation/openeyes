<?php

/**
 * This is the model class for table "cancellation_reason".
 *
 * The followings are the available columns in table 'cancellation_reason':
 * @property string $id
 * @property string $text
 * @property string $parent_id
 * @property integer $list_no
 *
 * The followings are the available model relations:
 * @property CancelledBooking[] $cancelledBookings
 * @property CancelledOperation[] $cancelledOperations
 */
class CancellationReason extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return CancellationReason the static model class
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
		return 'cancellation_reason';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('list_no', 'required'),
			array('list_no', 'numerical', 'integerOnly'=>true),
			array('text', 'length', 'max'=>255),
			array('parent_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, text, parent_id, list_no', 'safe', 'on'=>'search'),
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
			'cancelledBookings' => array(self::HAS_MANY, 'CancelledBooking', 'cancelled_reason_id'),
			'cancelledOperations' => array(self::HAS_MANY, 'CancelledOperation', 'cancelled_reason_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'text' => 'Text',
			'parent_id' => 'Parent',
			'list_no' => 'List No',
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
		$criteria->compare('text',$this->text,true);
		$criteria->compare('parent_id',$this->parent_id,true);
		$criteria->compare('list_no',$this->list_no);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	public static function getReasonsByListNumber($listNo = 2)
	{
		$options = Yii::app()->db->createCommand()
			->select('t.id, t.text')
			->from('cancellation_reason t')
			->where('list_no = :no', array(':no'=>$listNo))
			->order('text ASC')
			->queryAll();

		$result = array();
		foreach ($options as $value) {
			$result[$value['id']] = $value['text'];
		}

		return $result;
	}
}