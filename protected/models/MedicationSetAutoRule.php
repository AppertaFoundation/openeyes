<?php

/**
 * This is the model class for table "medication_set_auto_rule".
 *
 * The followings are the available columns in table 'medication_set_auto_rule':
 * @property integer $id
 * @property integer $medication_set_id
 * @property string $name
 * @property integer $hidden
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property MedicationSet $medicationSet
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property MedicationSetAutoRuleAttribute[] $medicationSetAutoRuleAttributes
 * @property MedicationSetAutoRuleMedication[] $medicationSetAutoRuleMedications
 * @property MedicationSetAutoRuleSetMembership[] $medicationSetAutoRuleSetMemberships
 */
class MedicationSetAutoRule extends BaseActiveRecordVersioned
{
	/*
	 * These variables stand for temporary storage only
	 */
	public $attrs = [];
	public $sets = [];
	public $meds = [];

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'medication_set_auto_rule';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('medication_set_id, name', 'required'),
			array('medication_set_id, hidden', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
			array('last_modified_date, created_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, medication_set_id, name, hidden, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
			'medicationSet' => array(self::BELONGS_TO, MedicationSet::class, 'medication_set_id'),
			'createdUser' => array(self::BELONGS_TO, User::class, 'created_user_id'),
			'lastModifiedUser' => array(self::BELONGS_TO, User::class, 'last_modified_user_id'),
			'medicationSetAutoRuleAttributes' => array(self::HAS_MANY, MedicationSetAutoRuleAttribute::class, 'medication_set_auto_rule_id'),
			'medicationSetAutoRuleMedications' => array(self::HAS_MANY, MedicationSetAutoRuleMedication::class, 'medication_set_auto_rule_id'),
			'medicationSetAutoRuleSetMemberships' => array(self::HAS_MANY, MedicationSetAutoRuleSetMembership::class, 'medication_set_auto_rule_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'medication_set_id' => 'Medication Set',
			'name' => 'Name',
			'hidden' => 'Set is a',
			'last_modified_user_id' => 'Last Modified User',
			'last_modified_date' => 'Last Modified Date',
			'created_user_id' => 'Created User',
			'created_date' => 'Created Date',
			'medicationSet.name' => 'Set name'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('medication_set_id',$this->medication_set_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('hidden',$this->hidden);
		$criteria->compare('last_modified_user_id',$this->last_modified_user_id,true);
		$criteria->compare('last_modified_date',$this->last_modified_date,true);
		$criteria->compare('created_user_id',$this->created_user_id,true);
		$criteria->compare('created_date',$this->created_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MedicationSetAutoRule the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function beforeValidate()
	{
		foreach ($this->attrs as $attr) {
			if($attr['medication_attribute_option_id'] == '') {
				$this->addError('attribute', 'Attribute value must be set');
			}
		}

		if(empty($this->medication_set_id)) {
			$medicationSet = new MedicationSet();
			$medicationSet->name = $this->name;
			$medicationSet->save();
			$this->medication_set_id = $medicationSet->id;
			$this->medicationSet = $medicationSet;
		}

		return parent::beforeValidate();
	}

	public function afterSave()
	{
		$this->_saveAttrs();
		$this->_saveSets();
		$this->_saveMeds();
		$this->medicationSet->hidden = $this->hidden;
		$this->medicationSet->save();
		return parent::afterSave();
	}

	private function _saveAttrs()
	{
		$existing_ids = array_map(function($e){ return $e->id; }, $this->medicationSetAutoRuleAttributes);
		$updated_ids = array();
		foreach ($this->attrs as $attr) {
			if($attr['id'] == -1) {
				$attrib = new MedicationSetAutoRuleAttribute();
			}
			else {
				$attrib = MedicationSetAutoRuleAttribute::model()->findByPk($attr['id']);
			}

			$attrib->medication_attribute_option_id = $attr['medication_attribute_option_id'];
			$attrib->medication_set_auto_rule_id = $this->id;
			$attrib->save();
			$updated_ids[] = $attrib->id;
		}
		$ids_to_delete = array_diff($existing_ids, $updated_ids);
		if(!empty($ids_to_delete)) {
			Yii::app()->db->createCommand("DELETE FROM ".MedicationSetAutoRuleAttribute::model()->tableName()." WHERE id IN (".implode(",", $ids_to_delete).");")->execute();
		}
	}

	private function _saveSets()
	{
		$existing_ids = array_map(function($e){ return $e->id; }, $this->medicationSetAutoRuleSetMemberships);
		$updated_ids = array();
		foreach ($this->sets as $set) {
			if($set['id'] == -1) {
				$set_m = new MedicationSetAutoRuleSetMembership();
			}
			else {
				$set_m = MedicationSetAutoRuleSetMembership::model()->findByPk($set['id']);
			}

			$set_m->medication_set_id = $set['medication_set_id'];
			$set_m->medication_set_auto_rule_id = $this->id;
			$set_m->save();
			$updated_ids[] = $set_m->id;
		}
		$ids_to_delete = array_diff($existing_ids, $updated_ids);
		if(!empty($ids_to_delete)) {
			Yii::app()->db->createCommand("DELETE FROM ".MedicationSetAutoRuleSetMembership::model()->tableName()." WHERE id IN (".implode(",", $ids_to_delete).");")->execute();
		}
	}

	private function _saveMeds()
	{
		$existing_ids = array_map(function($e){ return $e->id; }, $this->medicationSetAutoRuleMedications);
		$updated_ids = array();
		foreach ($this->meds as $med) {
			if($med['id'] == -1) {
				$med_m = new MedicationSetAutoRuleMedication();
			}
			else {
				$med_m = MedicationSetAutoRuleMedication::model()->findByPk($med['id']);
			}

			$med_m->medication_id = $med['medication_id'];
			$med_m->include_children = $med['include_children'];
			$med_m->include_parent = $med['include_parent'];
			$med_m->medication_set_auto_rule_id = $this->id;
			$med_m->save();
			$updated_ids[] = $med_m->id;
		}
		$ids_to_delete = array_diff($existing_ids, $updated_ids);
		if(!empty($ids_to_delete)) {
			Yii::app()->db->createCommand("DELETE FROM ".MedicationSetAutoRuleMedication::model()->tableName()." WHERE id IN (".implode(",", $ids_to_delete).");")->execute();
		}
	}
}
