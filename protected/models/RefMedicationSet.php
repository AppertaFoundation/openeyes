<?php

/**
 * This is the model class for table "ref_medication_set".
 *
 * The followings are the available columns in table 'ref_medication_set':
 * @property integer $id
 * @property integer $ref_medication_id
 * @property integer $ref_set_id
 * @property integer $default_form
 * @property double $default_dose
 * @property integer $default_route
 * @property integer $default_frequency
 * @property string $default_dose_unit_term
 * @property integer $default_duration
 * @property string $deleted_date
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property RefMedicationFrequency $defaultFrequency
 * @property RefMedicationForm $defaultForm
 * @property RefMedicationRoute $defaultRoute
 * @property RefMedication $refMedication
 * @property RefSet $refSet
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property RefMedicationSetTaper[] $tapers
 * @property RefMedicationDuration $defaultDuration
 */
class RefMedicationSet extends BaseActiveRecordVersioned
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ref_medication_set';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ref_medication_id, ref_set_id', 'required'),
			array('ref_medication_id, ref_set_id, default_form, default_route, default_frequency, default_duration', 'numerical', 'integerOnly'=>true),
			array('default_dose', 'numerical'),
			array('default_dose_unit_term', 'length', 'max'=>45),
			array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
			array('deleted_date, last_modified_date, created_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, ref_medication_id, ref_set_id, default_form, default_dose, default_route, default_frequency, default_dose_unit_term, deleted_date, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
			'defaultFrequency' => array(self::BELONGS_TO, 'RefMedicationFrequency', 'default_frequency'),
			'defaultForm' => array(self::BELONGS_TO, 'RefMedicationForm', 'default_form'),
			'defaultRoute' => array(self::BELONGS_TO, 'RefMedicationRoute', 'default_route'),
			'refMedication' => array(self::BELONGS_TO, 'RefMedication', 'ref_medication_id'),
			'refSet' => array(self::BELONGS_TO, 'RefSet', 'ref_set_id'),
			'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'tapers' => array(self::HAS_MANY, RefMedicationSetTaper::class, 'ref_medication_set_id'),
            'defaultDuration' => array(self::BELONGS_TO, RefMedicationDuration::class, 'default_duration')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'ref_medication_id' => 'Ref Medication',
			'ref_set_id' => 'Ref Set',
			'default_form' => 'Default Form',
			'default_dose' => 'Default Dose',
			'default_route' => 'Default Route',
			'default_frequency' => 'Default Frequency',
			'default_dose_unit_term' => 'Default Dose Unit Term',
			'deleted_date' => 'Deleted Date',
			'last_modified_user_id' => 'Last Modified User',
			'last_modified_date' => 'Last Modified Date',
			'created_user_id' => 'Created User',
			'created_date' => 'Created Date',
            'tapers' => 'Tapers'
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
		$criteria->compare('ref_medication_id',$this->ref_medication_id);
		$criteria->compare('ref_set_id',$this->ref_set_id);
		$criteria->compare('default_form',$this->default_form);
		$criteria->compare('default_dose',$this->default_dose);
		$criteria->compare('default_route',$this->default_route);
		$criteria->compare('default_frequency',$this->default_frequency);
		$criteria->compare('default_dose_unit_term',$this->default_dose_unit_term,true);
		$criteria->compare('deleted_date',$this->deleted_date,true);
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
	 * @return RefMedicationSet the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
