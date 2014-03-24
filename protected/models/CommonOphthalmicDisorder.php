<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "common_ophthalmic_disorder".
 *
 * The followings are the available columns in table 'common_ophthalmic_disorder':
 * @property integer $id
 * @property integer $disorder_id
 * @property integer $subspecialty_id
 *
 * The followings are the available model relations:
 * @property Disorder $disorder
 * @property Subspecialty $subspecialty
 */
class CommonOphthalmicDisorder extends BaseActiveRecordVersioned
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return CommonOphthalmicDisorder the static model class
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
		return 'common_ophthalmic_disorder';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('disorder_id, subspecialty_id', 'required'),
			array('disorder_id, subspecialty_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, disorder_id, subspecialty_id', 'safe', 'on'=>'search'),
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
			'disorder' => array(self::BELONGS_TO, 'Disorder', 'disorder_id', 'condition' => 'disorder.active = 1'),
			'subspecialty' => array(self::BELONGS_TO, 'Subspecialty', 'subspecialty_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'disorder_id' => 'Disorder',
			'subspecialty_id' => 'Subspecialty',
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
		$criteria->compare('disorder_id',$this->disorder_id,true);
		$criteria->compare('subspecialty_id',$this->subspecialty_id,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	public static function getList($firm)
	{
		if (empty($firm)) {
			throw new CException('Firm is required.');
		}
		if ($firm->serviceSubspecialtyAssignment) {
			$ss_id = $firm->serviceSubspecialtyAssignment->subspecialty_id;
			$disorders = Disorder::model()->active()->findAll(array(
					'condition' => 'cad.subspecialty_id = :subspecialty_id',
					'join' => 'JOIN common_ophthalmic_disorder cad ON cad.disorder_id = t.id JOIN specialty ON specialty_id = specialty.id AND specialty.code = :ophcode',
					'order' => 'term',
					'params' => array(':subspecialty_id' => $ss_id, ':ophcode' => 130),
			));
			return CHtml::listData($disorders, 'id', 'term');
		}
		return array();
	}

}
