<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

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
class CancellationReason extends BaseActiveRecord
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
