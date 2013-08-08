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
 * This is the model class for table "practice".
 *
 * The followings are the available columns in table 'practice':
 * @property integer $id
 * @property string $code
 * @property string $phone
 *
 * The followings are the available model relations:
 * @property Address $address
 * @property CommissioningBody[] $commissioningbodies
 */
class Practice extends BaseActiveRecord
{
	public $use_pas = TRUE;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Practice the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Suppress PAS integration
	 * @return Practice
	 */
	public function noPas()
	{
		// Clone to avoid singleton problems with use_pas flag
		$model = clone $this;
		$model->use_pas = FALSE;
		return $model;
	}

	public function behaviors()
	{
		return array(
			'ContactBehavior' => array(
				'class' => 'application.behaviors.ContactBehavior',
			),
		);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'practice';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('code', 'required'),
			array('phone, contact_id', 'safe'),
			array('id, code', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
			'commissioningbodies' => array(self::MANY_MANY, 'CommissioningBody', 'commissioning_body_practice_assignment(practice_id, commissioning_body_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'code' => 'Code',
			'phone' => 'Phone',
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
		$criteria->compare('code',$this->code,true);
		$criteria->compare('phone',$this->phone,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	* Pass through use_pas flag to allow pas supression
	* @see CActiveRecord::instantiate()
	*/
	protected function instantiate($attributes)
	{
			$model = parent::instantiate($attributes);
			$model->use_pas = $this->use_pas;
			return $model;
	}

	/**
	 * Raise event to allow external data sources to update practice
	 * @see CActiveRecord::afterFind()
	 */
	protected function afterFind()
	{
		parent::afterFind();
		Yii::app()->event->dispatch('practice_after_find', array('practice' => $this));
	}

	/**
	 * get the CommissioningBody of the CommissioningBodyType $type
	 * currently assumes there would only ever be one commissioning body of a given type
	 * 
	 * @param CommissioningBodyType $type
	 * @return CommissioningBody
	 */
	public function getCommissioningBodyOfType($type)
	{
		foreach ($this->commissioningbodies as $body) {
			if ($body->type->id == $type->id) {
				return $body;
			}
		}
	}
	
	public function getCorrespondenceName()
	{
		return Gp::UNKNOWN_NAME;
	}
	
	public function getSalutationName()
	{
		return Gp::UNKNOWN_SALUTATION;
	}
}
