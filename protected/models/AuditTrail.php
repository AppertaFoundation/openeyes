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
 * This is the model class for table "tbl_audit_trail".
 * 
 * The followings are the available columns in table 'tbl_audit_trail':
 * @property integer $id
 * @property string $new_value
 * @property string $old_value
 * @property string $action
 * @property string $model
 * @property string $field
 * @property string $stamp
 * @property integer $user_id
 * @property integer $model_id
 */
class AuditTrail extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @return AuditTrail the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'tbl_audit_trail';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array('action, model, field, stamp, model_id', 'required'),
			array('action, model, field', 'length', 'max' => 255),
			array('model_id, user_id', 'numerical', 'integerOnly' => true),
			array('id, new_value, old_value, action, model, field, stamp, user_id, model_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'old_value' => 'Old Value',
			'new_value' => 'New Value',
			'action' => 'Action',
			'model' => 'Model',
			'field' => 'Field',
			'stamp' => 'Stamp',
			'user_id' => 'User',
			'model_id' => 'Model',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($options = array()) {
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);
		$criteria->compare('old_value',$this->old_value,true);
		$criteria->compare('new_value',$this->new_value,true);
		$criteria->compare('action',$this->action,true);
		$criteria->compare('model',$this->model);
		$criteria->compare('field',$this->field,true);
		$criteria->compare('stamp',$this->stamp,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('model_id',$this->model_id);
		$criteria->mergeWith($this->getDbCriteria());
		return new CActiveDataProvider(
			get_class($this),
			array_merge(
				array(
					'criteria'=>$criteria,
				),
				$options
			)
		);
	}

}
