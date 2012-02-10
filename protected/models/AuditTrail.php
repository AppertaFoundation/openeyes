<?php

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
